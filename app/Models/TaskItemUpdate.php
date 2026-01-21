<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskItemUpdate extends Model
{
    protected $fillable = [
        'task_item_id',
        'updated_by',
        'old_progress_percentage',
        'new_progress_percentage',
        'old_status',
        'new_status',
        'notes',
        'attachments',
        'update_date',
        'time_from',
        'time_to',
    ];

    protected $casts = [
        'attachments' => 'array',
        'old_progress_percentage' => 'integer',
        'new_progress_percentage' => 'integer',
        'update_date' => 'date',
    ];

    /**
     * Get the start time for duration calculation.
     * If time_from is set, use it. Otherwise, use delegation accepted_at or fallback to 08:00.
     */
    public function getEffectiveTimeFromAttribute(): ?string
    {
        // If time_from is explicitly set, use it
        if ($this->time_from) {
            return $this->time_from;
        }

        // Otherwise, try to get from delegation accepted_at
        $taskItem = $this->taskItem;
        if ($taskItem && $taskItem->assigned_to) {
            // Load task with delegations if not already loaded
            if (!$taskItem->relationLoaded('task')) {
                $taskItem->load('task.delegations');
            }

            // Get the delegation for this task and assigned user
            $delegation = $taskItem->task->delegations
                ->where('delegated_to', $taskItem->assigned_to)
                ->whereNotNull('accepted_at')
                ->first();

            if ($delegation && $delegation->accepted_at) {
                // Convert to Asia/Jakarta timezone (use copy to avoid mutating original)
                $acceptedAt = $delegation->accepted_at->copy()->setTimezone('Asia/Jakarta');
                $updateDate = \Carbon\Carbon::parse($this->update_date)->setTimezone('Asia/Jakarta');
                
                // Use accepted_at if it's on the same date as update_date, or if update_date is after accepted_at
                if ($acceptedAt->format('Y-m-d') === $updateDate->format('Y-m-d') || 
                    $updateDate->greaterThanOrEqualTo($acceptedAt->copy()->startOfDay())) {
                    return $acceptedAt->format('H:i');
                }
            }
        }

        // Fallback: always return a default start time (08:00) for the update_date
        // This ensures we always have a start time for calculation, even for old data or without delegation
        return '08:00';
    }

    /**
     * Get the end time for duration calculation.
     * If time_to is set, use it. Otherwise, use task item completed_at or update created_at or fallback to 17:00.
     */
    public function getEffectiveTimeToAttribute(): ?string
    {
        // If time_to is explicitly set, use it
        if ($this->time_to) {
            return $this->time_to;
        }

        // Convert to Asia/Jakarta timezone
        $updateDate = \Carbon\Carbon::parse($this->update_date)->setTimezone('Asia/Jakarta');
        
        // Otherwise, try to get from task item completed_at
        $taskItem = $this->taskItem;
        if ($taskItem && $taskItem->completed_at) {
            $completedAt = $taskItem->completed_at->copy()->setTimezone('Asia/Jakarta');
            // Use completed_at if it's on the same date as update_date, or if update_date is before completed_at
            if ($completedAt->format('Y-m-d') === $updateDate->format('Y-m-d') || 
                $updateDate->lessThanOrEqualTo($completedAt->copy()->startOfDay())) {
                return $completedAt->format('H:i');
            }
        }

        // If not completed or completed on different date, use the update's created_at time
        if ($this->created_at) {
            $createdAt = $this->created_at->copy()->setTimezone('Asia/Jakarta');
            if ($createdAt->format('Y-m-d') === $updateDate->format('Y-m-d')) {
                return $createdAt->format('H:i');
            }
        }

        // Fallback: always return end of day (17:00) if update_date is today or in the past
        // This ensures we always have an end time for calculation, even for old data
        if ($updateDate->isToday() || $updateDate->isPast()) {
            return '17:00';
        }

        // If update_date is in the future, return null (shouldn't happen in normal cases)
        return '17:00';
    }

    /**
     * Calculate duration in minutes from time_from to time_to.
     * If time_from/time_to are not set, use delegation accepted_at and task item completed_at.
     */
    public function getDurationInMinutesAttribute(): ?int
    {
        $timeFrom = $this->effective_time_from;
        $timeTo = $this->effective_time_to;

        if (!$timeFrom || !$timeTo) {
            return null;
        }

        try {
            // Parse time strings (format H:i)
            $fromParts = explode(':', $timeFrom);
            $toParts = explode(':', $timeTo);
            
            if (count($fromParts) < 2 || count($toParts) < 2) {
                return null;
            }

            $fromHour = (int)$fromParts[0];
            $fromMin = (int)$fromParts[1];
            $toHour = (int)$toParts[0];
            $toMin = (int)$toParts[1];

            $fromMinutes = ($fromHour * 60) + $fromMin;
            $toMinutes = ($toHour * 60) + $toMin;

            // If time_to is before time_from, assume it's next day (add 24 hours)
            if ($toMinutes < $fromMinutes) {
                $toMinutes += (24 * 60);
            }

            return $toMinutes - $fromMinutes;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get formatted duration string (e.g., "2h 30m" or "45m")
     */
    public function getFormattedDurationAttribute(): ?string
    {
        $minutes = $this->duration_in_minutes;
        if ($minutes === null) {
            return null;
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $hours . 'j ' . $mins . 'm';
        }
        return $mins . 'm';
    }

    public function taskItem(): BelongsTo
    {
        return $this->belongsTo(TaskItem::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
