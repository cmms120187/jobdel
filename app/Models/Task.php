<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'room_id',
        'project_code',
        'title',
        'description',
        'priority',
        'type',
        'status',
        'due_date',
        'start_date',
        'created_by',
        'requested_by',
        'add_request',
        'file_support_1',
        'file_support_2',
        'approve_level',
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function delegations(): HasMany
    {
        return $this->hasMany(Delegation::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TaskHistory::class)->latest();
    }

    public function taskItems(): HasMany
    {
        return $this->hasMany(TaskItem::class)->orderBy('order')->orderBy('id');
    }

    /**
     * Get overall progress percentage based on task items
     */
    public function getOverallProgressAttribute()
    {
        $items = $this->taskItems;
        
        if ($items->isEmpty()) {
            return 0;
        }

        $totalProgress = $items->sum('progress_percentage');
        return round($totalProgress / $items->count());
    }
}
