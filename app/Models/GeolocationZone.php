<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeolocationZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'radius',
        'address',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Les présences dans cette zone
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'geolocation_zone_id');
    }

    /**
     * Scope pour les zones actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Vérifie si des coordonnées sont dans la zone
     */
    public function isWithinZone(float $lat, float $lng): bool
    {
        $distance = $this->calculateDistance($lat, $lng);
        return $distance <= $this->radius;
    }

    /**
     * Calcule la distance en mètres (formule Haversine)
     */
    public function calculateDistance(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // Rayon de la Terre en mètres

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Trouve la zone par défaut
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }
}
