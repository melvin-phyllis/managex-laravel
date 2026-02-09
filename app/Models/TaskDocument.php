<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDocument extends Model
{
    protected $fillable = [
        'task_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' Mo';
        }

        return round($bytes / 1024, 1).' Ko';
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }
}
