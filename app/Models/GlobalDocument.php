<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class GlobalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'position_id',
        'description',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Document types constants
    const TYPE_REGLEMENT_INTERIEUR = 'reglement_interieur';

    const TYPE_FICHE_POSTE = 'fiche_poste';

    const TYPE_CHARTE_INFORMATIQUE = 'charte_informatique';

    const TYPE_POLITIQUE_CONGES = 'politique_conges';

    public static function getTypes(): array
    {
        return [
            self::TYPE_REGLEMENT_INTERIEUR => 'Règlement intérieur',
            self::TYPE_FICHE_POSTE => 'Fiche de poste',
            self::TYPE_CHARTE_INFORMATIQUE => 'Charte informatique',
            self::TYPE_POLITIQUE_CONGES => 'Politique de congés',
        ];
    }

    // ============ RELATIONS ============

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function acknowledgedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'global_document_acknowledgments')
            ->withPivot('acknowledged_at')
            ->withTimestamps();
    }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ============ ACCESSORS ============

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }

    public function getFileIconAttribute(): string
    {
        $extension = strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => '📄',
            'jpg', 'jpeg', 'png', 'gif' => '🖼️',
            'doc', 'docx' => '📝',
            'xls', 'xlsx' => '📊',
            default => '📁',
        };
    }

    // ============ HELPERS ============

    /**
     * Check if user has acknowledged this document
     */
    public function isAcknowledgedBy(User $user): bool
    {
        return $this->acknowledgedBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark document as acknowledged by user
     */
    public function acknowledge(User $user): void
    {
        if (! $this->isAcknowledgedBy($user)) {
            $this->acknowledgedBy()->attach($user->id, [
                'acknowledged_at' => now(),
            ]);
        }
    }

    /**
     * Get users who haven't acknowledged this document
     */
    public function usersNotAcknowledged()
    {
        $acknowledgedIds = $this->acknowledgedBy()->pluck('users.id');

        return User::whereNotIn('id', $acknowledgedIds)
            ->where('role', '!=', 'admin')
            ->get();
    }

    /**
     * Get the active Règlement Intérieur
     */
    public static function getActiveReglement(): ?self
    {
        return static::active()
            ->ofType(self::TYPE_REGLEMENT_INTERIEUR)
            ->latest()
            ->first();
    }

    public function getFullPath(): string
    {
        return Storage::disk('documents')->path($this->file_path);
    }

    public function fileExists(): bool
    {
        return Storage::disk('documents')->exists($this->file_path);
    }

    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::disk('documents')->delete($this->file_path);
        }

        return true;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
