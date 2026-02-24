<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    protected $fillable = ['name', 'category', 'description'];

    public const CATEGORIES = [
        'technique' => 'Technique',
        'management' => 'Management',
        'communication' => 'Communication',
        'outils' => 'Outils & Logiciels',
        'langues' => 'Langues',
        'autre' => 'Autre',
    ];

    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category ?? '-';
    }
}
