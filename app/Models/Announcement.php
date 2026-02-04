<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'target_type',
        'department_id',
        'position_id',
        'target_user_ids',
        'is_active',
        'is_pinned',
        'publish_at',
        'start_date',
        'end_date',
        'requires_acknowledgment',
        'attachments',
        'created_by',
        'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'requires_acknowledgment' => 'boolean',
        'publish_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'target_user_ids' => 'array',
        'attachments' => 'array',
    ];

    // ============ RELATIONS ============

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    // ============ SCOPES ============

    /**
     * Annonces publiées et visibles maintenant
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', today());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', today());
            });
    }

    /**
     * Annonces pour un utilisateur spécifique
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            // Toutes
            $q->where('target_type', 'all')
            // Ou son département
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('target_type', 'department')
                        ->where('department_id', $user->department_id);
                })
            // Ou son poste
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('target_type', 'position')
                        ->where('position_id', $user->position_id);
                })
            // Ou ciblé spécifiquement
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('target_type', 'custom')
                        ->whereJsonContains('target_user_ids', $user->id);
                });
        });
    }

    /**
     * Non lues par un utilisateur
     */
    public function scopeUnreadBy($query, User $user)
    {
        return $query->whereDoesntHave('reads', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Triées par priorité (critiques d'abord)
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'critical', 'high', 'normal')")
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');
    }

    // ============ HELPERS ============

    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    public function isAcknowledgedBy(User $user): bool
    {
        return $this->reads()
            ->where('user_id', $user->id)
            ->whereNotNull('acknowledged_at')
            ->exists();
    }

    public function markAsReadBy(User $user): AnnouncementRead
    {
        return $this->reads()->firstOrCreate(
            ['user_id' => $user->id],
            ['read_at' => now()]
        );
    }

    public function acknowledgeBy(User $user): void
    {
        $this->reads()->updateOrCreate(
            ['user_id' => $user->id],
            ['read_at' => now(), 'acknowledged_at' => now()]
        );
    }

    // ============ ACCESSORS ============

    public function getReadCountAttribute(): int
    {
        return $this->reads()->count();
    }

    public function getAcknowledgedCountAttribute(): int
    {
        return $this->reads()->whereNotNull('acknowledged_at')->count();
    }

    public function getTargetUsersCountAttribute(): int
    {
        return match ($this->target_type) {
            'all' => User::where('role', 'employee')->count(),
            'department' => User::where('role', 'employee')
                ->where('department_id', $this->department_id)->count(),
            'position' => User::where('role', 'employee')
                ->where('position_id', $this->position_id)->count(),
            'custom' => count($this->target_user_ids ?? []),
            default => 0,
        };
    }

    public function getReadPercentageAttribute(): float
    {
        $total = $this->target_users_count;

        return $total > 0 ? round(($this->read_count / $total) * 100, 1) : 0;
    }

    public function getAcknowledgedPercentageAttribute(): float
    {
        $total = $this->target_users_count;

        return $total > 0 ? round(($this->acknowledged_count / $total) * 100, 1) : 0;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'info' => 'blue',
            'success' => 'green',
            'warning' => 'amber',
            'urgent' => 'red',
            'event' => 'purple',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'info' => 'ℹ️',
            'success' => '✅',
            'warning' => '⚠️',
            'urgent' => '🚨',
            'event' => '📅',
            default => '📢',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'critical' => 'red',
            'high' => 'orange',
            'normal' => 'gray',
            default => 'gray',
        };
    }

    public function getTargetLabelAttribute(): string
    {
        return match ($this->target_type) {
            'all' => 'Tous les employés',
            'department' => $this->department?->name ?? 'Département',
            'position' => $this->position?->name ?? 'Poste',
            'custom' => count($this->target_user_ids ?? []).' utilisateur(s)',
            default => 'Inconnu',
        };
    }

    /**
     * Get target users for this announcement
     */
    public function getTargetUsers()
    {
        return match ($this->target_type) {
            'all' => User::where('role', 'employee')->get(),
            'department' => User::where('role', 'employee')
                ->where('department_id', $this->department_id)->get(),
            'position' => User::where('role', 'employee')
                ->where('position_id', $this->position_id)->get(),
            'custom' => User::whereIn('id', $this->target_user_ids ?? [])->get(),
            default => collect(),
        };
    }

    /**
     * Get users who haven't read this announcement
     */
    public function getUnreadUsers()
    {
        $readUserIds = $this->reads()->pluck('user_id')->toArray();

        return match ($this->target_type) {
            'all' => User::where('role', 'employee')
                ->whereNotIn('id', $readUserIds)->get(),
            'department' => User::where('role', 'employee')
                ->where('department_id', $this->department_id)
                ->whereNotIn('id', $readUserIds)->get(),
            'position' => User::where('role', 'employee')
                ->where('position_id', $this->position_id)
                ->whereNotIn('id', $readUserIds)->get(),
            'custom' => User::whereIn('id', $this->target_user_ids ?? [])
                ->whereNotIn('id', $readUserIds)->get(),
            default => collect(),
        };
    }
}
