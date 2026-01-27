<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Admin can do anything
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * View a document
     */
    public function view(User $user, Document $document): bool
    {
        // User can view their own documents if type allows
        if ($document->user_id === $user->id) {
            return $document->type->employee_can_view;
        }
        return false;
    }

    /**
     * Delete a document
     */
    public function delete(User $user, Document $document): bool
    {
        // User can only delete their own pending documents if type allows
        if ($document->user_id === $user->id) {
            return $document->type->employee_can_delete && 
                   $document->status !== 'approved';
        }
        return false;
    }

    /**
     * Download a document
     */
    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}
