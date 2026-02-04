<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\EmployeeEvaluation;
use App\Models\InternEvaluation;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new KpisSheet($this->data),
            new AllEmployeesSheet($this->data),
            new DepartmentsSheet($this->data),
            new PresencesSheet($this->data),
            new EmployeeEvaluationsSheet($this->data),
            new InternEvaluationsSheet($this->data),
            new TopPerformersSheet($this->data),
            new BestAttendanceSheet($this->data),
            new LatecomersSheet($this->data),
            new TasksSheet($this->data),
            new LeavesSheet($this->data),
        ];
    }
}

class KpisSheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Resume KPIs';
    }

    public function array(): array
    {
        $kpis = $this->data['kpis'] ?? [];
        $evalStats = $this->data['evaluation_stats'] ?? [];

        return [
            // Row 1: Header
            ['RAPPORT ANALYTICS RH', '', ''],
            // Row 2: Period
            ['Période:', $this->data['period_label'] ?? '-', 'Généré le '.($this->data['generated_at'] ?? '-')],
            ['', '', ''],
            // Row 4: Section EFFECTIFS
            ['EFFECTIFS', '', ''],
            ['Effectif Total', $kpis['effectif_total']['value'] ?? 0, 'Variation: '.($kpis['effectif_total']['variation'] ?? 0).'%'],
            ['Stagiaires Actifs', $kpis['interns']['count'] ?? 0, 'À évaluer: '.($kpis['interns']['to_evaluate'] ?? 0)],
            ['', '', ''],
            // Row 8: Section PRESENCES
            ['PRÉSENCES', '', ''],
            ['Taux de Présence', ($kpis['presents_today']['percentage'] ?? 0).'%', ($kpis['presents_today']['value'] ?? 0).'/'.($kpis['presents_today']['expected'] ?? 0)],
            ['En Congé', $kpis['en_conge']['value'] ?? 0, 'CP: '.($kpis['en_conge']['types']['conge'] ?? 0).' / Maladie: '.($kpis['en_conge']['types']['maladie'] ?? 0)],
            ['Absents Non Justifiés', $kpis['absents_non_justifies']['value'] ?? 0, ''],
            ['Heures de Retard', ($kpis['late_hours']['total'] ?? 0).'h', ($kpis['late_hours']['employees'] ?? 0).' employé(s) concerné(s)'],
            ['', '', ''],
            // Row 14: Section EVALUATIONS EMPLOYES
            ['ÉVALUATIONS EMPLOYÉS', '', ''],
            ['Évaluations Validées', $evalStats['employees']['validated'] ?? 0, ''],
            ['Employés Non Évalués', $evalStats['employees']['not_evaluated'] ?? 0, ''],
            ['Note Moyenne', $evalStats['employees']['avg_score'] ?? 0, 'sur 5.5'],
            ['Meilleure Note', $evalStats['employees']['max_score'] ?? 0, 'sur 5.5'],
            ['', '', ''],
            // Row 20: Section EVALUATIONS STAGIAIRES
            ['ÉVALUATIONS STAGIAIRES', '', ''],
            ['Total Évaluations', $evalStats['interns']['total_evaluations'] ?? 0, '4 dernières semaines'],
            ['Note Moyenne', $evalStats['interns']['avg_score'] ?? 0, 'sur 10'],
            ['À Évaluer Cette Semaine', $evalStats['interns']['not_evaluated_this_week'] ?? 0, ''],
            ['', '', ''],
            // Row 25: Section TURNOVER & FINANCES
            ['TURNOVER & FINANCES', '', ''],
            ['Taux de Turnover', ($kpis['turnover']['rate'] ?? 0).'%', 'Entrées: '.($kpis['turnover']['entries'] ?? 0).' / Sorties: '.($kpis['turnover']['exits'] ?? 0)],
            ['Masse Salariale', $kpis['masse_salariale']['formatted'] ?? '0 FCFA', 'Variation: '.($kpis['masse_salariale']['variation'] ?? 0).'%'],
            ['Heures Supplémentaires', ($kpis['heures_supplementaires']['value'] ?? 0).'h', ($kpis['heures_supplementaires']['count'] ?? 0).' employé(s)'],
            ['', '', ''],
            // Row 30: Section TACHES
            ['TÂCHES', '', ''],
            ['Tâches Complétées', $kpis['tasks']['completed'] ?? 0, ''],
            ['Tâches En Attente', $kpis['tasks']['pending'] ?? 0, ''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bordures pour toutes les cellules avec données
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        // Appliquer les bordures
        $sheet->getStyle('A1:C33')->applyFromArray($borderStyle);

        // Style du titre principal (Row 1)
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->mergeCells('A1:C1');

        // Style de la période (Row 2)
        $sheet->getStyle('A2:C2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E0E7FF']],
        ]);

        // Section headers (avec fond coloré)
        $sectionRows = [4, 8, 14, 20, 25, 30];
        $sectionColors = ['3B82F6', '10B981', '059669', '7C3AED', 'F59E0B', '6366F1'];

        foreach ($sectionRows as $index => $row) {
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => $sectionColors[$index]]],
            ]);
            $sheet->mergeCells("A{$row}:C{$row}");
        }

        // Style des labels (colonne A) - gras
        $sheet->getStyle('A:A')->applyFromArray([
            'font' => ['bold' => true],
        ]);

        // Centrer les valeurs (colonne B)
        $sheet->getStyle('B:B')->applyFromArray([
            'alignment' => ['horizontal' => 'center'],
            'font' => ['bold' => true, 'size' => 11],
        ]);

        // Largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(35);

        return [];
    }
}

class AllEmployeesSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Liste Employes';
    }

    public function headings(): array
    {
        return ['ID', 'Nom', 'Email', 'Departement', 'Poste', 'Type Contrat', 'Date Embauche', 'Statut'];
    }

    public function array(): array
    {
        $rows = [];
        $employees = User::with(['department', 'position'])
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        foreach ($employees as $emp) {
            $rows[] = [
                $emp->id,
                $emp->name,
                $emp->email,
                $emp->department->name ?? '-',
                $emp->position->name ?? '-',
                strtoupper($emp->contract_type ?? '-'),
                $emp->hire_date?->format('d/m/Y') ?? '-',
                $emp->status ?? 'actif',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4F46E5']]],
        ];
    }
}

class DepartmentsSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Departements';
    }

    public function headings(): array
    {
        return ['Departement', 'Nb Employes', 'Nb Stagiaires', 'Total'];
    }

    public function array(): array
    {
        $rows = [];
        $departments = Department::withCount([
            'users as employees_count' => fn ($q) => $q->where('role', 'employee')->where('contract_type', '!=', 'stage'),
            'users as interns_count' => fn ($q) => $q->where('role', 'employee')->where('contract_type', 'stage'),
        ])->get();

        foreach ($departments as $dept) {
            $rows[] = [
                $dept->name,
                $dept->employees_count,
                $dept->interns_count,
                $dept->employees_count + $dept->interns_count,
            ];
        }

        // Total row
        $totalEmployees = $departments->sum('employees_count');
        $totalInterns = $departments->sum('interns_count');
        $rows[] = ['TOTAL', $totalEmployees, $totalInterns, $totalEmployees + $totalInterns];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '8B5CF6']]],
        ];
    }
}

class PresencesSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Presences du Mois';
    }

    public function headings(): array
    {
        return ['Date', 'Employe', 'Departement', 'Arrivee', 'Depart', 'Heures', 'Retard', 'Min Retard', 'Recupere'];
    }

    public function array(): array
    {
        $rows = [];
        $month = now()->month;
        $year = now()->year;

        $presences = Presence::with(['user', 'user.department'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('user_id')
            ->get();

        foreach ($presences as $p) {
            $rows[] = [
                $p->date->format('d/m/Y'),
                $p->user->name ?? '-',
                $p->user->department->name ?? '-',
                $p->check_in?->format('H:i') ?? '-',
                $p->check_out?->format('H:i') ?? '-',
                $p->hours_worked ? round($p->hours_worked, 1).'h' : '-',
                $p->is_late ? 'Oui' : 'Non',
                $p->late_minutes ?? 0,
                $p->recovery_minutes ?? 0,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '10B981']]],
        ];
    }
}

class EmployeeEvaluationsSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Evaluations Employes';
    }

    public function headings(): array
    {
        return ['Employe', 'Departement', 'Mois', 'Resolution Pb', 'Objectifs', 'Pression', 'Reporting', 'Total', 'Max', '%', 'Statut'];
    }

    public function array(): array
    {
        $rows = [];
        $month = now()->month;
        $year = now()->year;

        $evaluations = EmployeeEvaluation::with(['user', 'user.department'])
            ->forPeriod($month, $year)
            ->orderByDesc('total_score')
            ->get();

        foreach ($evaluations as $eval) {
            $rows[] = [
                $eval->user->name ?? '-',
                $eval->user->department->name ?? '-',
                $eval->periode_label,
                $eval->problem_solving,
                $eval->objectives_respect,
                $eval->work_under_pressure,
                $eval->accountability,
                $eval->total_score,
                EmployeeEvaluation::MAX_SCORE,
                $eval->score_percentage.'%',
                $eval->status_label,
            ];
        }

        if (empty($rows)) {
            $rows[] = ['Aucune évaluation ce mois', '', '', '', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '059669']]],
        ];
    }
}

class InternEvaluationsSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Evaluations Stagiaires';
    }

    public function headings(): array
    {
        return ['Stagiaire', 'Departement', 'Tuteur', 'Semaine', 'Discipline', 'Comportement', 'Competences', 'Communication', 'Total', 'Note', 'Statut'];
    }

    public function array(): array
    {
        $rows = [];

        $evaluations = InternEvaluation::with(['intern', 'intern.department', 'tutor'])
            ->where('week_start', '>=', now()->subWeeks(4)->startOfWeek())
            ->orderByDesc('week_start')
            ->orderBy('intern_id')
            ->get();

        foreach ($evaluations as $eval) {
            $rows[] = [
                $eval->intern->name ?? '-',
                $eval->intern->department->name ?? '-',
                $eval->tutor->name ?? '-',
                $eval->week_label,
                $eval->discipline_score,
                $eval->behavior_score,
                $eval->skills_score,
                $eval->communication_score,
                $eval->total_score,
                $eval->grade_letter,
                $eval->status === 'submitted' ? 'Soumise' : 'Brouillon',
            ];
        }

        if (empty($rows)) {
            $rows[] = ['Aucune évaluation récente', '', '', '', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '7C3AED']]],
        ];
    }
}

class TopPerformersSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Top Performers';
    }

    public function headings(): array
    {
        return ['Type', 'Rang', 'Nom', 'Departement', 'Note', 'Max', '%'];
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['--- TOP EMPLOYES ---', '', '', '', '', '', ''];
        foreach ($this->data['top_performers']['employees'] ?? [] as $emp) {
            $rows[] = [
                'Employe',
                $emp['rank'],
                $emp['name'],
                $emp['department'],
                $emp['score'],
                $emp['max_score'],
                $emp['percentage'].'%',
            ];
        }

        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = ['--- TOP STAGIAIRES ---', '', '', '', '', '', ''];
        foreach ($this->data['top_performers']['interns'] ?? [] as $intern) {
            $rows[] = [
                'Stagiaire',
                $intern['rank'],
                $intern['name'],
                $intern['department'],
                $intern['score'],
                $intern['max_score'],
                $intern['percentage'].'%',
            ];
        }

        if (count($rows) <= 3) {
            $rows[] = ['Aucune donnee disponible', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'F59E0B']]],
        ];
    }
}

class BestAttendanceSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Meilleure Assiduite';
    }

    public function headings(): array
    {
        return ['Rang', 'Nom', 'Departement', 'Presences', 'A l\'heure', 'Retards', 'Ponctualite %', 'Heures totales'];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['best_attendance'] ?? [] as $att) {
            $rows[] = [
                $att['rank'],
                $att['name'],
                $att['department'],
                $att['presence_count'],
                $att['on_time_count'],
                $att['late_count'],
                $att['punctuality_rate'].'%',
                $att['total_hours'].'h',
            ];
        }

        if (empty($rows)) {
            $rows[] = ['Aucune donnée disponible', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '3B82F6']]],
        ];
    }
}

class LatecomersSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Retardataires';
    }

    public function headings(): array
    {
        return ['Rang', 'Employe', 'Departement', 'Nombre de retards', 'Moyenne (min)', 'Total (min)'];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['latecomers'] ?? [] as $latecomer) {
            $rows[] = [
                $latecomer['rank'],
                $latecomer['name'],
                $latecomer['department'],
                $latecomer['count'],
                $latecomer['avg_minutes'],
                $latecomer['count'] * $latecomer['avg_minutes'],
            ];
        }

        if (empty($rows)) {
            $rows[] = ['Aucun retard ce mois', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'EF4444']]],
        ];
    }
}

class TasksSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Taches';
    }

    public function headings(): array
    {
        return ['ID', 'Titre', 'Assigne a', 'Departement', 'Priorite', 'Statut', 'Echeance', 'Creee le'];
    }

    public function array(): array
    {
        $rows = [];
        $tasks = Task::with(['user', 'user.department'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('created_at')
            ->get();

        foreach ($tasks as $task) {
            $rows[] = [
                $task->id,
                $task->titre,
                $task->user->name ?? '-',
                $task->user->department->name ?? '-',
                ucfirst($task->priorite ?? '-'),
                ucfirst($task->statut ?? '-'),
                $task->date_fin?->format('d/m/Y') ?? '-',
                $task->created_at?->format('d/m/Y') ?? '-',
            ];
        }

        if (empty($rows)) {
            $rows[] = ['Aucune tâche ce mois', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '6366F1']]],
        ];
    }
}

class LeavesSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Conges';
    }

    public function headings(): array
    {
        return ['Employe', 'Departement', 'Type', 'Date debut', 'Date fin', 'Duree (jours)', 'Statut', 'Demande le'];
    }

    public function array(): array
    {
        $rows = [];
        $leaves = Leave::with(['user', 'user.department'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('created_at')
            ->get();

        foreach ($leaves as $leave) {
            $rows[] = [
                $leave->user->name ?? '-',
                $leave->user->department->name ?? '-',
                $leave->type_label ?? $leave->type,
                $leave->date_debut?->format('d/m/Y') ?? '-',
                $leave->date_fin?->format('d/m/Y') ?? '-',
                $leave->duree ?? 0,
                ucfirst($leave->statut ?? '-'),
                $leave->created_at?->format('d/m/Y') ?? '-',
            ];
        }

        if (empty($rows)) {
            $rows[] = ['Aucune demande ce mois', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '14B8A6']]],
        ];
    }
}
