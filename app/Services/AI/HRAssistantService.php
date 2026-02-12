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
    /**
     * Construire le contexte admin (données entreprise détaillées).
     */
    protected function buildAdminContext(): string
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $todayStr = $now->toDateString();

        // 1. Données Gloables
        $totalActive = User::where('role', 'employee')->where('status', 'active')->count();
        $presentsToday = Presence::where('date', $todayStr)->distinct('user_id')->count('user_id');
        $lateCountMonth = Presence::whereBetween('date', [$monthStart, $now])->where('is_late', true)->count();
        $pendingLeaves = Leave::where('statut', 'en_attente')->count();

        // 2. Données Détaillées par Employé
        // On récupère TOUS les employés actifs avec leurs relations clés
        $employees = User::with(['department', 'position', 'currentContract'])
            ->where('status', 'active')
            ->get();

        $employeeDetails = [];

        foreach ($employees as $emp) {
            // Stats Présence du mois
            $presencesMonth = Presence::where('user_id', $emp->id)
                ->whereBetween('date', [$monthStart, $now])
                ->get();
            
            $daysPresent = $presencesMonth->count();
            $lates = $presencesMonth->where('is_late', true)->count();
            $totalLateMins = $presencesMonth->sum('late_minutes');
            
            // Présence aujourd'hui
            $todayPresence = Presence::where('user_id', $emp->id)->where('date', $todayStr)->first();
            $statusToday = $todayPresence 
                ? ($todayPresence->is_late ? "Présent (Retard {$todayPresence->late_minutes}m)" : "Présent (À l'heure)")
                : "Absent";

            // Tâches
            $tasks = Task::where('user_id', $emp->id)
                ->selectRaw('statut, count(*) as count')
                ->groupBy('statut')
                ->pluck('count', 'statut')
                ->toArray();
            
            $pendingTasks = $tasks['pending'] ?? 0;
            $completedTasks = $tasks['completed'] ?? 0;

            // Congés
            $leaveBalance = $emp->leave_balance;
            $activeLeave = Leave::where('user_id', $emp->id)
                ->where('statut', 'approuve')
                ->where('date_debut', '<=', $todayStr)
                ->where('date_fin', '>=', $todayStr)
                ->first();
            
            if ($activeLeave) {
                $statusToday = "En congé ({$activeLeave->type}) jusqu'au " . Carbon::parse($activeLeave->date_fin)->format('d/m');
            }

            // Évaluation (Stagiaires uniquement pour l'instant dans le contexte simplifié, ou dernière éval employés)
            // Pour l'instant on reste simple pour ne pas exploser le contexte, on met juste la note moyenne si dispo
            $lastEval = null; // À implémenter si besoin de détails spécifiques

            $role = $emp->role === 'admin' ? 'Admin' : ($emp->contract_type === 'stage' ? 'Stagiaire' : 'Employé');

            $details = [
                "ID: {$emp->id}",
                "Nom: {$emp->name}",
                "Dépt: " . ($emp->department->name ?? 'N/A'),
                "Poste: " . ($emp->position->name ?? 'N/A'),
                "Rôle: {$role}",
                "Statut Aujourd'hui: {$statusToday}",
                "Stats Mois: {$daysPresent}j présents, {$lates} retards ({$totalLateMins} min)",
                "Tâches: {$pendingTasks} en cours, {$completedTasks} terminées",
                "Solde Congés: {$leaveBalance}j",
                "Contrat: " . ($emp->contract_type ?? 'N/A') . " (Début: " . ($emp->hire_date ? Carbon::parse($emp->hire_date)->format('d/m/Y') : '?') . ")"
            ];

            $employeeDetails[] = implode(" | ", $details);
        }

        // 3. Construction du Prompt Final
        $contextLines = [
            "=== RÉSUMÉ GLOBAL ({$now->format('d/m/Y H:i')}) ===",
            "Effectif Actif: {$totalActive}",
            "Présents ce jour: {$presentsToday}",
            "Retards ce mois: {$lateCountMonth}",
            "Congés en attente: {$pendingLeaves}",
            "",
            "=== DÉTAILS DES EMPLOYÉS (Base de données en temps réel) ===",
            "Format: ID | Nom | Département | Poste | Rôle | Statut Jour | Stats Mois | Tâches | Congés | Contrat",
            ""
        ];

        // Ajouter la liste des employés
        $contextLines = array_merge($contextLines, $employeeDetails);

        return implode("\n", $contextLines);
    }

    /**
     * Prompt système pour le chatbot admin.
     */
    protected function buildAdminSystemPrompt(string $context): string
    {
        $companyName = config('app.name', 'ManageX');

        return <<<PROMPT
Tu es l'assistant IA de gestion RH de {$companyName}, destiné aux administrateurs. Tu réponds en français de manière concise, précise et professionnelle.

RÔLE:
- Tu as accès en TEMPS RÉEL à toutes les données de la base (utilisateurs, tâches, présences, congés, etc.) via le contexte fourni.
- Tu aides l'administrateur à analyser ces données, trouver des informations précises sur un employé, ou détecter des tendances.
- Tes réponses doivent être basées UNIQUEMENT sur les données fournies. Si une info est absente, dis-le.

RÈGLES STRICTES:
- Sois direct et factuel.
- Si on te demande "Qui est en retard ?", liste les noms.
- Si on te demande des détails sur un employé, donne tout ce que tu as dans le contexte.
- Ne mentionne jamais de données qui ne sont pas dans le contexte (pas d'hallucination).

CONTEXTE BASE DE DONNÉES (Mise à jour temps réel):
{$context}

FONCTIONNALITÉS:
- Analyse des présences et retards
- Suivi des tâches et performances
- Gestion des congés et contrats
- Vue globale et détaillée par employé
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
