<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Superuser can view all tasks
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        return $task->created_by === $user->id || 
               $task->delegations()->where('delegated_to', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Superuser (Administrator) can update all tasks
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        // Creator can update task
        if ($task->created_by === $user->id) {
            return true;
        }
        
        // Requester can update task
        if ($task->requested_by && $task->requested_by === $user->id) {
            return true;
        }
        
        // User yang didelegasikan TIDAK bisa edit task
        // Mereka hanya bisa update detail pekerjaan (task items) melalui progress update
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Superuser (Administrator) can delete all tasks
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        // Creator can delete task
        if ($task->created_by === $user->id) {
            return true;
        }
        
        // Requester can delete task
        if ($task->requested_by && $task->requested_by === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
