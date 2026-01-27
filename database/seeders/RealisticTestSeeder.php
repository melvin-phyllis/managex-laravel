<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Contract;
use App\Models\Presence;
use App\Models\Leave;
use App\Models\Task;
use App\Models\Payroll;
use App\Models\PayrollCountry;
use App\Models\PayrollCountryRule;
use App\Models\Setting;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RealisticTestSeeder extends Seeder
{
    /**
     * 20 employés prédéfinis avec profils réalistes ivoiriens
     */
    private array $employees = [
        // Direction (2)
        ['name' => 'Kouamé Yao', 'email' => 'kouame.yao@managex.ci', 'dept' => 'Direction', 'pos' => 'Directeur Général', 'salary' => 2500000, 'status' => 'married', 'children' => 3, 'gender' => 'male'],
        ['name' => 'Adjoua Koné', 'email' => 'adjoua.kone@managex.ci', 'dept' => 'Direction', 'pos' => 'Assistant de Direction', 'salary' => 450000, 'status' => 'single', 'children' => 0, 'gender' => 'female'],

        // Ressources Humaines (3)
        ['name' => 'Fatou Traoré', 'email' => 'fatou.traore@managex.ci', 'dept' => 'Ressources Humaines', 'pos' => 'DRH', 'salary' => 1200000, 'status' => 'married', 'children' => 2, 'gender' => 'female'],
        ['name' => 'Mamadou Coulibaly', 'email' => 'mamadou.coulibaly@managex.ci', 'dept' => 'Ressources Humaines', 'pos' => 'Chargé RH', 'salary' => 550000, 'status' => 'married', 'children' => 1, 'gender' => 'male'],
        ['name' => 'Awa Diallo', 'email' => 'awa.diallo@managex.ci', 'dept' => 'Ressources Humaines', 'pos' => 'Gestionnaire Paie', 'salary' => 480000, 'status' => 'single', 'children' => 0, 'gender' => 'female'],

        // Finance (4)
        ['name' => 'Ibrahim Bamba', 'email' => 'ibrahim.bamba@managex.ci', 'dept' => 'Finance', 'pos' => 'DAF', 'salary' => 1300000, 'status' => 'married', 'children' => 4, 'gender' => 'male'],
        ['name' => 'Marie-Claire Aka', 'email' => 'marie.aka@managex.ci', 'dept' => 'Finance', 'pos' => 'Chef Comptable', 'salary' => 750000, 'status' => 'married', 'children' => 2, 'gender' => 'female'],
        ['name' => 'Sekou Touré', 'email' => 'sekou.toure@managex.ci', 'dept' => 'Finance', 'pos' => 'Comptable', 'salary' => 420000, 'status' => 'single', 'children' => 0, 'gender' => 'male'],
        ['name' => 'Christelle N\'Guessan', 'email' => 'christelle.nguessan@managex.ci', 'dept' => 'Finance', 'pos' => 'Contrôleur de Gestion', 'salary' => 650000, 'status' => 'married', 'children' => 1, 'gender' => 'female'],

        // Informatique (5)
        ['name' => 'Koffi Assi', 'email' => 'koffi.assi@managex.ci', 'dept' => 'Informatique', 'pos' => 'DSI', 'salary' => 1400000, 'status' => 'married', 'children' => 2, 'gender' => 'male'],
        ['name' => 'Amara Sylla', 'email' => 'amara.sylla@managex.ci', 'dept' => 'Informatique', 'pos' => 'Développeur Senior', 'salary' => 850000, 'status' => 'single', 'children' => 0, 'gender' => 'male'],
        ['name' => 'Aya Kouassi', 'email' => 'aya.kouassi@managex.ci', 'dept' => 'Informatique', 'pos' => 'Développeur Senior', 'salary' => 780000, 'status' => 'married', 'children' => 1, 'gender' => 'female'],
        ['name' => 'Olivier Yao', 'email' => 'olivier.yao@managex.ci', 'dept' => 'Informatique', 'pos' => 'Développeur Junior', 'salary' => 380000, 'status' => 'single', 'children' => 0, 'gender' => 'male'],
        ['name' => 'Sandrine Brou', 'email' => 'sandrine.brou@managex.ci', 'dept' => 'Informatique', 'pos' => 'Support IT', 'salary' => 320000, 'status' => 'single', 'children' => 0, 'gender' => 'female'],

        // Commercial (6)
        ['name' => 'Konan Dje', 'email' => 'konan.dje@managex.ci', 'dept' => 'Commercial', 'pos' => 'Directeur Commercial', 'salary' => 1100000, 'status' => 'married', 'children' => 3, 'gender' => 'male'],
        ['name' => 'Aminata Sanogo', 'email' => 'aminata.sanogo@managex.ci', 'dept' => 'Commercial', 'pos' => 'Commercial Senior', 'salary' => 620000, 'status' => 'married', 'children' => 2, 'gender' => 'female'],
        ['name' => 'Patrick Konaté', 'email' => 'patrick.konate@managex.ci', 'dept' => 'Commercial', 'pos' => 'Commercial Senior', 'salary' => 580000, 'status' => 'single', 'children' => 0, 'gender' => 'male'],
        ['name' => 'Aïcha Ouattara', 'email' => 'aicha.ouattara@managex.ci', 'dept' => 'Commercial', 'pos' => 'Commercial Junior', 'salary' => 350000, 'status' => 'single', 'children' => 0, 'gender' => 'female'],
        ['name' => 'Jean-Marc Sery', 'email' => 'jeanmarc.sery@managex.ci', 'dept' => 'Commercial', 'pos' => 'Commercial Junior', 'salary' => 320000, 'status' => 'married', 'children' => 1, 'gender' => 'male'],
        ['name' => 'Lou Zadi', 'email' => 'lou.zadi@managex.ci', 'dept' => 'Commercial', 'pos' => 'Commercial Junior', 'salary' => 280000, 'status' => 'single', 'children' => 0, 'gender' => 'female'],
    ];

    /**
     * Départements et postes
     */
    private array $departments = [
        'Direction' => ['color' => '#1E40AF', 'positions' => ['Directeur Général', 'Assistant de Direction']],
        'Ressources Humaines' => ['color' => '#059669', 'positions' => ['DRH', 'Chargé RH', 'Gestionnaire Paie']],
        'Finance' => ['color' => '#D97706', 'positions' => ['DAF', 'Chef Comptable', 'Comptable', 'Contrôleur de Gestion']],
        'Informatique' => ['color' => '#2563EB', 'positions' => ['DSI', 'Développeur Senior', 'Développeur Junior', 'Support IT']],
        'Commercial' => ['color' => '#DC2626', 'positions' => ['Directeur Commercial', 'Commercial Senior', 'Commercial Junior']],
    ];

    /**
     * Tâches par département
     */
    private array $tasksByDept = [
        'Direction' => ['Réunion stratégique', 'Validation budget', 'Comité de direction', 'Revue mensuelle', 'Planification annuelle'],
        'Ressources Humaines' => ['Entretiens recrutement', 'Formation équipe', 'Gestion congés', 'Évaluation annuelle', 'Mise à jour procédures'],
        'Finance' => ['Clôture mensuelle', 'Rapprochement bancaire', 'Déclarations fiscales', 'Audit interne', 'Prévisions trésorerie'],
        'Informatique' => ['Développement feature', 'Correction bugs', 'Maintenance serveurs', 'Support utilisateurs', 'Documentation technique'],
        'Commercial' => ['Prospection clients', 'Négociation contrat', 'Suivi commandes', 'Rapport commercial', 'Formation produits'],
    ];

    private ?PayrollCountry $civCountry = null;
    private array $positionIds = [];
    private array $employeeLeaves = [];

    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeding réaliste (20 employés, 6 mois)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Nettoyer les tables
        $this->truncateTables();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Admin
        $this->createAdmin();

        // 2. Départements & Postes
        $this->createDepartmentsAndPositions();

        // 3. Configuration Paie CIV
        $this->createPayrollSettings();

        // 4. Paramètres généraux
        $this->createGeneralSettings();

        // 5. Employés
        $this->createEmployees();

        // 6. Données sur 6 mois
        $this->createSixMonthsData();

        // 7. Sondages
        $this->createSurveys();

        $this->command->info('✅ Seeding terminé avec succès!');
        $this->displaySummary();
    }

    private function truncateTables(): void
    {
        $tables = [
            'survey_responses', 'survey_questions', 'surveys',
            'payrolls', 'tasks', 'leaves', 'presences',
            'contracts', 'users', 'positions', 'departments',
            'payroll_country_rules', 'payroll_countries', 'settings'
        ];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
    }

    private function createAdmin(): void
    {
        $this->command->info('👤 Création administrateur...');

        User::create([
            'name' => 'Admin ManageX',
            'email' => 'admin@managex.ci',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'telephone' => '+225 07 00 00 00 00',
            'address' => 'Plateau, Abidjan',
            'city' => 'Abidjan',
            'country' => 'Côte d\'Ivoire',
            'gender' => 'male',
            'date_of_birth' => Carbon::now()->subYears(45),
            'hire_date' => Carbon::now()->subYears(5),
            'marital_status' => 'married',
            'children_count' => 2,
            'number_of_parts' => 3,
            'email_verified_at' => now(),
        ]);
    }

    private function createDepartmentsAndPositions(): void
    {
        $this->command->info('🏢 Création départements et postes...');

        foreach ($this->departments as $deptName => $deptData) {
            $department = Department::create([
                'name' => $deptName,
                'description' => 'Département ' . $deptName,
                'color' => $deptData['color'],
                'is_active' => true,
            ]);

            foreach ($deptData['positions'] as $positionName) {
                $position = Position::create([
                    'department_id' => $department->id,
                    'name' => $positionName,
                    'description' => 'Poste de ' . $positionName,
                    'is_active' => true,
                ]);

                $this->positionIds[$deptName][$positionName] = $position->id;
            }
        }
    }

    private function createPayrollSettings(): void
    {
        $this->command->info('💰 Configuration paie CIV...');

        // Créer le pays
        $this->civCountry = PayrollCountry::create([
            'code' => 'CIV',
            'name' => 'Côte d\'Ivoire',
            'currency' => 'XOF',
            'currency_symbol' => 'FCFA',
            'is_active' => true,
        ]);

        // Règles IS (1.2%)
        PayrollCountryRule::create([
            'country_id' => $this->civCountry->id,
            'code' => 'IS',
            'label' => 'Impôt sur Salaire',
            'rule_type' => 'tax',
            'rule_category' => 'employee',
            'calculation_type' => 'percentage',
            'rate' => 1.2,
            'base_field' => 'taxable_gross',
            'is_mandatory' => true,
            'is_visible_on_payslip' => true,
            'display_order' => 10,
        ]);

        // Règles CN (barème progressif)
        PayrollCountryRule::create([
            'country_id' => $this->civCountry->id,
            'code' => 'CN',
            'label' => 'Contribution Nationale',
            'rule_type' => 'tax',
            'rule_category' => 'employee',
            'calculation_type' => 'bracket',
            'brackets' => [
                ['min' => 0, 'max' => 50000, 'rate' => 0.00],
                ['min' => 50000, 'max' => 130000, 'rate' => 0.015],
                ['min' => 130000, 'max' => 200000, 'rate' => 0.05],
                ['min' => 200000, 'max' => null, 'rate' => 0.10],
            ],
            'base_field' => 'taxable_gross',
            'is_mandatory' => true,
            'is_visible_on_payslip' => true,
            'display_order' => 20,
        ]);

        // Règles IGR (quotient familial)
        PayrollCountryRule::create([
            'country_id' => $this->civCountry->id,
            'code' => 'IGR',
            'label' => 'Impôt Général sur le Revenu',
            'rule_type' => 'tax',
            'rule_category' => 'employee',
            'calculation_type' => 'bracket',
            'brackets' => [
                ['min' => 0, 'max' => 25000, 'rate' => 0.00, 'deduction' => 0],
                ['min' => 25000, 'max' => 45583, 'rate' => 0.10, 'deduction' => 2500],
                ['min' => 45583, 'max' => 81583, 'rate' => 0.15, 'deduction' => 4779],
                ['min' => 81583, 'max' => 126583, 'rate' => 0.20, 'deduction' => 8858],
                ['min' => 126583, 'max' => 220333, 'rate' => 0.25, 'deduction' => 15187],
                ['min' => 220333, 'max' => 389083, 'rate' => 0.35, 'deduction' => 37220],
                ['min' => 389083, 'max' => 842166, 'rate' => 0.45, 'deduction' => 76128],
                ['min' => 842166, 'max' => null, 'rate' => 0.60, 'deduction' => 202553],
            ],
            'base_field' => 'net_before_igr',
            'is_mandatory' => true,
            'is_visible_on_payslip' => true,
            'display_order' => 30,
        ]);

        // Règles CNPS (6.3% plafonné)
        PayrollCountryRule::create([
            'country_id' => $this->civCountry->id,
            'code' => 'CNPS',
            'label' => 'CNPS (Retraite)',
            'rule_type' => 'contribution',
            'rule_category' => 'employee',
            'calculation_type' => 'percentage',
            'rate' => 6.3,
            'ceiling' => 1647315,
            'base_field' => 'gross_salary',
            'is_mandatory' => true,
            'is_visible_on_payslip' => true,
            'display_order' => 40,
        ]);

        // Définir comme pays par défaut
        Setting::set('payroll_country_id', $this->civCountry->id, 'integer', 'payroll');
    }

    private function createGeneralSettings(): void
    {
        $this->command->info('⚙️ Paramètres généraux...');

        Setting::set('work_start_time', '08:00', 'time', 'presence');
        Setting::set('work_end_time', '17:00', 'time', 'presence');
        Setting::set('break_start_time', '12:00', 'time', 'presence');
        Setting::set('break_end_time', '13:00', 'time', 'presence');
        Setting::set('late_tolerance_minutes', 15, 'integer', 'presence');
        Setting::set('company_name', 'ManageX CI', 'string', 'company');
        Setting::set('company_address', 'Plateau, Abidjan, Côte d\'Ivoire', 'string', 'company');
    }

    private function createEmployees(): void
    {
        $this->command->info('👥 Création des 20 employés...');

        foreach ($this->employees as $index => $emp) {
            // Date d'embauche entre 8 mois et 3 ans
            $hireDate = Carbon::now()->subMonths(rand(8, 36));

            $user = User::create([
                'name' => $emp['name'],
                'email' => $emp['email'],
                'password' => Hash::make('password'),
                'role' => 'employee',
                'telephone' => '+225 0' . rand(1, 9) . ' ' . sprintf('%02d', rand(0, 99)) . ' ' . sprintf('%02d', rand(0, 99)) . ' ' . sprintf('%02d', rand(0, 99)) . ' ' . sprintf('%02d', rand(0, 99)),
                'address' => $this->getRandomAddress(),
                'city' => 'Abidjan',
                'country' => 'Côte d\'Ivoire',
                'gender' => $emp['gender'],
                'date_of_birth' => Carbon::now()->subYears(rand(25, 55)),
                'hire_date' => $hireDate,
                'marital_status' => $emp['status'],
                'children_count' => $emp['children'],
                'number_of_parts' => $this->calculateParts($emp['status'], $emp['children']),
                'department_id' => Department::where('name', $emp['dept'])->first()->id,
                'position_id' => $this->positionIds[$emp['dept']][$emp['pos']],
                'email_verified_at' => now(),
            ]);

            // Créer le contrat
            Contract::create([
                'user_id' => $user->id,
                'contract_type' => $emp['salary'] >= 500000 ? 'cdi' : (rand(0, 3) > 0 ? 'cdi' : 'cdd'),
                'start_date' => $hireDate,
                'end_date' => null,
                'base_salary' => $emp['salary'],
                'is_current' => true,
            ]);

            $this->command->info("  → Employé créé: {$emp['name']}");
        }
    }

    private function createSixMonthsData(): void
    {
        $this->command->info('📊 Génération des données sur 6 mois...');

        $employees = User::where('role', 'employee')->get();
        $startMonth = Carbon::now()->subMonths(6)->startOfMonth();

        // D'abord, créer tous les congés
        foreach ($employees as $employee) {
            $this->createLeaves($employee, $startMonth);
        }

        // Ensuite, pour chaque mois
        for ($month = 0; $month < 6; $month++) {
            $currentMonth = $startMonth->copy()->addMonths($month);
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();

            $this->command->info("  📅 " . $currentMonth->translatedFormat('F Y'));

            foreach ($employees as $employee) {
                // Présences
                $this->createMonthlyPresences($employee, $monthStart, $monthEnd);

                // Tâches
                $this->createMonthlyTasks($employee, $currentMonth);

                // Paie (sauf mois en cours si pas encore fini)
                if ($monthEnd->isPast() || $monthEnd->isToday()) {
                    $this->createPayroll($employee, $currentMonth->month, $currentMonth->year);
                }
            }
        }
    }

    private function createLeaves(User $employee, Carbon $startMonth): void
    {
        $this->employeeLeaves[$employee->id] = [];

        // 2-3 congés sur 6 mois
        $numLeaves = rand(2, 3);

        for ($i = 0; $i < $numLeaves; $i++) {
            $monthOffset = rand(0, 5);
            $leaveMonth = $startMonth->copy()->addMonths($monthOffset);
            $startDay = rand(1, 20);
            $duration = rand(1, 7);

            $leaveStart = $leaveMonth->copy()->day($startDay);

            // S'assurer que ce n'est pas un weekend
            while ($leaveStart->isWeekend()) {
                $leaveStart->addDay();
            }

            $leaveEnd = $leaveStart->copy()->addDays($duration);

            // Type de congé
            $rand = rand(1, 100);
            $type = $rand <= 70 ? 'conge' : ($rand <= 90 ? 'maladie' : 'autre');

            // Statut
            $statusRand = rand(1, 100);
            $statut = $statusRand <= 80 ? 'approved' : ($statusRand <= 90 ? 'pending' : 'rejected');

            $leave = Leave::create([
                'user_id' => $employee->id,
                'type' => $type,
                'date_debut' => $leaveStart,
                'date_fin' => $leaveEnd,
                'motif' => $this->getLeaveReason($type),
                'statut' => $statut,
            ]);

            // Stocker les congés approuvés pour exclure des présences
            if ($statut === 'approved') {
                $this->employeeLeaves[$employee->id][] = [
                    'start' => $leaveStart->format('Y-m-d'),
                    'end' => $leaveEnd->format('Y-m-d'),
                ];
            }
        }
    }

    private function createMonthlyPresences(User $employee, Carbon $monthStart, Carbon $monthEnd): void
    {
        $current = $monthStart->copy();
        $today = Carbon::today();

        while ($current <= $monthEnd && $current <= $today) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // Skip si en congé approuvé
            if ($this->isOnLeave($employee->id, $current)) {
                $current->addDay();
                continue;
            }

            // 95% de présence
            if (rand(1, 100) <= 95) {
                $scheduledStart = '08:00';
                $scheduledEnd = '17:00';

                // Heure d'arrivée
                $arrivalMinutes = rand(0, 40);
                $isLate = $arrivalMinutes > 15;
                $lateMinutes = $isLate ? $arrivalMinutes - 15 : 0;

                $checkIn = $current->copy()->setTime(8, $arrivalMinutes, 0);

                // Heure de départ
                $departureMinutes = rand(0, 45);
                $isEarlyDeparture = rand(1, 100) <= 10;
                $earlyMinutes = 0;

                if ($isEarlyDeparture) {
                    $earlyMinutes = rand(15, 60);
                    $checkOut = $current->copy()->setTime(17, 0, 0)->subMinutes($earlyMinutes);
                } else {
                    $checkOut = $current->copy()->setTime(17, $departureMinutes, 0);
                }

                Presence::create([
                    'user_id' => $employee->id,
                    'date' => $current->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'is_late' => $isLate,
                    'late_minutes' => $lateMinutes,
                    'is_early_departure' => $isEarlyDeparture,
                    'early_departure_minutes' => $earlyMinutes,
                    'scheduled_start' => $scheduledStart,
                    'scheduled_end' => $scheduledEnd,
                ]);
            }

            $current->addDay();
        }
    }

    private function isOnLeave(int $employeeId, Carbon $date): bool
    {
        if (!isset($this->employeeLeaves[$employeeId])) {
            return false;
        }

        foreach ($this->employeeLeaves[$employeeId] as $leave) {
            if ($date->format('Y-m-d') >= $leave['start'] && $date->format('Y-m-d') <= $leave['end']) {
                return true;
            }
        }

        return false;
    }

    private function createMonthlyTasks(User $employee, Carbon $month): void
    {
        $department = $employee->department->name;
        $tasks = $this->tasksByDept[$department] ?? $this->tasksByDept['Commercial'];
        $numTasks = rand(3, 5);

        for ($i = 0; $i < $numTasks; $i++) {
            $taskTitle = $tasks[array_rand($tasks)];
            $startDay = rand(1, 10);
            $endDay = rand(15, 28);

            $dateDebut = $month->copy()->day($startDay);
            $dateFin = $month->copy()->day($endDay);

            // Déterminer statut et progression
            $isPast = $dateFin->isPast();

            if ($isPast) {
                $isCompleted = rand(1, 100) <= 85;
                $statut = $isCompleted ? 'completed' : 'approved';
                $progression = $isCompleted ? 100 : rand(60, 95);
            } else {
                $statut = 'pending';
                $progression = rand(0, 50);
            }

            // Priorité
            $prioRand = rand(1, 100);
            $priorite = $prioRand <= 30 ? 'low' : ($prioRand <= 80 ? 'medium' : 'high');

            Task::create([
                'user_id' => $employee->id,
                'titre' => $taskTitle . ' - ' . $month->translatedFormat('M Y'),
                'description' => 'Tâche assignée pour ' . $month->translatedFormat('F Y'),
                'statut' => $statut,
                'progression' => $progression,
                'priorite' => $priorite,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
            ]);
        }
    }

    private function createPayroll(User $employee, int $month, int $year): void
    {
        $contract = $employee->currentContract;
        if (!$contract) return;

        $baseSalary = $contract->base_salary;
        $transport = 25000; // Indemnité transport standard
        $housing = $baseSalary >= 800000 ? rand(50000, 150000) : 0;

        $grossSalary = $baseSalary + $transport + $housing;

        // Brut imposable (transport exonéré jusqu'à 25000)
        $taxableGross = $baseSalary + $housing;

        // Calculs fiscaux CIV
        $cnps = $this->calculateCNPS($grossSalary);
        $is = floor($taxableGross * 0.012);
        $cn = $this->calculateCN($taxableGross);

        // Base IGR = Brut imposable - IS - CN - CNPS
        $igrBase = $taxableGross - $is - $cn - $cnps;
        $fiscalParts = $employee->number_of_parts ?? 1;
        $igr = $this->calculateIGR($igrBase, $fiscalParts);

        $totalDeductions = $is + $cn + $igr + $cnps;
        $netSalary = $grossSalary - $totalDeductions;

        Payroll::create([
            'user_id' => $employee->id,
            'contract_id' => $contract->id,
            'country_id' => $this->civCountry?->id,
            'mois' => $month,
            'annee' => $year,
            'statut' => 'paid',
            'workflow_status' => 'validated',
            'gross_salary' => $grossSalary,
            'transport_allowance' => $transport,
            'housing_allowance' => $housing,
            'taxable_gross' => $taxableGross,
            'tax_is' => floor($is),
            'tax_cn' => floor($cn),
            'tax_igr' => floor($igr),
            'cnps_employee' => floor($cnps),
            'total_deductions' => floor($totalDeductions),
            'net_salary' => floor($netSalary),
            'fiscal_parts' => $fiscalParts,
            'validated_at' => Carbon::create($year, $month, 28),
            'validated_by' => 1,
        ]);
    }

    private function createSurveys(): void
    {
        $this->command->info('📋 Création des sondages...');

        $admin = User::where('role', 'admin')->first();
        $employees = User::where('role', 'employee')->get();

        // Sondage 1: Satisfaction générale
        $survey1 = Survey::create([
            'admin_id' => $admin->id,
            'titre' => 'Enquête de satisfaction 2025',
            'description' => 'Nous souhaitons connaître votre niveau de satisfaction au travail.',
            'is_active' => true,
            'date_limite' => Carbon::now()->addMonths(1),
        ]);

        $questions1 = [
            ['question' => 'Comment évaluez-vous l\'ambiance de travail ?', 'type' => 'rating', 'options' => null],
            ['question' => 'Êtes-vous satisfait de votre rémunération ?', 'type' => 'yesno', 'options' => null],
            ['question' => 'Recommanderiez-vous l\'entreprise ?', 'type' => 'choice', 'options' => ['Oui, certainement', 'Probablement', 'Probablement pas', 'Non']],
            ['question' => 'Qu\'est-ce qui pourrait être amélioré ?', 'type' => 'text', 'options' => null],
            ['question' => 'Notez la communication interne', 'type' => 'rating', 'options' => null],
        ];

        foreach ($questions1 as $index => $q) {
            SurveyQuestion::create([
                'survey_id' => $survey1->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $q['options'],
                'is_required' => true,
                'ordre' => $index + 1,
            ]);
        }

        // Réponses au sondage 1 (80% des employés)
        foreach ($employees->take(16) as $employee) {
            foreach ($survey1->questions as $question) {
                SurveyResponse::create([
                    'survey_question_id' => $question->id,
                    'user_id' => $employee->id,
                    'reponse' => $this->generateSurveyResponse($question),
                ]);
            }
        }

        // Sondage 2: Conditions de travail
        $survey2 = Survey::create([
            'admin_id' => $admin->id,
            'titre' => 'Conditions de travail',
            'description' => 'Évaluation des conditions de travail et des équipements.',
            'is_active' => true,
            'date_limite' => Carbon::now()->addWeeks(2),
        ]);

        $questions2 = [
            ['question' => 'Votre bureau est-il bien équipé ?', 'type' => 'yesno', 'options' => null],
            ['question' => 'Qualité du matériel informatique', 'type' => 'rating', 'options' => null],
            ['question' => 'Préférez-vous le télétravail ?', 'type' => 'choice', 'options' => ['Oui, 100%', 'Hybride (2-3j)', 'Présentiel', 'Indifférent']],
            ['question' => 'Suggestions d\'amélioration', 'type' => 'text', 'options' => null],
        ];

        foreach ($questions2 as $index => $q) {
            SurveyQuestion::create([
                'survey_id' => $survey2->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $q['options'],
                'is_required' => $q['type'] !== 'text',
                'ordre' => $index + 1,
            ]);
        }

        // Réponses au sondage 2 (50% des employés)
        foreach ($employees->take(10) as $employee) {
            foreach ($survey2->questions as $question) {
                SurveyResponse::create([
                    'survey_question_id' => $question->id,
                    'user_id' => $employee->id,
                    'reponse' => $this->generateSurveyResponse($question),
                ]);
            }
        }
    }

    // ===== HELPERS =====

    private function calculateParts(string $status, int $children): float
    {
        $parts = ($status === 'married') ? 2.0 : 1.0;
        $parts += $children * 0.5;
        return min($parts, 5.0);
    }

    private function calculateCN(float $gross): float
    {
        $tax = 0;
        $brackets = [
            [0, 50000, 0.00],
            [50000, 130000, 0.015],
            [130000, 200000, 0.05],
            [200000, PHP_INT_MAX, 0.10],
        ];

        foreach ($brackets as [$min, $max, $rate]) {
            if ($gross > $min) {
                $taxableInBracket = min($gross, $max) - $min;
                $tax += $taxableInBracket * $rate;
            }
        }

        return floor($tax);
    }

    private function calculateIGR(float $base, float $parts): float
    {
        if ($parts <= 0) $parts = 1;
        $Q = $base / $parts;

        $table = [
            [0, 25000, 0.00, 0],
            [25000, 45583, 0.10, 2500],
            [45583, 81583, 0.15, 4779],
            [81583, 126583, 0.20, 8858],
            [126583, 220333, 0.25, 15187],
            [220333, 389083, 0.35, 37220],
            [389083, 842166, 0.45, 76128],
            [842166, PHP_INT_MAX, 0.60, 202553],
        ];

        foreach ($table as [$min, $max, $rate, $ded]) {
            if ($Q > $min && $Q <= $max) {
                return max(0, floor(($Q * $rate - $ded) * $parts));
            }
        }

        // Si au-delà de la dernière tranche
        if ($Q > 842166) {
            return max(0, floor(($Q * 0.60 - 202553) * $parts));
        }

        return 0;
    }

    private function calculateCNPS(float $gross): float
    {
        $ceiling = 1647315;
        $rate = 0.063;
        return floor(min($gross, $ceiling) * $rate);
    }

    private function getRandomAddress(): string
    {
        $quartiers = ['Cocody', 'Plateau', 'Marcory', 'Treichville', 'Yopougon', 'Abobo', 'Adjamé', 'Bingerville'];
        return $quartiers[array_rand($quartiers)] . ', Abidjan';
    }

    private function getLeaveReason(string $type): string
    {
        $reasons = [
            'conge' => ['Congés annuels', 'Vacances familiales', 'Repos bien mérité', 'Voyage personnel'],
            'maladie' => ['Grippe', 'Consultation médicale', 'Fatigue', 'Rendez-vous médical'],
            'autre' => ['Événement familial', 'Démarches administratives', 'Raisons personnelles'],
        ];

        return $reasons[$type][array_rand($reasons[$type])];
    }

    private function generateSurveyResponse(SurveyQuestion $question): string
    {
        return match ($question->type) {
            'rating' => (string) rand(3, 5),
            'yesno' => rand(0, 1) ? 'Oui' : 'Non',
            'choice' => $question->options[array_rand($question->options)],
            'text' => $this->getRandomTextResponse(),
            default => '',
        };
    }

    private function getRandomTextResponse(): string
    {
        $responses = [
            'Améliorer la communication entre les équipes.',
            'Plus de formations professionnelles.',
            'Meilleure climatisation dans les bureaux.',
            'Organiser plus d\'événements d\'équipe.',
            'RAS, tout est bien.',
            'Possibilité de télétravail.',
            'Mise à jour des équipements informatiques.',
        ];

        return $responses[array_rand($responses)];
    }

    private function displaySummary(): void
    {
        $this->command->info('');
        $this->command->info('📈 Résumé des données créées:');
        $this->command->table(
            ['Entité', 'Quantité'],
            [
                ['Administrateur', 1],
                ['Employés', User::where('role', 'employee')->count()],
                ['Départements', Department::count()],
                ['Postes', Position::count()],
                ['Contrats', Contract::count()],
                ['Présences', Presence::count()],
                ['Congés', Leave::count()],
                ['Tâches', Task::count()],
                ['Fiches de paie', Payroll::count()],
                ['Sondages', Survey::count()],
                ['Réponses sondages', SurveyResponse::count()],
            ]
        );
        $this->command->info('');
        $this->command->info('🔐 Identifiants de connexion:');
        $this->command->info('   Admin: admin@managex.ci / password');
        $this->command->info('   Employé: kouame.yao@managex.ci / password');
    }
}
