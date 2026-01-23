<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    /**
     * Récupérer une valeur de paramètre par sa clé
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Définir une valeur de paramètre
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $group = null, ?string $description = null): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            static::create([
                'key' => $key,
                'value' => $value,
                'type' => $type ?? 'string',
                'group' => $group,
                'description' => $description,
            ]);
        }

        Cache::forget("setting.{$key}");
    }

    /**
     * Récupérer tous les paramètres d'un groupe
     */
    public static function getGroup(string $group): array
    {
        $settings = static::where('group', $group)->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = static::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    /**
     * Convertir la valeur selon son type
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer', 'int' => (int) $value,
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode($value, true),
            'time' => $value, // Retourner comme string "HH:MM"
            default => $value,
        };
    }

    /**
     * Helpers pour les paramètres de présence
     */
    public static function getWorkStartTime(): string
    {
        return static::get('work_start_time', '08:00');
    }

    public static function getWorkEndTime(): string
    {
        return static::get('work_end_time', '17:00');
    }

    public static function getBreakStartTime(): string
    {
        return static::get('break_start_time', '12:00');
    }

    public static function getBreakEndTime(): string
    {
        return static::get('break_end_time', '13:00');
    }

    public static function getLateTolerance(): int
    {
        return static::get('late_tolerance_minutes', 15);
    }

    /**
     * Calculer le nombre d'heures de travail par jour (hors pause)
     */
    public static function getDailyWorkHours(): float
    {
        $start = \Carbon\Carbon::createFromFormat('H:i', static::getWorkStartTime());
        $end = \Carbon\Carbon::createFromFormat('H:i', static::getWorkEndTime());
        $breakStart = \Carbon\Carbon::createFromFormat('H:i', static::getBreakStartTime());
        $breakEnd = \Carbon\Carbon::createFromFormat('H:i', static::getBreakEndTime());

        $totalMinutes = $end->diffInMinutes($start);
        $breakMinutes = $breakEnd->diffInMinutes($breakStart);

        return ($totalMinutes - $breakMinutes) / 60;
    }
}
