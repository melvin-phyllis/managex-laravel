<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollCountry extends Model
{
    protected $fillable = [
        'code',
        'name',
        'currency',
        'currency_symbol',
        'legal_mentions',
        'is_active',
    ];

    protected $casts = [
        'legal_mentions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Règles de calcul du pays
     */
    public function rules(): HasMany
    {
        return $this->hasMany(PayrollCountryRule::class, 'country_id')->orderBy('display_order');
    }

    /**
     * Champs dynamiques du pays
     */
    public function fields(): HasMany
    {
        return $this->hasMany(PayrollCountryField::class, 'country_id')->orderBy('display_order');
    }

    /**
     * Templates PDF du pays
     */
    public function templates(): HasMany
    {
        return $this->hasMany(PayrollTemplate::class, 'country_id');
    }

    /**
     * Fiches de paie du pays
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'country_id');
    }

    /**
     * Template par défaut
     */
    public function defaultTemplate()
    {
        return $this->templates()->where('is_default', true)->first();
    }

    /**
     * Règles employé (retenues)
     */
    public function employeeRules()
    {
        return $this->rules()->whereIn('rule_category', ['employee', 'both']);
    }

    /**
     * Règles employeur (charges patronales)
     */
    public function employerRules()
    {
        return $this->rules()->whereIn('rule_category', ['employer', 'both']);
    }

    /**
     * Scope: Pays actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
