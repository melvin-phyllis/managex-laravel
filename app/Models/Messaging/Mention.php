<?php

namespace App\Models\Messaging;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mention extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'type',
        'target_id',
    ];

    /**
     * Get the message
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the mentioned user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for user mentions
     */
    public function scopeUserMentions($query)
    {
        return $query->where('type', 'user');
    }

    /**
     * Scope for @all mentions
     */
    public function scopeAllMentions($query)
    {
        return $query->where('type', 'all');
    }
}
