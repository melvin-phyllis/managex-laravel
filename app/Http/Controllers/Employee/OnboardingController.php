<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\GlobalDocument;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding checklist for the authenticated employee.
     */
    public function index()
    {
        $user = auth()->user();

        // If already onboarded, redirect to dashboard
        if ($user->onboarding_completed_at) {
            return redirect()->route('employee.dashboard');
        }

        $steps = $this->getOnboardingSteps($user);
        $progress = $this->calculateProgress($steps);

        return view('employee.onboarding.index', compact('steps', 'progress', 'user'));
    }

    /**
     * Mark a specific onboarding step as completed.
     */
    public function completeStep(Request $request, string $step)
    {
        $user = auth()->user();

        // Store completed steps in a JSON field or session-based tracking
        $completedSteps = $user->onboarding_steps ?? [];

        if (!in_array($step, $completedSteps)) {
            $completedSteps[] = $step;
            $user->onboarding_steps = $completedSteps;
            $user->save();
        }

        // Check if all required steps are completed
        $steps = $this->getOnboardingSteps($user);
        $allDone = collect($steps)->where('required', true)->every(fn ($s) => $s['completed']);

        if ($allDone) {
            $user->onboarding_completed_at = now();
            $user->save();

            return redirect()->route('employee.dashboard')
                ->with('success', '🎉 Bienvenue chez nous ! Votre intégration est terminée.');
        }

        return back()->with('success', 'Étape complétée !');
    }

    /**
     * Build the onboarding steps with their completion status.
     */
    private function getOnboardingSteps($user): array
    {
        $completedSteps = $user->onboarding_steps ?? [];

        $steps = [
            [
                'key' => 'profile_photo',
                'title' => 'Ajouter votre photo de profil',
                'description' => 'Uploadez une photo professionnelle pour que vos collègues puissent vous reconnaître.',
                'icon' => 'camera',
                'completed' => !empty($user->avatar),
                'required' => false,
                'action_url' => route('employee.profile.index'),
                'action_label' => 'Modifier mon profil',
            ],
            [
                'key' => 'personal_info',
                'title' => 'Compléter vos informations personnelles',
                'description' => 'Vérifiez et complétez votre adresse, téléphone et contact d\'urgence.',
                'icon' => 'user',
                'completed' => !empty($user->telephone) && !empty($user->emergency_contact_name),
                'required' => true,
                'action_url' => route('employee.profile.index'),
                'action_label' => 'Compléter mon profil',
            ],
            [
                'key' => 'read_documents',
                'title' => 'Consulter les documents de l\'entreprise',
                'description' => 'Prenez connaissance du règlement intérieur et des chartes de l\'entreprise.',
                'icon' => 'document',
                'completed' => in_array('read_documents', $completedSteps),
                'required' => true,
                'action_url' => route('employee.global-documents.index'),
                'action_label' => 'Voir les documents',
            ],
            [
                'key' => 'discover_presence',
                'title' => 'Découvrir le système de pointage',
                'description' => 'Apprenez comment pointer votre arrivée et votre départ chaque jour.',
                'icon' => 'clock',
                'completed' => in_array('discover_presence', $completedSteps),
                'required' => true,
                'action_url' => route('employee.presences.index'),
                'action_label' => 'Voir mes présences',
            ],
            [
                'key' => 'explore_leaves',
                'title' => 'Comprendre les congés',
                'description' => 'Découvrez comment demander un congé et consulter votre solde.',
                'icon' => 'calendar',
                'completed' => in_array('explore_leaves', $completedSteps),
                'required' => false,
                'action_url' => route('employee.leaves.index'),
                'action_label' => 'Voir mes congés',
            ],
            [
                'key' => 'team_intro',
                'title' => 'Découvrir votre équipe',
                'description' => 'Consultez l\'organigramme pour identifier vos collègues et votre responsable.',
                'icon' => 'people',
                'completed' => in_array('team_intro', $completedSteps),
                'required' => false,
                'action_url' => route('employee.dashboard'),
                'action_label' => 'Voir l\'organigramme',
            ],
        ];

        return $steps;
    }

    /**
     * Calculate overall onboarding progress percentage.
     */
    private function calculateProgress(array $steps): int
    {
        $total = count($steps);
        $completed = collect($steps)->where('completed', true)->count();

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }
}
