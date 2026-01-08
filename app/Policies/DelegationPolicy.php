<?php

namespace App\Policies;

use App\Models\Delegation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DelegationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Superuser can view all delegations
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        return true; // Users can view their own delegations
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Delegation $delegation): bool
    {
        // Superuser can view all delegations
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        return $delegation->delegated_to === $user->id || 
               $delegation->delegated_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Users can create delegations for their tasks
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Delegation $delegation): bool
    {
        // Superuser can update all delegations
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        return $delegation->delegated_to === $user->id || 
               $delegation->delegated_by === $user->id;
    }
    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Delegation $delegation): bool
    {
        // Superuser can delete all delegations
        if ($user->position && $user->position->name === 'Superuser') {
            return true;
        }
        
        return $delegation->delegated_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Delegation $delegation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Delegation $delegation): bool
    {
        return false;
    }
}
