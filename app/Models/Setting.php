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

        if (! $setting) {
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
     * Récupérer tous les paramètres d'un groupe (avec cache)
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("setting.group.{$group}", 3600, function () use ($group) {
            $settings = static::where('group', $group)->get();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = static::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Invalider le cache d'un groupe de paramètres
     */
    public static function clearGroupCache(string $group): void
    {
        Cache::forget("setting.group.{$group}");
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

    /**
     * Get the default payroll country
     */
    public static function getDefaultPayrollCountry(): ?\App\Models\PayrollCountry
    {
        $countryId = static::get('payroll_country_id');
        if (! $countryId) {
            return \App\Models\PayrollCountry::where('is_active', true)->first();
        }

        return \App\Models\PayrollCountry::find($countryId);
    }

    /**
     * Helpers pour le système de rattrapage des retards
     */

    /**
     * Get number of days to recover late hours
     */
    public static function getLateRecoveryDays(): int
    {
        return static::get('late_recovery_days', 7);
    }

    /**
     * Get penalty threshold in minutes
     */
    public static function getLatePenaltyThresholdMinutes(): int
    {
        return static::get('late_penalty_threshold_minutes', 480); // 8h default
    }

    /**
     * Check if late penalty system is enabled
     */
    public static function isLatePenaltyEnabled(): bool
    {
        return static::get('late_penalty_enabled', true);
    }

    /**
     * Get penalty threshold in hours
     */
    public static function getLatePenaltyThresholdHours(): float
    {
        return static::getLatePenaltyThresholdMinutes() / 60;
    }

    /**
     * Get SMIC amount (Salaire Minimum Interprofessionnel de Croissance)
     */
    public static function getSmicAmount(): float
    {
        return (float) static::get('smic_amount', 75000);
    }

    /**
     * Get evaluation criteria max scores
     */
    public static function getEvaluationMaxScore(): float
    {
        return 5.5; // Total des critères d'évaluation
    }
}
