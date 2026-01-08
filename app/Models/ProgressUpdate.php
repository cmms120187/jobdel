<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressUpdate extends Model
{
    protected $fillable = [
        'delegation_id',
        'updated_by',
        'progress_percentage',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function delegation(): BelongsTo
    {
        return $this->belongsTo(Delegation::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
