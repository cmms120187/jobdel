<?php

namespace App\Policies;

use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view attachments if they have access to the task
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskAttachment $taskAttachment): bool
    {
        $task = $taskAttachment->task;
        
        // Creator, delegated users, requester, or Superuser can view
        $isCreator = $task->created_by === $user->id;
        $isInDelegation = $task->delegations()->where('delegated_to', $user->id)->exists();
        $isRequester = $task->requested_by === $user->id;
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        return $isCreator || $isInDelegation || $isRequester || $isSuperuser;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $task): bool
    {
        // Creator, delegated users, requester, or Superuser can upload
        $isCreator = $task->created_by === $user->id;
        $isInDelegation = $task->delegations()->where('delegated_to', $user->id)->exists();
        $isRequester = $task->requested_by === $user->id;
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        return $isCreator || $isInDelegation || $isRequester || $isSuperuser;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskAttachment $taskAttachment): bool
    {
        // Only uploader, creator, requester, or Superuser can update
        $task = $taskAttachment->task;
        $isUploader = $taskAttachment->uploaded_by === $user->id;
        $isCreator = $task->created_by === $user->id;
        $isRequester = $task->requested_by === $user->id;
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        return $isUploader || $isCreator || $isRequester || $isSuperuser;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskAttachment $taskAttachment): bool
    {
        // Only uploader, creator, requester, or Superuser can delete
        $task = $taskAttachment->task;
        $isUploader = $taskAttachment->uploaded_by === $user->id;
        $isCreator = $task->created_by === $user->id;
        $isRequester = $task->requested_by === $user->id;
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        return $isUploader || $isCreator || $isRequester || $isSuperuser;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskAttachment $taskAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskAttachment $taskAttachment): bool
    {
        return $this->delete($user, $taskAttachment);
    }
}
