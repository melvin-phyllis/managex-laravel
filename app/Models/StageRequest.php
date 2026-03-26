<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StageRequest extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'school',
        'level',
        'desired_role',
        'message',
        'status',
        'admin_note',
        'interview_at',
        'interview_type',
        'score_technical',
        'score_communication',
        'score_motivation',
        'final_status',
        'retained_mail_sent_at',
        'source',
        'source_uid',
    ];

    protected $casts = [
        'interview_at' => 'datetime',
        'retained_mail_sent_at' => 'datetime',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(StageRequestAttachment::class);
    }
}

