<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use App\Models\Room;
use App\Models\TaskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        // Get page from request, session, or default to 1
        $page = $request->get('page', session('tasks_page', 1));
        
        // Save current page to session
        session(['tasks_page' => $page]);
        
        // Get filter user_id (default to current user if not provided)
        $filterUserId = $request->get('user_id');
        
        // If no filter provided, default to current user
        if ($filterUserId === null || $filterUserId === '') {
            $filterUserId = $user->id;
        }
        
        // Get subordinates for filter dropdown
        $subordinates = $user->getSubordinatesIncludingSelf();
        
        // Validate filter user_id - user must be able to see that user (subordinate or self)
        // If filterUserId is 'all', allow it (will show all subordinates)
        if ($filterUserId !== 'all') {
            $allowedUserIds = $subordinates->pluck('id')->toArray();
            if (!in_array($filterUserId, $allowedUserIds)) {
                $filterUserId = $user->id; // Default to current user if not allowed
            }
        }
        
        // Build query
        $query = Task::with(['room', 'creator', 'delegations.delegatedTo']);
        
        if ($filterUserId === 'all') {
            // Show all tasks for subordinates (including self)
            $subordinateIds = $subordinates->pluck('id')->toArray();
            $query->where(function($q) use ($subordinateIds) {
                $q->whereIn('created_by', $subordinateIds)
                  ->orWhereHas('delegations', function($dq) use ($subordinateIds) {
                      $dq->whereIn('delegated_to', $subordinateIds);
                  });
            });
        } else {
            // Filter by specific user (default is current user)
            $query->where(function($q) use ($filterUserId) {
                $q->where('created_by', $filterUserId)
                  ->orWhereHas('delegations', function($dq) use ($filterUserId) {
                      $dq->where('delegated_to', $filterUserId);
                  });
            });
        }
        
        $tasks = $query->latest()->paginate(10, ['*'], 'page', $page);
        
        // Get all users for filter dropdown (subordinates + self for regular users, all for superuser)
        $filterUsers = $isSuperuser 
            ? User::with('position')->orderBy('name')->get()
            : $subordinates;
        
        return view('tasks.index', compact('tasks', 'filterUsers', 'filterUserId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $users = $user->getDelegatableUsers();
        $superiors = $user->getSuperiors();
        $rooms = Room::orderBy('room')->get();
        return view('tasks.create', compact('users', 'superiors', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $delegatableUserIds = $user->getDelegatableUsers()->pluck('id')->toArray();
        // Always include current user in delegatable users
        if (!in_array($user->id, $delegatableUserIds)) {
            $delegatableUserIds[] = $user->id;
        }

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'project_code' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'type' => 'nullable|in:JOB DESCRIPTION,PROJECT,TASK,OTHER',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'requested_by' => 'nullable|exists:users,id',
            'add_request' => 'nullable|string|max:255',
            // Allow all file types (no mimes restriction)
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:51200', // Max 50MB per file
            'approve_level' => 'nullable|integer|min:0',
            'delegated_to' => 'required|array|min:1',
            'delegated_to.*' => ['required', 'exists:users,id', function ($attribute, $value, $fail) use ($delegatableUserIds) {
                if (!in_array($value, $delegatableUserIds)) {
                    $fail('Anda tidak dapat mendelegasikan ke user tersebut berdasarkan hierarki posisi.');
                }
            }],
            'delegation_notes' => 'nullable|string',
        ]);

        $task = Task::create([
            'room_id' => $validated['room_id'] ?? null,
            'project_code' => $validated['project_code'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'type' => $validated['type'] ?? 'TASK',
            'due_date' => $validated['due_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'requested_by' => $validated['requested_by'] ?? null,
            'add_request' => $validated['add_request'] ?? null,
            'approve_level' => $validated['approve_level'] ?? 0,
            'created_by' => Auth::id(),
        ]);

        // Handle file attachments uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePath = $file->store('task-attachments', 'local');
                
                TaskAttachment::create([
                    'task_id' => $task->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        // Create history record for task creation
        TaskHistory::create([
            'task_id' => $task->id,
            'updated_by' => Auth::id(),
            'action' => 'created',
            'new_values' => $task->toArray(),
            'notes' => 'Task created',
        ]);

        // Create delegations for all selected users
        foreach ($validated['delegated_to'] as $userId) {
            $task->delegations()->create([
                'delegated_to' => $userId,
                'delegated_by' => Auth::id(),
                'notes' => $validated['delegation_notes'] ?? null,
                'status' => 'pending',
            ]);
        }

        // Get page from session for redirect
        $page = session('tasks_page', 1);
        
        return redirect()->route('tasks.index', ['page' => $page])
            ->with('success', 'Task berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load([
            'room', 
            'creator.position', 
            'requester.position', 
            'delegations.delegatedTo.position', 
            'delegations.delegatedBy.position', 
            'delegations.progressUpdates.updater.position',
            'histories.updater.position',
            'taskItems.assignedUser.position',
            'taskItems.updates.updater.position'
        ]);
        
        // Get users for assigning task items - filter by delegations in this task
        $currentUser = Auth::user();
        
        // Get all users delegated in this task
        $delegatedUserIds = $task->delegations->pluck('delegated_to')->unique()->toArray();
        
        // Always include current user
        if (!in_array($currentUser->id, $delegatedUserIds)) {
            $delegatedUserIds[] = $currentUser->id;
        }
        
        // Include task creator
        if (!in_array($task->created_by, $delegatedUserIds)) {
            $delegatedUserIds[] = $task->created_by;
        }
        
        // Get users with their positions
        $users = User::whereIn('id', $delegatedUserIds)
            ->with('position')
            ->orderBy('name')
            ->get();
        
        // Load attachments
        $task->load('attachments.uploader');
        
        return view('tasks.show', compact('task', 'users', 'currentUser'));
    }

    /**
     * Securely download a task attachment.
     * fileKey is either "file_support_1" or "file_support_2".
     */
    public function downloadFile(Task $task, $fileKey)
    {
        $user = Auth::user();
        
        // Check authorization: Only creator or users in delegation can access
        $isCreator = $task->created_by === $user->id;
        $isInDelegation = $task->delegations()->where('delegated_to', $user->id)->exists();
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        if (!$isCreator && !$isInDelegation && !$isSuperuser) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses file ini.');
        }

        // Handle old file_support_1 and file_support_2 for backward compatibility
        if (in_array($fileKey, ['file_support_1', 'file_support_2'])) {
            $filePath = $task->{$fileKey};
            if (!$filePath) {
                abort(404);
            }

            // Ensure file exists on the private disk
            if (!Storage::disk('local')->exists($filePath)) {
                abort(404);
            }

            // Stream download with original filename if possible
            $basename = basename($filePath);
            $mime = Storage::disk('local')->mimeType($filePath) ?: 'application/octet-stream';

            return Storage::disk('local')->download($filePath, $basename, ['Content-Type' => $mime]);
        }

        // Handle new attachment system
        $attachment = TaskAttachment::where('task_id', $task->id)
            ->where('id', $fileKey)
            ->first();

        if (!$attachment) {
            abort(404);
        }

        // Ensure file exists on the private disk
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        // Stream download with original filename
        $mime = Storage::disk('local')->mimeType($attachment->file_path) ?: $attachment->file_type ?: 'application/octet-stream';

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name, ['Content-Type' => $mime]);
    }

    /**
     * Upload a new attachment to a task.
     */
    public function uploadAttachment(Request $request, Task $task)
    {
        $policy = new \App\Policies\TaskAttachmentPolicy();
        if (!$policy->create(Auth::user(), $task)) {
            abort(403, 'Anda tidak memiliki izin untuk mengupload file.');
        }

        $validator = Validator::make($request->all(), [
            'files.*' => 'required|file|max:10240', // Max 10MB per file
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $uploadedFiles = [];
        $user = Auth::user();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $filePath = $file->store('task_attachments', 'local');
                $fileType = $file->getMimeType();
                $fileSize = $file->getSize();

                $attachment = TaskAttachment::create([
                    'task_id' => $task->id,
                    'original_name' => $originalName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'uploaded_by' => $user->id,
                    'description' => $request->input('description'),
                ]);

                $uploadedFiles[] = $attachment;
            }
        }

        return redirect()->back()
            ->with('success', count($uploadedFiles) . ' file berhasil diupload.');
    }

    /**
     * Delete a task attachment.
     */
    public function deleteAttachment(Task $task, TaskAttachment $attachment)
    {
        // Ensure attachment belongs to this task
        if ($attachment->task_id !== $task->id) {
            abort(404);
        }

        $policy = new \App\Policies\TaskAttachmentPolicy();
        if (!$policy->delete(Auth::user(), $attachment)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus file ini.');
        }

        // Delete file from storage
        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()->back()
            ->with('success', 'File berhasil dihapus.');
    }

    /**
     * Preview a task attachment (for images and PDFs).
     */
    public function previewFile(Task $task, $fileKey)
    {
        $user = Auth::user();
        
        // Check authorization: Only creator or users in delegation can access
        $isCreator = $task->created_by === $user->id;
        $isInDelegation = $task->delegations()->where('delegated_to', $user->id)->exists();
        $isRequester = $task->requested_by === $user->id;
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        if (!$isCreator && !$isInDelegation && !$isRequester && !$isSuperuser) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses file ini.');
        }

        // Handle old file_support_1 and file_support_2 for backward compatibility
        if (in_array($fileKey, ['file_support_1', 'file_support_2'])) {
            $filePath = $task->{$fileKey};
            if (!$filePath) {
                abort(404);
            }

            if (!Storage::disk('local')->exists($filePath)) {
                abort(404);
            }

            $mime = Storage::disk('local')->mimeType($filePath) ?: 'application/octet-stream';
            return response()->file(Storage::disk('local')->path($filePath), ['Content-Type' => $mime]);
        }

        // Handle new attachment system
        $attachment = TaskAttachment::where('task_id', $task->id)
            ->where('id', $fileKey)
            ->first();

        if (!$attachment) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        $mime = Storage::disk('local')->mimeType($attachment->file_path) ?: $attachment->file_type ?: 'application/octet-stream';
        return response()->file(Storage::disk('local')->path($attachment->file_path), ['Content-Type' => $mime]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $user = Auth::user();
        $users = $user->getDelegatableUsers();
        $superiors = $user->getSuperiors();
        $rooms = Room::orderBy('room')->get();
        // Load delegations to check if task already has delegations
        $task->load('delegations');
        // Load attachments
        $task->load('attachments.uploader');
        
        return view('tasks.edit', compact('task', 'users', 'superiors', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'project_code' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'type' => 'nullable|in:JOB DESCRIPTION,PROJECT,TASK,OTHER',
            // Status tidak bisa diubah manual, diambil dari progress detail pekerjaan (task items)
            // 'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'requested_by' => 'nullable|exists:users,id',
            'add_request' => 'nullable|string|max:255',
            // Allow all file types (no mimes restriction)
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:51200', // Max 50MB per file
            'approve_level' => 'nullable|integer|min:0',
            'delegated_to' => 'nullable|array',
            'delegated_to.*' => 'exists:users,id',
            'delegation_notes' => 'nullable|string',
        ]);

        // Store old values for history (before update)
        $oldValues = $task->getAttributes();
        
        // Status tidak bisa diubah manual di form, diambil dari progress detail pekerjaan
        // Hapus status dari validated jika ada, karena akan dihitung dari task items
        unset($validated['status']);
        
        // Update task (tanpa status)
        $task->update($validated);
        
        // Handle delegations if task doesn't have any yet (for duplicated tasks)
        if ($request->has('delegated_to') && is_array($request->delegated_to) && count($request->delegated_to) > 0) {
            // Only create delegations if task doesn't have any yet
            if ($task->delegations->isEmpty()) {
                $user = Auth::user();
                $delegatableUserIds = $user->getDelegatableUsers()->pluck('id')->toArray();
                
                // Validate that all selected users are delegatable
                $validUserIds = array_intersect($request->delegated_to, $delegatableUserIds);
                
                if (count($validUserIds) > 0) {
                    foreach ($validUserIds as $userId) {
                        $task->delegations()->create([
                            'delegated_to' => $userId,
                            'delegated_by' => Auth::id(),
                            'notes' => $validated['delegation_notes'] ?? null,
                            'status' => 'pending',
                        ]);
                    }
                }
            }
        }
        
        // Update status task berdasarkan overall progress dari detail pekerjaan (task items)
        $this->updateTaskStatusFromTaskItems($task);
        
        // Refresh to get new values
        $task->refresh();
        $newValues = $task->getAttributes();

        // Get changed fields
        $changedFields = [];
        $fieldLabels = [
            'room_id' => 'Room',
            'project_code' => 'Project Code',
            'title' => 'Title',
            'description' => 'Description',
            'priority' => 'Priority',
            'type' => 'Type',
            'status' => 'Status',
            'due_date' => 'Due Date',
            'start_date' => 'Start Date',
            'requested_by' => 'User Request',
            'add_request' => 'Add Request',
            'file_support_1' => 'File Support 1',
            'file_support_2' => 'File Support 2',
            'approve_level' => 'Approve Level',
        ];

        foreach ($validated as $key => $newValue) {
            // Skip fields that are not in the task model (except file uploads)
            if (!array_key_exists($key, $oldValues) && !in_array($key, ['file_support_1', 'file_support_2'])) {
                continue;
            }
            
            $oldValue = $oldValues[$key] ?? null;
            
            // Handle date comparison
            if (in_array($key, ['due_date', 'start_date'])) {
                if ($oldValue) {
                    $oldValue = is_string($oldValue) ? $oldValue : ($oldValue instanceof \DateTime ? $oldValue->format('Y-m-d') : null);
                }
                if ($newValue) {
                    $newValue = is_string($newValue) ? $newValue : ($newValue instanceof \DateTime ? $newValue->format('Y-m-d') : null);
                }
            }
            
            // Handle room_id comparison (convert to string for comparison)
            if ($key === 'room_id') {
                $oldValue = $oldValue ? (string)$oldValue : null;
                $newValue = $newValue ? (string)$newValue : null;
            }
            
            // Handle requested_by comparison
            if ($key === 'requested_by') {
                $oldValue = $oldValue ? (string)$oldValue : null;
                $newValue = $newValue ? (string)$newValue : null;
            }
            
            // Compare values (handle null properly)
            $oldValueForCompare = $oldValue ?? '';
            $newValueForCompare = $newValue ?? '';
            
            if ($oldValueForCompare != $newValueForCompare) {
                $changedFields[$key] = [
                    'field' => $fieldLabels[$key] ?? $key,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // Create history record if there are changes
        if (!empty($changedFields)) {
            try {
                // Store old and new values with field names as keys
                $oldValuesStored = [];
                $newValuesStored = [];
                foreach ($changedFields as $field => $data) {
                    $oldValuesStored[$field] = $data['old'];
                    $newValuesStored[$field] = $data['new'];
                }
                
                TaskHistory::create([
                    'task_id' => $task->id,
                    'updated_by' => Auth::id(),
                    'action' => 'updated',
                    'old_values' => $oldValuesStored,
                    'new_values' => $newValuesStored,
                    'notes' => 'Task updated: ' . implode(', ', array_column($changedFields, 'field')),
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the update
                \Log::error('Failed to create task history: ' . $e->getMessage());
            }
        }

        // Get page from session for redirect
        $page = session('tasks_page', 1);
        
        return redirect()->route('tasks.index', ['page' => $page])
            ->with('success', 'Task berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        // Store task data before deletion for history
        $taskData = $task->toArray();
        $taskId = $task->id;
        
        // Create history record before deletion
        try {
            TaskHistory::create([
                'task_id' => $taskId,
                'updated_by' => Auth::id(),
                'action' => 'deleted',
                'old_values' => $taskData,
                'new_values' => null,
                'notes' => 'Task deleted',
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the deletion
            \Log::error('Failed to create task history for deletion: ' . $e->getMessage());
        }
        
        $task->delete();

        // Get page from session for redirect
        $page = session('tasks_page', 1);
        
        return redirect()->route('tasks.index', ['page' => $page])
            ->with('success', 'Task berhasil dihapus.');
    }

    /**
     * Duplicate a task and redirect to edit page.
     * Only duplicates task utama, NOT delegations and NOT task items.
     * Delegations direset agar bisa dipilih user baru yang berbeda.
     */
    public function duplicate(Task $task)
    {
        // Check if user can view the task (to duplicate)
        $this->authorize('view', $task);

        // Create new task with same data (except id, timestamps, and status)
        $newTask = $task->replicate();
        $newTask->created_by = Auth::id();
        $newTask->status = 'pending'; // Reset status to pending
        $newTask->save();

        // Delegations TIDAK diduplikasi (direset)
        // User akan memilih delegasi baru yang berbeda melalui form edit
        // Ini berguna untuk task yang sama di bulan berikutnya yang dialihkan ke personil/user lain

        // Task items juga TIDAK diduplikasi
        // Detail pekerjaan perlu dibuat baru sesuai kebutuhan

        // Task history TIDAK diduplikasi
        // Task yang di-duplikat adalah task baru yang dikerjakan ulang di hari yang lain
        // Jadi tidak perlu membawa history dari task sebelumnya

        // Redirect to edit page of the new task
        return redirect()->route('tasks.edit', $newTask)
            ->with('success', 'Task berhasil diduplikasi. Delegasi direset - silakan pilih delegasi baru. Detail pekerjaan tidak diduplikasi - silakan buat detail pekerjaan baru sesuai kebutuhan.');
    }

    /**
     * Update task status based on overall progress from task items
     */
    private function updateTaskStatusFromTaskItems(Task $task)
    {
        // Get overall progress from task items
        $overallProgress = $task->overall_progress;
        
        // Update task status based on overall progress
        if ($overallProgress >= 100) {
            // All task items completed
            if ($task->status !== 'completed') {
                $task->update(['status' => 'completed']);
            }
        } elseif ($overallProgress > 0) {
            // Has some progress
            if ($task->status === 'pending') {
                $task->update(['status' => 'in_progress']);
            }
        } else {
            // No progress yet
            if ($task->status !== 'pending' && $task->status !== 'cancelled') {
                // Only change to pending if not cancelled
                // If task has no task items, keep current status
                if ($task->taskItems->isNotEmpty()) {
                    $task->update(['status' => 'pending']);
                }
            }
        }
    }
}
