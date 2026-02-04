<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Leave $leave): bool
    {
        // Admin peut tout voir, l'employé ne peut voir que ses demandes de congés
        return $user->isAdmin() || $user->id === $leave->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tous les employés authentifiés peuvent créer des demandes de congés
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Leave $leave): bool
    {
        // Admin peut tout modifier
        // L'employé peut modifier ses demandes uniquement si elles sont en attente
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $leave->user_id && $leave->statut === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Leave $leave): bool
    {
        // Admin peut tout supprimer
        // L'employé peut annuler ses demandes uniquement si elles sont en attente
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $leave->user_id && $leave->statut === 'pending';
    }

    /**
     * Determine whether the user can approve the leave request.
     */
    public function approve(User $user, Leave $leave): bool
    {
        // Seul l'admin peut approuver des demandes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reject the leave request.
     */
    public function reject(User $user, Leave $leave): bool
    {
        // Seul l'admin peut rejeter des demandes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Leave $leave): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Leave $leave): bool
    {
        return $user->isAdmin();
    }
}
