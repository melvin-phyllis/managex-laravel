<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'owner_type',
        'requires_validation',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_validation' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ============ RELATIONS ============

    public function types(): HasMany
    {
        return $this->hasMany(DocumentType::class, 'category_id');
    }

    public function activeTypes(): HasMany
    {
        return $this->types()->where('is_active', true)->orderBy('sort_order');
    }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ============ ACCESSORS ============

    public function getIconEmojiAttribute(): string
    {
        return match ($this->slug) {
            'administrative' => '📋',
            'contractual' => '📝',
            'daily' => '📁',
            default => '📄',
        };
    }
}
