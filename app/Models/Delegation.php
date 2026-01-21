<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delegation extends Model
{
    protected $fillable = [
        'task_id',
        'delegated_to',
        'delegated_by',
        'notes',
        'status',
        'accepted_at',
        'completed_at',
        'progress_percentage',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get accepted_at in Asia/Jakarta timezone
     */
    public function getAcceptedAtJakartaAttribute()
    {
        if (!$this->accepted_at) {
            return null;
        }
        return $this->accepted_at->setTimezone('Asia/Jakarta');
    }

    /**
     * Get completed_at in Asia/Jakarta timezone
     */
    public function getCompletedAtJakartaAttribute()
    {
        if (!$this->completed_at) {
            return null;
        }
        return $this->completed_at->setTimezone('Asia/Jakarta');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function delegatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegated_to');
    }

    public function delegatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegated_by');
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class);
    }
}
