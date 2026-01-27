<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollCountryField extends Model
{
    protected $fillable = [
        'country_id',
        'field_name',
        'field_label',
        'field_type',
        'options',
        'is_required',
        'default_value',
        'placeholder',
        'help_text',
        'min_value',
        'max_value',
        'section',
        'display_order',
        'is_taxable',
        'affects_gross',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'display_order' => 'integer',
        'is_taxable' => 'boolean',
        'affects_gross' => 'boolean',
    ];

    /**
     * Pays associé
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(PayrollCountry::class, 'country_id');
    }

    /**
     * Génère les règles de validation Laravel
     */
    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($this->field_type) {
            case 'number':
                $rules[] = 'numeric';
                if ($this->min_value !== null) {
                    $rules[] = "min:{$this->min_value}";
                }
                if ($this->max_value !== null) {
                    $rules[] = "max:{$this->max_value}";
                }
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
            case 'select':
                if ($this->options) {
                    $values = collect($this->options)->pluck('value')->implode(',');
                    $rules[] = "in:{$values}";
                }
                break;
            default:
                $rules[] = 'string';
        }

        return $rules;
    }

    /**
     * Scope: Par section
     */
    public function scopeSection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope: Champs imposables
     */
    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    /**
     * Scope: Champs non imposables
     */
    public function scopeNonTaxable($query)
    {
        return $query->where('is_taxable', false);
    }
}
