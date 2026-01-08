<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskItem;
use App\Models\TaskItemUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskItemController extends Controller
{
    /**
     * Display a listing of task items for a task.
     */
    public function index(Task $task)
    {
        $task->load(['taskItems.assignedUser', 'taskItems.updates.updater']);
        return response()->json($task->taskItems);
    }

    /**
     * Store a newly created task item.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'due_time' => 'nullable|date_format:H:i',
            'order' => 'nullable|integer|min:0',
        ]);

        $maxOrder = TaskItem::where('task_id', $task->id)->max('order') ?? 0;

        $taskItem = TaskItem::create([
            'task_id' => $task->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'due_time' => $validated['due_time'] ?? null,
            'order' => $validated['order'] ?? ($maxOrder + 1),
            'status' => 'pending',
            'progress_percentage' => 0,
        ]);

        // Create update record
        TaskItemUpdate::create([
            'task_item_id' => $taskItem->id,
            'updated_by' => Auth::id(),
            'update_date' => now()->toDateString(),
            'new_status' => 'pending',
            'new_progress_percentage' => 0,
            'notes' => 'Task item created',
        ]);

        // Don't update delegation status when creating task item
        // Delegation stays pending until the delegated user makes progress updates themselves
        // Only update delegation if the delegated user creates/updates task items assigned to them
        // This will be handled in updateProgress() method

        // Update task status based on overall progress from task items
        $this->updateTaskStatusFromTaskItems($task);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Detail pekerjaan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified task item.
     * Only accessible by Administrator (Superuser).
     */
    public function edit(Task $task, TaskItem $taskItem)
    {
        // Verify task item belongs to task
        if ($taskItem->task_id !== $task->id) {
            abort(404);
        }

        // Only Superuser (Administrator) can edit task items
        $user = Auth::user();
        if (!$user->position || $user->position->name !== 'Superuser') {
            abort(403, 'Hanya Administrator yang dapat mengedit detail pekerjaan.');
        }

        // Get users from task delegations for assign dropdown
        $delegationUserIds = $task->delegations->pluck('delegated_to')->unique()->toArray();
        
        // Build user IDs array
        $userIds = $delegationUserIds;
        $userIds[] = $task->created_by;
        if ($task->requested_by) {
            $userIds[] = $task->requested_by;
        }
        $userIds = array_unique($userIds);
        
        $users = User::whereIn('id', $userIds)
            ->with('position')
            ->orderBy('name')
            ->get();

        return view('task-items.edit', compact('task', 'taskItem', 'users'));
    }

    /**
     * Update the specified task item.
     * Only accessible by Administrator (Superuser).
     */
    public function update(Request $request, Task $task, TaskItem $taskItem)
    {
        // Verify task item belongs to task
        if ($taskItem->task_id !== $task->id) {
            abort(404);
        }

        // Only Superuser (Administrator) can update task items
        $user = Auth::user();
        if (!$user->position || $user->position->name !== 'Superuser') {
            abort(403, 'Hanya Administrator yang dapat mengedit detail pekerjaan.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed,cancelled',
            'progress_percentage' => 'sometimes|required|integer|min:0|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'due_time' => 'nullable|date_format:H:i',
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $taskItem->status;
        $oldProgress = $taskItem->progress_percentage;

        // Update task item
        $taskItem->update($validated);

        // Auto-update status based on progress
        if (isset($validated['progress_percentage'])) {
            if ($validated['progress_percentage'] >= 100 && $taskItem->status !== 'completed') {
                $taskItem->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                $validated['status'] = 'completed';
            } elseif ($validated['progress_percentage'] > 0 && $taskItem->status === 'pending') {
                $taskItem->update(['status' => 'in_progress']);
                $validated['status'] = 'in_progress';
            }
        }

        // Create update record if status or progress changed
        if (($oldStatus !== $taskItem->status) || ($oldProgress !== $taskItem->progress_percentage)) {
            TaskItemUpdate::create([
                'task_item_id' => $taskItem->id,
                'updated_by' => Auth::id(),
                'update_date' => now()->toDateString(),
                'old_status' => $oldStatus,
                'new_status' => $taskItem->status,
                'old_progress_percentage' => $oldProgress,
                'new_progress_percentage' => $taskItem->progress_percentage,
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Only update delegation if the update was made by the delegated user
            // and the task item is assigned to them
            if ($taskItem->assigned_to && $taskItem->assigned_to == Auth::id()) {
                $this->updateDelegationStatusBasedOnTaskItems($task);
            }
        }

        // Update task status based on overall progress from task items
        $this->updateTaskStatusFromTaskItems($task);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Detail pekerjaan berhasil diperbarui.');
    }

    /**
     * Update progress of a task item.
     */
    public function updateProgress(Request $request, Task $task, TaskItem $taskItem)
    {
        // Verify task item belongs to task
        if ($taskItem->task_id !== $task->id) {
            abort(404);
        }

        // Authorization: Only assigned user or Administrator (Superuser) can update progress
        $user = Auth::user();
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        $isAssignedUser = $taskItem->assigned_to == $user->id;
        
        if (!$isSuperuser && !$isAssignedUser) {
            abort(403, 'Anda tidak memiliki izin untuk mengupdate progress pekerjaan ini. Hanya user yang di-assign atau Administrator yang dapat melakukan update.');
        }

        // Allow multiple updates as long as progress is not 100%
        // If progress is 100%, only allow notes update (no progress change)
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'update_date' => 'required|date',
            'time_from' => 'nullable|date_format:H:i',
            'time_to' => 'nullable|date_format:H:i|after_or_equal:time_from',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB per photo
        ]);

        $oldStatus = $taskItem->status;
        $oldProgress = $taskItem->progress_percentage;
        $newProgress = $validated['progress_percentage'];
        
        // If task item is already 100%, don't allow reducing progress
        if ($oldProgress >= 100 && $newProgress < 100) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Tidak dapat mengurangi progress task yang sudah 100%.');
        }

        $newStatus = $oldStatus;

        // Auto-update status based on progress
        if ($newProgress >= 100) {
            $newStatus = 'completed';
        } elseif ($newProgress > 0 && $oldStatus === 'pending') {
            $newStatus = 'in_progress';
        } elseif ($newProgress == 0 && $oldStatus !== 'pending') {
            $newStatus = 'pending';
        }

        // Handle photo uploads
        $attachments = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $attachments[] = $photo->store('task-item-photos', 'public');
            }
        }

        // Update task item
        $taskItem->update([
            'progress_percentage' => $newProgress,
            'status' => $newStatus,
            'completed_at' => $newProgress >= 100 ? now() : null,
        ]);

        // Always create update record (even if progress doesn't change, for notes/photos)
        TaskItemUpdate::create([
            'task_item_id' => $taskItem->id,
            'updated_by' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_progress_percentage' => $oldProgress,
            'new_progress_percentage' => $newProgress,
            'notes' => $validated['notes'] ?? null,
            'attachments' => !empty($attachments) ? $attachments : null,
            'update_date' => $validated['update_date'],
            'time_from' => $validated['time_from'] ?? null,
            'time_to' => $validated['time_to'] ?? null,
        ]);

        // Update delegation status based on task items progress
        $this->updateDelegationStatusBasedOnTaskItems($task);
        
        // Update task status based on overall progress from task items
        $this->updateTaskStatusFromTaskItems($task);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Progress berhasil diperbarui.');
    }

    /**
     * Update delegation status based on task items progress
     * Only update delegation based on task items assigned to that user
     */
    private function updateDelegationStatusBasedOnTaskItems(Task $task)
    {
        $taskItems = $task->taskItems;
        
        if ($taskItems->isEmpty()) {
            return;
        }
        
        // Get all delegations for this task (except rejected)
        $delegations = $task->delegations()->where('status', '!=', 'rejected')->get();

        foreach ($delegations as $delegation) {
            // Get task items assigned to this delegation user only
            $userTaskItems = $taskItems->where('assigned_to', $delegation->delegated_to);
            
            // Calculate progress based on task items assigned to this user only
            $userProgress = 0;
            if ($userTaskItems->isNotEmpty()) {
                $totalProgress = $userTaskItems->sum('progress_percentage');
                $userProgress = round($totalProgress / $userTaskItems->count());
            }
            
            // Check if this user (yang didelegasikan) has made any progress updates on their assigned task items
            // Only update delegation if user yang didelegasikan sendiri yang update progress
            $hasUserProgressUpdates = false;
            if ($userTaskItems->isNotEmpty()) {
                // Check if the delegated user has made progress updates themselves
                $hasUserProgressUpdates = TaskItemUpdate::whereHas('taskItem', function($query) use ($task, $delegation) {
                    $query->where('task_id', $task->id)
                          ->where('assigned_to', $delegation->delegated_to);
                })
                ->where('updated_by', $delegation->delegated_to) // HARUS user yang didelegasikan yang update
                ->where(function($query) {
                    // Either progress actually increased (not just creation with 0%)
                    $query->where(function($q) {
                        $q->where('new_progress_percentage', '>', 0) // Progress must be > 0
                          ->where(function($subQ) {
                              // Old and new are different (actual progress change)
                              $subQ->whereColumn('old_progress_percentage', '!=', 'new_progress_percentage')
                                   // OR old is null and new > 0 (first update from creation)
                                   ->orWhere(function($orQ) {
                                       $orQ->whereNull('old_progress_percentage')
                                           ->where('new_progress_percentage', '>', 0);
                                   });
                          });
                    })
                    // OR user added notes (not just "Task item created")
                    ->orWhere(function($q) {
                        $q->whereNotNull('notes')
                          ->where('notes', '!=', 'Task item created')
                          ->where('notes', '!=', ''); // Not empty
                    })
                    // OR user uploaded photos (indicating active work)
                    ->orWhere(function($q) {
                        $q->whereNotNull('attachments')
                          ->where('attachments', '!=', '[]')
                          ->where('attachments', '!=', ''); // Has at least one attachment
                    });
                })
                ->exists();
            }
            
            // If user has no task items assigned to them OR hasn't made any progress updates themselves
            // Keep delegation as pending with 0% progress
            if ($userTaskItems->isEmpty() || !$hasUserProgressUpdates) {
                // User hasn't started working yet - keep delegation pending
                // Reset progress to 0 if user has no task items assigned
                if ($userTaskItems->isEmpty()) {
                    if ($delegation->progress_percentage > 0) {
                        $delegation->update([
                            'progress_percentage' => 0,
                        ]);
                    }
                } else {
                    // User has task items assigned but hasn't made progress updates themselves
                    // Maybe someone else created/assigned task items for them
                    // Reset progress to 0 and keep status as pending
                    if ($delegation->progress_percentage > 0) {
                        $delegation->update([
                            'progress_percentage' => 0,
                        ]);
                    }
                }
                // Keep status as pending - don't change anything
                continue;
            }
            
            // User has task items assigned AND has made progress updates themselves
            // Update delegation progress (only for this user's assigned task items)
            $delegation->update([
                'progress_percentage' => $userProgress,
            ]);

            // Update delegation status based on progress
            // Only update if user has actually started working (has progress updates they made)
            if ($userProgress >= 100) {
                // All task items assigned to this user are completed
                if ($delegation->status !== 'completed') {
                    $delegation->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
            } elseif ($userProgress > 0) {
                // User has progress and is actively working (they made the updates)
                if ($delegation->status === 'accepted') {
                    $delegation->update(['status' => 'in_progress']);
                } elseif ($delegation->status === 'pending') {
                    // User has started working (making progress updates themselves)
                    // Change to in_progress to indicate work has started
                    $delegation->update(['status' => 'in_progress']);
                }
            }
        }
        
        // Update task status based on all delegations
        // Task is completed only if all delegations are completed
        $activeDelegations = $delegations->whereIn('status', ['accepted', 'in_progress', 'completed']);
        
        if ($activeDelegations->isNotEmpty()) {
            $allCompleted = $activeDelegations->every(function($delegation) {
                return $delegation->status === 'completed' && $delegation->progress_percentage >= 100;
            });
            
            if ($allCompleted) {
                $task->update(['status' => 'completed']);
            } else {
                // Check if any delegation has progress
                $hasAnyProgress = $activeDelegations->some(function($delegation) {
                    return $delegation->progress_percentage > 0;
                });
                
                if ($hasAnyProgress && $task->status === 'pending') {
                    $task->update(['status' => 'in_progress']);
                }
            }
        }
    }

    /**
     * Remove the specified task item.
     */
    public function destroy(Task $task, TaskItem $taskItem)
    {
        // Verify task item belongs to task
        if ($taskItem->task_id !== $task->id) {
            abort(404);
        }

        $taskItem->delete();

        // Update task status based on overall progress from task items
        $this->updateTaskStatusFromTaskItems($task);

        // Update task status based on overall progress from task items
        $this->updateTaskStatusFromTaskItems($task);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Detail pekerjaan berhasil dihapus.');
    }

    /**
     * Show edit form for progress update (Administrator only)
     */
    public function editUpdate(Task $task, TaskItem $taskItem, $updateId)
    {
        // Get the update
        $update = TaskItemUpdate::findOrFail($updateId);
        
        // Verify update belongs to task item and task item belongs to task
        if ($update->task_item_id !== $taskItem->id || $taskItem->task_id !== $task->id) {
            abort(404);
        }

        // Only Superuser (Administrator) can edit progress updates
        $user = Auth::user();
        if (!$user->position || $user->position->name !== 'Superuser') {
            abort(403, 'Hanya Administrator yang dapat mengedit progress update.');
        }

        return view('task-items.edit-update', compact('task', 'taskItem', 'update'));
    }

    /**
     * Update progress update (Administrator only)
     */
    public function updateUpdate(Request $request, Task $task, TaskItem $taskItem, $updateId)
    {
        // Get the update
        $update = TaskItemUpdate::findOrFail($updateId);
        
        // Verify update belongs to task item and task item belongs to task
        if ($update->task_item_id !== $taskItem->id || $taskItem->task_id !== $task->id) {
            abort(404);
        }

        // Only Superuser (Administrator) can edit progress updates
        $user = Auth::user();
        if (!$user->position || $user->position->name !== 'Superuser') {
            abort(403, 'Hanya Administrator yang dapat mengedit progress update.');
        }

        $validated = $request->validate([
            'new_progress_percentage' => 'required|integer|min:0|max:100',
            'old_progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'update_date' => 'required|date',
            'time_from' => 'nullable|date_format:H:i',
            'time_to' => 'nullable|date_format:H:i|after_or_equal:time_from',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Handle photo uploads (append to existing attachments)
        $existingAttachments = $update->attachments ?? [];
        $newAttachments = [];
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $newAttachments[] = $photo->store('task-item-photos', 'public');
            }
        }

        // Merge existing and new attachments
        $allAttachments = array_merge($existingAttachments, $newAttachments);

        // Update the progress update record
        $update->update([
            'old_progress_percentage' => $validated['old_progress_percentage'],
            'new_progress_percentage' => $validated['new_progress_percentage'],
            'notes' => $validated['notes'] ?? null,
            'update_date' => $validated['update_date'],
            'time_from' => $validated['time_from'] ?? null,
            'time_to' => $validated['time_to'] ?? null,
            'attachments' => !empty($allAttachments) ? $allAttachments : null,
        ]);

        // Recalculate task item progress based on latest update
        $latestUpdate = $taskItem->updates()->latest()->first();
        if ($latestUpdate) {
            $taskItem->update([
                'progress_percentage' => $latestUpdate->new_progress_percentage ?? 0,
            ]);
        }

        // Update delegation and task status
        $this->updateDelegationStatusBasedOnTaskItems($task);
        $this->updateTaskStatusFromTaskItems($task);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Progress update berhasil diperbarui.');
    }

    /**
     * Update task status based on overall progress from task items
     */
    private function updateTaskStatusFromTaskItems(Task $task)
    {
        // Reload task with task items
        $task->load('taskItems');
        
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
