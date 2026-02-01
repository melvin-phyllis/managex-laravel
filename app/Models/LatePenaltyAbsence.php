<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LatePenaltyAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'absence_date',
        'total_expired_minutes',
        'source_presence_ids',
        'reason',
        'is_acknowledged',
        'acknowledged_at',
    ];

    protected $casts = [
        'absence_date' => 'date',
        'source_presence_ids' => 'array',
        'is_acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
        'total_expired_minutes' => 'integer',
    ];

    /**
     * Get the user that has this penalty absence
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source presences that caused this penalty
     */
    public function sourcePresences()
    {
        return Presence::whereIn('id', $this->source_presence_ids ?? [])->get();
    }

    /**
     * Format the total expired time
     */
    public function getFormattedExpiredTimeAttribute(): string
    {
        $hours = floor($this->total_expired_minutes / 60);
        $mins = $this->total_expired_minutes % 60;
        return $hours > 0 ? "{$hours}h" . ($mins > 0 ? sprintf('%02d', $mins) : '') : "{$mins} min";
    }

    /**
     * Scope for unacknowledged penalties
     */
    public function scopeUnacknowledged($query)
    {
        return $query->where('is_acknowledged', false);
    }

    /**
     * Mark as acknowledged
     */
    public function acknowledge(): void
    {
        $this->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);
    }
}
