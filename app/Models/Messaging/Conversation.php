<?php

namespace App\Models\Messaging;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'description',
        'avatar',
        'created_by',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the creator of the conversation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all participants in this conversation
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * Get all active participants (not left)
     */
    public function activeParticipants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class)->whereNull('left_at');
    }

    /**
     * Get users in this conversation
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['role', 'nickname', 'joined_at', 'left_at', 'last_read_at', 'notifications', 'is_pinned', 'is_archived'])
            ->withTimestamps();
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the latest message
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Check if user is participant
     */
    public function hasParticipant(int $userId): bool
    {
        return $this->activeParticipants()->where('user_id', $userId)->exists();
    }

    /**
     * Check if user is admin of conversation
     */
    public function isAdmin(int $userId): bool
    {
        return $this->activeParticipants()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->exists();
    }

    /**
     * Get unread messages count for a user
     */
    public function unreadCountFor(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->count();
        }

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at)
            ->where('sender_id', '!=', $userId)
            ->count();
    }

    /**
     * Scope for direct conversations
     */
    public function scopeDirect($query)
    {
        return $query->where('type', 'direct');
    }

    /**
     * Scope for group conversations
     */
    public function scopeGroup($query)
    {
        return $query->where('type', 'group');
    }

    /**
     * Scope for channels
     */
    public function scopeChannel($query)
    {
        return $query->where('type', 'channel');
    }

    /**
     * Scope for user's conversations
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('activeParticipants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
