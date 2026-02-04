<?php

namespace App\Services\AI;

use App\Models\Department;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class HRAssistantService
{
    protected MistralService $mistral;

    public function __construct(MistralService $mistral)
    {
        $this->mistral = $mistral;
    }

    /**
     * Poser une question au chatbot RH.
     *
     * @param  User  $user  L'employé authentifié
     * @param  string  $question  La question posée
     * @param  array  $history  Historique des messages [{role, content}]
     * @return string Réponse de l'IA
     */
    public function ask(User $user, string $question, array $history = []): string
    {
        if (! $this->mistral->isAvailable()) {
            return "Le service d'assistance IA est temporairement indisponible. Veuillez contacter le service RH directement.";
        }

        $context = $this->buildEmployeeContext($user);
        $system = $this->buildSystemPrompt($context);

        $messages = collect($history)
            ->take(-10) // Garder les 10 derniers messages max
            ->map(fn (array $msg) => [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => mb_substr($msg['content'], 0, 500),
            ])
            ->push(['role' => 'user', 'content' => $question])
            ->values()
            ->toArray();

        $response = $this->mistral->chat($system, $messages);

        if ($response === null) {
            return 'Je suis temporairement indisponible. Veuillez réessayer dans quelques instants ou contacter le service RH.';
        }

        return $response;
    }

    /**
     * Poser une question en tant qu'admin (contexte entreprise).
     */
    public function askAsAdmin(User $admin, string $question, array $history = []): string
    {
        if (! $this->mistral->isAvailable()) {
            return 'Le service IA est temporairement indisponible. Veuillez réessayer plus tard.';
        }

        $context = $this->buildAdminContext();
        $system = $this->buildAdminSystemPrompt($context);

        $messages = collect($history)
            ->take(-10)
            ->map(fn (array $msg) => [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => mb_substr($msg['content'], 0, 500),
            ])
            ->push(['role' => 'user', 'content' => $question])
            ->values()
            ->toArray();

        $response = $this->mistral->chat($system, $messages);

        if ($response === null) {
            return 'Je suis temporairement indisponible. Veuillez réessayer dans quelques instants.';
        }

        return $response;
    }

    /**
     * Construire le contexte admin (données entreprise).
     */
    protected function buildAdminContext(): string
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $todayStr = $now->toDateString();

        // Effectif
        $totalActive = User::where('role', 'employee')->where('status', 'active')->count();
        $totalInterns = User::where('status', 'active')->where('contract_type', 'stage')->count();
        $totalEmployees = $totalActive - $totalInterns;

        // Présences aujourd'hui
        $presentsToday = Presence::where('date', $todayStr)->distinct('user_id')->count('user_id');

        // Présences du mois
        $monthPresences = Presence::whereBetween('date', [$monthStart, $now])->get();
        $lateCountMonth = $monthPresences->where('is_late', true)->count();
        $totalLateHours = round($monthPresences->sum('late_minutes') / 60, 1);

        // Retards par département
        $latsByDept = Presence::whereBetween('date', [$monthStart, $now])
            ->where('is_late', true)
            ->join('users', 'presences.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as dept, COUNT(*) as total')
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'dept');

        // Congés en attente
        $pendingLeaves = Leave::where('statut', 'en_attente')->count();
        $onLeaveToday = Leave::where('statut', 'approuve')
            ->where('date_debut', '<=', $todayStr)
            ->where('date_fin', '>=', $todayStr)
            ->count();

        // Tâches
        $taskStats = Task::selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Départements
        $departments = Department::withCount(['users' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        $lines = [
            "=== DONNÉES ENTREPRISE ({$now->format('d/m/Y H:i')}) ===",
            '',
            '--- Effectif ---',
            "Employés actifs (CDI): {$totalEmployees}",
            "Stagiaires actifs: {$totalInterns}",
            "Effectif total actif: {$totalActive}",
            "Présents aujourd'hui: {$presentsToday}/{$totalActive}",
            "En congé aujourd'hui: {$onLeaveToday}",
            '',
            '--- Présences (mois en cours) ---',
            "Retards ce mois: {$lateCountMonth} occurrences",
            "Heures de retard cumulées: {$totalLateHours}h",
        ];

        if ($latsByDept->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '--- Retards par département (top 5) ---';
            foreach ($latsByDept as $dept => $count) {
                $lines[] = "  {$dept}: {$count} retards";
            }
        }

        $lines[] = '';
        $lines[] = '--- Congés ---';
        $lines[] = "Demandes en attente: {$pendingLeaves}";
        $lines[] = "En congé aujourd'hui: {$onLeaveToday}";

        $lines[] = '';
        $lines[] = '--- Tâches ---';
        $lines[] = 'En attente: '.($taskStats['pending'] ?? 0);
        $lines[] = 'Approuvées: '.($taskStats['approved'] ?? 0);
        $lines[] = 'Complétées: '.($taskStats['completed'] ?? 0);
        $lines[] = 'Validées: '.($taskStats['validated'] ?? 0);

        if ($departments->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '--- Départements ---';
            foreach ($departments as $dept) {
                $lines[] = "  {$dept->name}: {$dept->users_count} employés";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Prompt système pour le chatbot admin.
     */
    protected function buildAdminSystemPrompt(string $context): string
    {
        $companyName = config('app.name', 'ManageX');

        return <<<PROMPT
Tu es l'assistant IA de gestion RH de {$companyName}, destiné aux administrateurs. Tu réponds en français de manière concise et professionnelle.

RÔLE:
- Tu aides l'administrateur à comprendre et analyser les données RH de l'entreprise
- Tu donnes des insights sur les tendances (présences, retards, congés, tâches)
- Tu proposes des recommandations concrètes et actionnables
- Tu alertes sur les anomalies ou situations nécessitant une attention

RÈGLES STRICTES:
- Sois concis: 2-4 phrases, sauf si une analyse détaillée est demandée
- Base-toi uniquement sur les données fournies, n'invente rien
- Utilise des pourcentages et comparaisons quand c'est pertinent
- Mets en gras (**mot**) les chiffres clés et points importants
- Si l'admin demande quelque chose hors contexte RH, redirige poliment
- Ne donne pas de conseils juridiques

CONTEXTE ENTREPRISE:
{$context}

FONCTIONNALITÉS DISPONIBLES DANS L'APPLICATION:
- Tableau de bord Analytics avec KPIs en temps réel
- Gestion des présences (pointage, géolocalisation, retards)
- Gestion des congés (approbation, soldes)
- Assignation et suivi des tâches
- Gestion de la paie (fiches, règles multi-pays)
- Évaluations des employés et stagiaires
- Messagerie interne
- Gestion documentaire
PROMPT;
    }

    /**
     * Construire le contexte de l'employé à partir de ses données.
     */
    protected function buildEmployeeContext(User $user): string
    {
        $user->loadMissing(['department', 'position']);

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        // Présences du mois
        $presences = Presence::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $now])
            ->get();

        $presenceDays = $presences->count();
        $lateCount = $presences->where('is_late', true)->count();
        $totalLateMinutes = $presences->sum('late_minutes');

        // Tâches en cours
        $tasks = Task::where('user_id', $user->id)
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Congés
        $pendingLeaves = Leave::where('user_id', $user->id)
            ->where('statut', 'pending')
            ->count();

        $approvedLeaves = Leave::where('user_id', $user->id)
            ->where('statut', 'approved')
            ->whereYear('date_debut', $now->year)
            ->get(['date_debut', 'date_fin']);

        $approvedLeaveDays = $approvedLeaves->sum(function ($leave) {
            return Carbon::parse($leave->date_debut)->diffInDays(Carbon::parse($leave->date_fin)) + 1;
        });

        // Contrat
        $contract = $user->currentContract;

        $lines = [
            "Nom: {$user->name}",
            'Poste: '.($user->position->name ?? 'Non défini'),
            'Département: '.($user->department->name ?? 'Non défini'),
            "Date d'embauche: ".($user->hire_date ? Carbon::parse($user->hire_date)->format('d/m/Y') : 'Non définie'),
            'Type de contrat: '.($user->contract_type ?? 'Non défini'),
        ];

        if ($contract) {
            if ($contract->end_date) {
                $lines[] = 'Fin de contrat: '.Carbon::parse($contract->end_date)->format('d/m/Y');
            }
            $lines[] = 'Salaire de base: '.number_format($contract->base_salary ?? 0, 0, ',', ' ').' FCFA';
        }

        $lines[] = '';
        $lines[] = '--- Présences (mois en cours) ---';
        $lines[] = "Jours de présence: {$presenceDays}";
        $lines[] = "Retards: {$lateCount}";
        $lines[] = "Minutes de retard total: {$totalLateMinutes}";

        $lines[] = '';
        $lines[] = '--- Tâches ---';
        $lines[] = 'En attente: '.($tasks['pending'] ?? 0);
        $lines[] = 'Approuvées: '.($tasks['approved'] ?? 0);
        $lines[] = 'Complétées: '.($tasks['completed'] ?? 0);

        $lines[] = '';
        $lines[] = '--- Congés ---';
        $lines[] = "Demandes en attente: {$pendingLeaves}";
        $lines[] = "Jours utilisés cette année: {$approvedLeaveDays}";
        $lines[] = 'Solde congés: '.($user->leave_balance ?? 'Non défini');

        return implode("\n", $lines);
    }

    /**
     * Construire le prompt système pour le chatbot RH.
     */
    protected function buildSystemPrompt(string $context): string
    {
        $companyName = config('app.name', 'ManageX');

        return <<<PROMPT
Tu es l'assistant RH virtuel de {$companyName}. Tu réponds en français de manière concise et professionnelle.

RÈGLES STRICTES:
- Réponds uniquement aux questions liées au travail, aux RH, aux congés, aux présences, aux tâches et à la paie
- Ne divulgue JAMAIS d'informations sur d'autres employés
- Si tu ne connais pas la réponse, dirige l'employé vers le service RH
- Sois concis: 2-3 phrases maximum sauf si une explication détaillée est nécessaire
- N'invente jamais de données: utilise uniquement le contexte fourni
- Ne donne pas de conseils juridiques

CONTEXTE EMPLOYÉ:
{$context}

FONCTIONNALITÉS DISPONIBLES DANS L'APPLICATION:
- Pointage des présences (check-in / check-out) avec géolocalisation
- Sessions de rattrapage pour récupérer les retards (jours non travaillés)
- Demandes de congés (payés, maladie, autres)
- Suivi des tâches assignées par l'administrateur
- Consultation des fiches de paie
- Messagerie interne
- Documents personnels et administratifs
PROMPT;
    }
}
