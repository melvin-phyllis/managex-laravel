<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayrollPolicy
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
    public function view(User $user, Payroll $payroll): bool
    {
        // Admin peut tout voir, l'employé ne peut voir que ses propres fiches de paie
        return $user->role === 'admin' || $user->id === $payroll->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Seul l'admin peut créer des fiches de paie
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payroll $payroll): bool
    {
        // Seul l'admin peut modifier des fiches de paie
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payroll $payroll): bool
    {
        // Seul l'admin peut supprimer des fiches de paie
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can download the payroll PDF.
     */
    public function download(User $user, Payroll $payroll): bool
    {
        // Admin peut tout télécharger, l'employé ne peut télécharger que ses propres fiches
        return $user->role === 'admin' || $user->id === $payroll->user_id;
    }
}
