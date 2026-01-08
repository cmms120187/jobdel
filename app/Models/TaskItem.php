<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskItem extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'status',
        'progress_percentage',
        'order',
        'assigned_to',
        'start_date',
        'start_time',
        'due_date',
        'due_time',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'progress_percentage' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Check if task item is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->status === 'completed') {
            return false;
        }

        $now = now();
        $dueDateTime = $this->due_date->copy();
        
        // If due_time is set, combine with due_date
        if ($this->due_time) {
            $timeParts = explode(':', $this->due_time);
            if (count($timeParts) >= 2) {
                $dueDateTime->setTime((int)$timeParts[0], (int)$timeParts[1], 0);
            } else {
                $dueDateTime->endOfDay();
            }
        } else {
            // Default to end of day if no time specified
            $dueDateTime->endOfDay();
        }

        // Check if past due datetime
        return $now->greaterThan($dueDateTime);
    }

    /**
     * Get formatted due datetime string
     */
    public function getDueDateTimeString(): ?string
    {
        if (!$this->due_date) {
            return null;
        }

        $dateStr = $this->due_date->format('d M Y');
        
        if ($this->due_time) {
            return $dateStr . ' ' . $this->due_time;
        }

        return $dateStr;
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TaskItemUpdate::class, 'task_item_id')->latest();
    }

    /**
     * Calculate overall progress of task based on task items
     */
    public static function calculateTaskProgress($taskId)
    {
        $items = self::where('task_id', $taskId)->get();
        
        if ($items->isEmpty()) {
            return 0;
        }

        $totalProgress = $items->sum('progress_percentage');
        return round($totalProgress / $items->count());
    }
}
