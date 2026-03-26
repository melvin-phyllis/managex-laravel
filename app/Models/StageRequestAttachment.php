<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageRequestAttachment extends Model
{
    protected $fillable = [
        'stage_request_id',
        'original_name',
        'stored_name',
        'disk',
        'path',
        'mime_type',
        'size',
    ];

    public function stageRequest(): BelongsTo
    {
        return $this->belongsTo(StageRequest::class);
    }
}

