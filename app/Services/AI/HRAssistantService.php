<?php

namespace App\Services\AI;

use App\Models\Department;
use App\Models\EmployeeEvaluation;
use App\Models\EmployeeWorkDay;
use App\Models\InternEvaluation;
use App\Models\Leave;
use App\Models\Payroll;
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

        $response = $this->mistral->chat($system, $messages, 4096);

        if ($response === null) {
            return 'Je suis temporairement indisponible. Veuillez réessayer dans quelques instants.';
        }

        return $response;
    }

    /**
     * Construire le contexte admin (données entreprise détaillées).
     */
    protected function buildAdminContext(): string
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $yearStart = $now->copy()->startOfYear();
        $todayStr = $now->toDateString();
        $tomorrowStr = $now->copy()->addDay()->toDateString();
        $tomorrowDayOfWeek = (int) $now->copy()->addDay()->isoFormat('E');
        $tomorrowDayName = EmployeeWorkDay::DAYS[$tomorrowDayOfWeek] ?? '?';

        // ─── 1. Données Globales ───
        $totalActive = User::where('role', 'employee')->where('status', 'active')->count();
        $presentsToday = Presence::where('date', $todayStr)->distinct('user_id')->count('user_id');
        $lateCountMonth = Presence::whereBetween('date', [$monthStart, $now])->where('is_late', true)->count();
        $pendingLeavesCount = Leave::where('statut', 'en_attente')->count();

        // Départements avec effectifs
        $departments = Department::withCount(['users' => fn($q) => $q->where('status', 'active')])->get();
        $deptLines = $departments->map(fn($d) => "{$d->name}: {$d->users_count} employés")->implode(', ');

        // Tâches globales par statut
        $tasksByStatus = Task::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut')->toArray();
        $taskPending = $tasksByStatus['pending'] ?? 0;
        $taskApproved = $tasksByStatus['approved'] ?? 0;
        $taskCompleted = $tasksByStatus['completed'] ?? 0;
        $taskRejected = $tasksByStatus['rejected'] ?? 0;

        // Congés en attente avec détails
        $pendingLeavesList = Leave::with('user')->where('statut', 'en_attente')->get();
        $pendingLeavesDetails = $pendingLeavesList->map(function ($l) {
            $name = $l->user->name ?? 'Inconnu';
            $debut = Carbon::parse($l->date_debut)->format('d/m');
            $fin = Carbon::parse($l->date_fin)->format('d/m');
            return "{$name} ({$l->type}, {$debut}-{$fin})";
        })->implode(' ; ');

        // Masse salariale du mois (dernier payroll par employé)
        $currentMonthPayrolls = Payroll::where('mois', $now->month)->where('annee', $now->year)->get();
        $masseSalariale = $currentMonthPayrolls->sum('net_salary');
        $masseSalarialeFormatted = number_format($masseSalariale, 0, ',', ' ');

        // Stats stagiaires
        $internsCount = User::where('status', 'active')->where('contract_type', 'stage')->count();
        $internsWithoutEvalThisWeek = 0;
        if ($internsCount > 0) {
            $weekStart = $now->copy()->startOfWeek();
            $internIds = User::where('status', 'active')->where('contract_type', 'stage')->pluck('id');
            $evaluatedThisWeek = InternEvaluation::whereIn('intern_id', $internIds)->where('week_start', $weekStart)->pluck('intern_id')->unique();
            $internsWithoutEvalThisWeek = $internIds->diff($evaluatedThisWeek)->count();
        }

        // ─── 2. Données Détaillées par Employé ───
        $employees = User::with(['department', 'position', 'currentContract', 'supervisor'])
            ->where('status', 'active')
            ->get();

        $allWorkDays = EmployeeWorkDay::whereIn('user_id', $employees->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $tomorrowLeaves = Leave::where('statut', 'approuve')
            ->where('date_debut', '<=', $tomorrowStr)
            ->where('date_fin', '>=', $tomorrowStr)
            ->get()
            ->keyBy('user_id');

        $employeeDetails = [];
        $expectedTomorrow = [];
        $absentTomorrow = [];

        foreach ($employees as $emp) {
            // Présence du mois
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

            // Congés actifs aujourd'hui
            $leaveBalance = $emp->leave_balance;
            $activeLeave = Leave::where('user_id', $emp->id)
                ->where('statut', 'approuve')
                ->where('date_debut', '<=', $todayStr)
                ->where('date_fin', '>=', $todayStr)
                ->first();

            if ($activeLeave) {
                $statusToday = "En congé ({$activeLeave->type}) jusqu'au " . Carbon::parse($activeLeave->date_fin)->format('d/m');
            }

            // Planning
            $empWorkDays = $allWorkDays->get($emp->id, collect());
            $workDayNumbers = $empWorkDays->pluck('day_of_week')->toArray();
            $workDayNames = array_map(fn($d) => EmployeeWorkDay::DAYS[$d] ?? '?', $workDayNumbers);
            $planning = !empty($workDayNames) ? implode(', ', $workDayNames) : 'Non défini';

            // Prédiction demain
            $worksOnTomorrow = in_array($tomorrowDayOfWeek, $workDayNumbers);
            $onLeaveTomorrow = $tomorrowLeaves->has($emp->id);

            if ($worksOnTomorrow && !$onLeaveTomorrow) {
                $expectedTomorrow[] = $emp->name;
            } elseif ($onLeaveTomorrow) {
                $leaveTomorrow = $tomorrowLeaves->get($emp->id);
                $absentTomorrow[] = "{$emp->name} (Congé {$leaveTomorrow->type} jusqu'au " . Carbon::parse($leaveTomorrow->date_fin)->format('d/m') . ')';
            }

            $role = $emp->role === 'admin' ? 'Admin' : ($emp->contract_type === 'stage' ? 'Stagiaire' : 'Employé');

            // ── Infos de base ──
            $lines = [
                "  Nom: {$emp->name} | ID: {$emp->id} | Rôle: {$role}",
                "  Email: {$emp->email} | Tél: " . ($emp->phone ?? 'N/A'),
                "  Dépt: " . ($emp->department->name ?? 'N/A') . " | Poste: " . ($emp->position->name ?? 'N/A'),
                "  Contrat: " . ($emp->contract_type ?? 'N/A') . " | Début: " . ($emp->hire_date ? Carbon::parse($emp->hire_date)->format('d/m/Y') : '?'),
            ];

            // Contrat & salaire
            $contract = $emp->currentContract;
            if ($contract) {
                $salary = number_format($contract->base_salary ?? 0, 0, ',', ' ');
                $lines[] = "  Salaire base: {$salary} FCFA";
                if ($contract->end_date) {
                    $lines[] = "  Fin contrat: " . Carbon::parse($contract->end_date)->format('d/m/Y');
                }
            }

            $lines[] = "  Statut Aujourd'hui: {$statusToday} | Planning: {$planning}";
            $lines[] = "  Stats Mois: {$daysPresent}j présents, {$lates} retards ({$totalLateMins} min) | Solde Congés: {$leaveBalance}j";

            // ── Détail des 10 dernières tâches ──
            $recentTasks = Task::where('user_id', $emp->id)->orderByDesc('created_at')->take(10)->get();
            if ($recentTasks->isNotEmpty()) {
                $taskSummaries = $recentTasks->map(function ($t) {
                    $deadline = $t->date_fin ? Carbon::parse($t->date_fin)->format('d/m/Y') : '?';
                    return "{$t->titre}[{$t->statut},{$t->priorite},{$t->progression}%,éch:{$deadline}]";
                })->implode(' ; ');
                $lines[] = "  Tâches (10 dern.): {$taskSummaries}";
            } else {
                $lines[] = "  Tâches: Aucune";
            }

            // ── Historique congés de l'année ──
            $yearLeaves = Leave::where('user_id', $emp->id)
                ->whereYear('date_debut', $now->year)
                ->orderByDesc('date_debut')
                ->get();
            if ($yearLeaves->isNotEmpty()) {
                $leavesSummary = $yearLeaves->map(function ($l) {
                    $d = Carbon::parse($l->date_debut)->format('d/m');
                    $f = Carbon::parse($l->date_fin)->format('d/m');
                    return "{$l->type}({$d}-{$f},{$l->statut})";
                })->implode(' ; ');
                $lines[] = "  Congés {$now->year}: {$leavesSummary}";
            }

            // ── Dernière paie ──
            $lastPayroll = Payroll::where('user_id', $emp->id)->orderByDesc('annee')->orderByDesc('mois')->first();
            if ($lastPayroll) {
                $netFormatted = number_format($lastPayroll->net_salary, 0, ',', ' ');
                $lines[] = "  Dernière paie: {$lastPayroll->mois_label} {$lastPayroll->annee} | Net: {$netFormatted} FCFA | Statut: {$lastPayroll->statut}";
            }

            // ── Évaluation employé (dernière) ──
            if ($emp->contract_type !== 'stage') {
                $lastEval = EmployeeEvaluation::where('user_id', $emp->id)->orderByDesc('year')->orderByDesc('month')->first();
                if ($lastEval) {
                    $lines[] = "  Évaluation: {$lastEval->periode_label} | Score: {$lastEval->total_score}/" . EmployeeEvaluation::MAX_SCORE . " ({$lastEval->score_percentage}%) | Statut: {$lastEval->status}";
                }
            }

            // ── Stagiaire : évaluation + tuteur ──
            if ($emp->contract_type === 'stage') {
                $tutorName = $emp->supervisor->name ?? 'Non assigné';
                $lines[] = "  Tuteur: {$tutorName}";

                $lastInternEval = InternEvaluation::where('intern_id', $emp->id)->orderByDesc('week_start')->first();
                if ($lastInternEval) {
                    $lines[] = "  Éval. stagiaire: Sem. {$lastInternEval->week_start->format('d/m/Y')} | Score: {$lastInternEval->total_score}/10 | Grade: {$lastInternEval->grade_letter}";
                }
            }

            $employeeDetails[] = implode("\n", $lines);
        }

        // ─── 3. Construction du Contexte Final ───
        $contextLines = [
            "=== RÉSUMÉ GLOBAL ({$now->format('d/m/Y H:i')}) ===",
            "Effectif Actif: {$totalActive}",
            "Présents ce jour: {$presentsToday}",
            "Retards ce mois: {$lateCountMonth}",
            "Congés en attente: {$pendingLeavesCount}",
            "Départements: {$deptLines}",
            "Tâches globales: {$taskPending} en attente, {$taskApproved} approuvées, {$taskCompleted} terminées, {$taskRejected} rejetées",
            "Masse salariale mois: {$masseSalarialeFormatted} FCFA",
            "Stagiaires: {$internsCount} actifs, {$internsWithoutEvalThisWeek} sans évaluation cette semaine",
            '',
            "=== CONGÉS EN ATTENTE (détails) ===",
            empty($pendingLeavesDetails) ? 'Aucun congé en attente' : $pendingLeavesDetails,
            '',
            "=== PRÉVISION DEMAIN ({$tomorrowDayName} {$now->copy()->addDay()->format('d/m/Y')}) ===",
            'Employés attendus (' . count($expectedTomorrow) . '): ' . (empty($expectedTomorrow) ? 'Aucun (jour non travaillé ou planning non défini)' : implode(', ', $expectedTomorrow)),
            'Absents prévus (' . count($absentTomorrow) . '): ' . (empty($absentTomorrow) ? 'Aucun' : implode(', ', $absentTomorrow)),
            '',
            "=== DÉTAILS DES EMPLOYÉS (Base de données en temps réel) ===",
            '',
        ];

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
- Tu as accès en TEMPS RÉEL à toutes les données de la base (utilisateurs, tâches, présences, congés, paie, évaluations, stagiaires, départements) via le contexte fourni.
- Tu aides l'administrateur à analyser ces données, trouver des informations précises sur un employé, ou détecter des tendances.
- Tu PEUX prédire qui sera présent demain grâce aux plannings de travail et aux congés approuvés fournis dans le contexte.
- Tes réponses doivent être basées UNIQUEMENT sur les données fournies. Si une info est absente, dis-le.

RÈGLES STRICTES:
- Sois direct et factuel.
- Si on te demande "Qui est en retard ?", liste les noms.
- Si on te demande "Qui sera présent demain ?", utilise la section PRÉVISION DEMAIN du contexte.
- Si on te demande des détails sur un employé, donne tout ce que tu as dans le contexte.
- Ne mentionne jamais de données qui ne sont pas dans le contexte (pas d'hallucination).

CONTEXTE BASE DE DONNÉES (Mise à jour temps réel):
{$context}

FONCTIONNALITÉS:
- Analyse des présences et retards
- Prévision des présences de demain (basée sur le planning et les congés)
- Détail des tâches par employé (nom, deadline, priorité, progression)
- Historique et solde des congés
- Informations de paie et masse salariale
- Évaluations des employés (CDI/CDD) et des stagiaires
- Statistiques par département
- Gestion des contrats
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
