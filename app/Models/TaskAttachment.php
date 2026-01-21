<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file extension from original name
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute(): bool
    {
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'image/bmp', 'image/svg+xml'];
        return in_array(strtolower($this->file_type), array_map('strtolower', $imageTypes));
    }

    /**
     * Check if file can be previewed (images and PDFs)
     */
    public function getCanPreviewAttribute(): bool
    {
        return $this->is_image || str_contains(strtolower($this->file_type), 'pdf');
    }

    /**
     * Format file size for display
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
