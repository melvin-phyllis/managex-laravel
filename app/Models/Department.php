<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Cache key pour les départements actifs
     */
    const CACHE_KEY_ACTIVE = 'departments.active';

    const CACHE_TTL = 3600; // 1 heure

    /**
     * Boot du modèle - invalidation du cache
     */
    protected static function booted(): void
    {
        // Invalider le cache à chaque modification
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }

    /**
     * Les positions de ce département
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Les employés de ce département
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope pour les départements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Nombre d'employés dans ce département
     */
    public function getEmployeesCountAttribute(): int
    {
        return $this->users()->where('role', 'employee')->count();
    }

    /**
     * Récupérer les départements actifs avec cache
     */
    public static function getActiveCached()
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE, self::CACHE_TTL, function () {
            return static::active()->orderBy('name')->get();
        });
    }

    /**
     * Vider le cache des départements
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }
}
