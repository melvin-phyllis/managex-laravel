<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Upload a document for a user
     */
    public function upload(
        UploadedFile $file,
        User $user,
        DocumentType $type,
        User $uploader,
        array $metadata = []
    ): Document {
        // Validate extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (! $type->isExtensionAllowed($extension)) {
            throw new \InvalidArgumentException(
                "Extension .{$extension} non autorisée. Formats acceptés : ".$type->getAllowedExtensionsString()
            );
        }

        // Validate size
        $maxBytes = $type->max_size_mb * 1024 * 1024;
        if ($file->getSize() > $maxBytes) {
            throw new \InvalidArgumentException(
                "Fichier trop volumineux. Taille max : {$type->max_size_mb} MB"
            );
        }

        // If type is unique, check for existing document
        if ($type->is_unique) {
            $existing = Document::where('user_id', $user->id)
                ->where('document_type_id', $type->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existing) {
                // Delete old file and document
                $existing->delete();
            }
        }

        // Generate unique filename
        $filename = $this->generateFilename($file, $user, $type);

        // Store file
        $path = $file->storeAs(
            $this->getStoragePath($user, $type),
            $filename,
            'documents'
        );

        // Determine initial status
        $status = $type->requires_validation ? 'pending' : 'approved';

        // Create document record
        return Document::create([
            'user_id' => $user->id,
            'document_type_id' => $type->id,
            'title' => $metadata['title'] ?? $type->name,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $metadata['description'] ?? null,
            'document_date' => $metadata['document_date'] ?? null,
            'expiry_date' => $metadata['expiry_date'] ?? null,
            'status' => $status,
            'requires_acknowledgment' => $type->slug === 'internal_rules',
            'uploaded_by' => $uploader->id,
            'validated_by' => ! $type->requires_validation ? $uploader->id : null,
            'validated_at' => ! $type->requires_validation ? now() : null,
        ]);
    }

    /**
     * Generate a unique filename
     */
    protected function generateFilename(UploadedFile $file, User $user, DocumentType $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);

        return "{$type->slug}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get storage path for a document
     */
    protected function getStoragePath(User $user, DocumentType $type): string
    {
        return "users/{$user->id}/{$type->category->slug}";
    }

    /**
     * Get missing required documents for a user
     */
    public function getMissingRequiredDocuments(User $user): array
    {
        $requiredTypes = DocumentType::active()
            ->required()
            ->with('category')
            ->get();

        $missing = [];

        foreach ($requiredTypes as $type) {
            // Skip if employee can't upload (company documents)
            if (! $type->employee_can_upload) {
                // Check if company has uploaded
                $hasDocument = Document::where('user_id', $user->id)
                    ->where('document_type_id', $type->id)
                    ->where('status', 'approved')
                    ->exists();

                if (! $hasDocument) {
                    $missing[] = [
                        'type' => $type,
                        'can_upload' => false,
                        'message' => 'En attente (RH)',
                    ];
                }

                continue;
            }

            // Check if user has a valid document
            if (! $type->hasValidDocument($user)) {
                $missing[] = [
                    'type' => $type,
                    'can_upload' => true,
                    'message' => 'À fournir',
                ];
            }
        }

        return $missing;
    }

    /**
     * Get documents pending validation
     */
    public function getPendingValidation(): \Illuminate\Database\Eloquent\Collection
    {
        return Document::pending()
            ->with(['user', 'type.category', 'uploader'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get documents expiring soon
     */
    public function getExpiringSoon(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Document::approved()
            ->expiringSoon($days)
            ->with(['user', 'type'])
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get user's documents grouped by category
     */
    public function getUserDocumentsByCategory(User $user): array
    {
        $documents = Document::forUser($user)
            ->whereIn('status', ['pending', 'approved'])
            ->with(['type.category'])
            ->get()
            ->groupBy(fn ($doc) => $doc->type->category->slug);

        return $documents->toArray();
    }

    /**
     * Get onboarding completion status
     */
    public function getOnboardingStatus(User $user): array
    {
        $requiredTypes = DocumentType::active()
            ->required()
            ->where('employee_can_upload', true)
            ->get();

        $total = $requiredTypes->count();
        $completed = 0;
        $pending = 0;

        foreach ($requiredTypes as $type) {
            $doc = $type->getDocumentForUser($user);
            if ($doc) {
                if ($doc->status === 'approved') {
                    $completed++;
                } else {
                    $pending++;
                }
            }
        }

        $percentage = $total > 0 ? round(($completed / $total) * 100) : 100;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'missing' => $total - $completed - $pending,
            'percentage' => $percentage,
        ];
    }
}
