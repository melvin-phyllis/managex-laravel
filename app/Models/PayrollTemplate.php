<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollTemplate extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'blade_path',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Pays associé
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(PayrollCountry::class, 'country_id');
    }

    /**
     * Fiches utilisant ce template
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'template_id');
    }

    /**
     * Définit ce template comme défaut (et retire le flag des autres)
     */
    public function setAsDefault(): void
    {
        // Retirer le flag des autres templates du même pays
        static::where('country_id', $this->country_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
