<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_type',
        'base_salary',
        'start_date',
        'end_date',
        'is_current',
        'notes',
        'document_path',
        'document_original_name',
        'document_uploaded_at',
        'document_uploaded_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'document_uploaded_at' => 'datetime',
    ];

    /**
     * Get the user that owns the contract.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payrolls for the contract.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Scope a query to only include current contract.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to only include active contracts (by date).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    /**
     * Get formatted salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        return number_format($this->base_salary, 0, ',', ' ') . ' FCFA';
    }
}
