<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Position extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Cache keys
     */
    const CACHE_KEY_ACTIVE = 'positions.active';

    const CACHE_KEY_BY_DEPARTMENT = 'positions.department.';

    const CACHE_TTL = 3600; // 1 heure

    /**
     * Boot du modèle - invalidation du cache
     */
    protected static function booted(): void
    {
        // Invalider le cache à chaque modification
        static::saved(fn ($position) => self::clearCache($position->department_id));
        static::deleted(fn ($position) => self::clearCache($position->department_id));
    }

    /**
     * Le département de cette position
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Les employés ayant cette position
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope pour les positions actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Nom complet avec département
     */
    public function getFullNameAttribute(): string
    {
        return $this->department->name.' - '.$this->name;
    }

    /**
     * Récupérer les positions actives avec cache
     */
    public static function getActiveCached()
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE, self::CACHE_TTL, function () {
            return static::active()->with('department')->orderBy('name')->get();
        });
    }

    /**
     * Récupérer les positions d'un département avec cache
     */
    public static function getByDepartmentCached(int $departmentId)
    {
        return Cache::remember(self::CACHE_KEY_BY_DEPARTMENT.$departmentId, self::CACHE_TTL, function () use ($departmentId) {
            return static::where('department_id', $departmentId)
                ->active()
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Vider le cache des positions
     */
    public static function clearCache(?int $departmentId = null): void
    {
        Cache::forget(self::CACHE_KEY_ACTIVE);
        if ($departmentId) {
            Cache::forget(self::CACHE_KEY_BY_DEPARTMENT.$departmentId);
        }
    }
}
