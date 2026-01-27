<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'document_type_id',
        'title',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'description',
        'document_date',
        'expiry_date',
        'status',
        'rejection_reason',
        'validated_by',
        'validated_at',
        'requires_acknowledgment',
        'acknowledged_at',
        'uploaded_by',
        'download_count',
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'validated_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'requires_acknowledgment' => 'boolean',
    ];

    // ============ RELATIONS ============

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ============ SCOPES ============

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [today(), today()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', today());
    }

    public function scopeNeedsAcknowledgment($query)
    {
        return $query->where('requires_acknowledgment', true)
            ->whereNull('acknowledged_at');
    }

    // ============ ACCESSORS ============

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => '🟡',
            'approved' => '✅',
            'rejected' => '❌',
            default => '⚪',
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
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

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(today()) <= 30;
    }

    public function getExpiryStatusAttribute(): ?string
    {
        if (!$this->expiry_date) return null;
        if ($this->is_expired) return 'expired';
        if ($this->is_expiring_soon) return 'expiring';
        return 'valid';
    }

    // ============ HELPERS ============

    public function approve(User $validator): void
    {
        $this->update([
            'status' => 'approved',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(User $validator, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function acknowledge(): void
    {
        $this->update([
            'acknowledged_at' => now(),
        ]);
    }

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    public function getFullPath(): string
    {
        return Storage::disk('documents')->path($this->file_path);
    }

    public function fileExists(): bool
    {
        return Storage::disk('documents')->exists($this->file_path);
    }

    /**
     * Delete the associated file
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::disk('documents')->delete($this->file_path);
        }
        return true;
    }

    /**
     * Boot method to delete file when document is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
