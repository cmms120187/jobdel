<?php

namespace App\Http\Controllers;

use App\Models\Delegation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DelegationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Superuser can see all delegations
        if ($user->position && $user->position->name === 'Superuser') {
            $delegations = Delegation::with(['task', 'delegatedTo.position', 'delegatedBy.position'])
                ->latest()
                ->paginate(10);
        } else {
            $delegations = Delegation::with(['task', 'delegatedTo.position', 'delegatedBy.position'])
                ->where('delegated_to', $user->id)
                ->latest()
                ->paginate(10);
        }
        
        return view('delegations.index', compact('delegations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Task $task)
    {
        $this->authorize('update', $task);
        
        $user = Auth::user();
        $users = $user->getDelegatableUsers();
        return view('delegations.create', compact('task', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $user = Auth::user();
        $delegatableUserIds = $user->getDelegatableUsers()->pluck('id')->toArray();

        $validated = $request->validate([
            'delegated_to' => ['required', 'exists:users,id', function ($attribute, $value, $fail) use ($delegatableUserIds) {
                if (!in_array($value, $delegatableUserIds)) {
                    $fail('Anda tidak dapat mendelegasikan ke user tersebut berdasarkan hierarki posisi.');
                }
            }],
            'notes' => 'nullable|string',
        ]);

        Delegation::create([
            'task_id' => $task->id,
            'delegated_to' => $validated['delegated_to'],
            'delegated_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Delegasi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Delegation $delegation)
    {
        $delegation->load([
            'task.room',
            'task.creator.position',
            'task.taskItems.assignedUser.position',
            'task.taskItems.updates.updater.position',
            'delegatedTo.position', 
            'delegatedBy.position', 
            'progressUpdates.updater.position'
        ]);
        return view('delegations.show', compact('delegation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delegation $delegation)
    {
        $this->authorize('update', $delegation);
        
        $delegation->load(['task', 'delegatedTo.position', 'delegatedBy.position']);
        return view('delegations.edit', compact('delegation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delegation $delegation)
    {
        $this->authorize('update', $delegation);
        
        if ($request->has('action')) {
            if ($request->action === 'accept') {
                $delegation->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);
                // Sinkronkan status Task: jika masih pending, ubah ke in_progress
                if ($delegation->task->status === 'pending') {
                    $delegation->task->update(['status' => 'in_progress']);
                }
                return redirect()->back()->with('success', 'Delegasi diterima.');
            } elseif ($request->action === 'reject') {
                $delegation->update([
                    'status' => 'rejected',
                ]);
                return redirect()->back()->with('success', 'Delegasi ditolak.');
            } elseif ($request->action === 'complete') {
                $delegation->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'progress_percentage' => 100,
                ]);
                $delegation->task->update(['status' => 'completed']);
                return redirect()->back()->with('success', 'Delegasi diselesaikan.');
            }
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,accepted,rejected,in_progress,completed',
        ]);

        $delegation->update($validated);

        // Sinkronkan status Task dengan status delegasi
        $task = $delegation->task;
        if (in_array($validated['status'], ['accepted', 'in_progress']) && $task->status === 'pending') {
            $task->update(['status' => 'in_progress']);
        } elseif ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $task->update(['status' => 'completed']);
        }

        // Get page from session for redirect
        $page = session('delegations_page', 1);
        
        return redirect()->route('delegations.index', ['page' => $page])
            ->with('success', 'Delegasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delegation $delegation)
    {
        $this->authorize('delete', $delegation);
        
        $delegation->delete();

        // Get page from session for redirect
        $page = session('delegations_page', 1);
        
        return redirect()->route('delegations.index', ['page' => $page])
            ->with('success', 'Delegasi berhasil dihapus.');
    }
}
