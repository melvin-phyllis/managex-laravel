<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'is_required',
        'employee_can_upload',
        'employee_can_view',
        'employee_can_delete',
        'requires_validation',
        'has_expiry_date',
        'is_unique',
        'allowed_extensions',
        'max_size_mb',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'employee_can_upload' => 'boolean',
        'employee_can_view' => 'boolean',
        'employee_can_delete' => 'boolean',
        'requires_validation' => 'boolean',
        'has_expiry_date' => 'boolean',
        'is_unique' => 'boolean',
        'is_active' => 'boolean',
        'allowed_extensions' => 'array',
    ];

    // ============ RELATIONS ============

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeEmployeeUploadable($query)
    {
        return $query->where('employee_can_upload', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ============ HELPERS ============

    public function getAllowedExtensionsString(): string
    {
        if (empty($this->allowed_extensions)) {
            return 'PDF, JPG, PNG';
        }

        return strtoupper(implode(', ', $this->allowed_extensions));
    }

    public function isExtensionAllowed(string $extension): bool
    {
        if (empty($this->allowed_extensions)) {
            return in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png']);
        }

        return in_array(strtolower($extension), array_map('strtolower', $this->allowed_extensions));
    }

    /**
     * Get document for a specific user (if is_unique)
     */
    public function getDocumentForUser(User $user): ?Document
    {
        return $this->documents()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->first();
    }

    /**
     * Check if user has a valid document of this type
     */
    public function hasValidDocument(User $user): bool
    {
        return $this->documents()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', today());
            })
            ->exists();
    }
}
