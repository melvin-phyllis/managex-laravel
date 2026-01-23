<?php

namespace App\Models\Messaging;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatus extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'status',
        'custom_message',
        'until',
        'last_seen_at',
    ];

    protected $casts = [
        'until' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user is online
     */
    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    /**
     * Check if user is available (online or away)
     */
    public function isAvailable(): bool
    {
        return in_array($this->status, ['online', 'away']);
    }

    /**
     * Check if status is temporary
     */
    public function isTemporary(): bool
    {
        return $this->until && $this->until->isFuture();
    }

    /**
     * Get status label in French
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'online' => 'En ligne',
            'away' => 'Absent',
            'busy' => 'Occupé',
            'dnd' => 'Ne pas déranger',
            'offline' => 'Hors ligne',
            default => 'Inconnu',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'online' => 'green',
            'away' => 'yellow',
            'busy' => 'red',
            'dnd' => 'red',
            'offline' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Set user online
     */
    public function setOnline(): void
    {
        $this->update([
            'status' => 'online',
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Set user offline
     */
    public function setOffline(): void
    {
        $this->update([
            'status' => 'offline',
            'last_seen_at' => now(),
        ]);
    }
}
