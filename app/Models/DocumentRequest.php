<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'status',
        'admin_id',
        'admin_response',
        'document_path',
        'document_name',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // Types de documents demandables
    const TYPE_ATTESTATION_TRAVAIL = 'attestation_travail';
    const TYPE_CERTIFICAT_EMPLOI = 'certificat_emploi';
    const TYPE_ATTESTATION_SALAIRE = 'attestation_salaire';
    const TYPE_AUTRE = 'autre';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function getTypes(): array
    {
        return [
            self::TYPE_ATTESTATION_TRAVAIL => 'Attestation de travail',
            self::TYPE_CERTIFICAT_EMPLOI => 'Certificat d\'emploi',
            self::TYPE_ATTESTATION_SALAIRE => 'Attestation de salaire',
            self::TYPE_AUTRE => 'Autre document',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_APPROVED => 'Approuvé',
            self::STATUS_REJECTED => 'Refusé',
        ];
    }

    // ============ RELATIONS ============

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // ============ SCOPES ============

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ============ ACCESSORS ============

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'amber',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    // ============ METHODS ============

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function hasDocument(): bool
    {
        return !empty($this->document_path);
    }
}
