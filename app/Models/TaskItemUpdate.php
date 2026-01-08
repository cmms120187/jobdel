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
        'time_from' => 'datetime:H:i',
        'time_to' => 'datetime:H:i',
    ];

    public function taskItem(): BelongsTo
    {
        return $this->belongsTo(TaskItem::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
