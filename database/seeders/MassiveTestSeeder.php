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
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class MassiveTestSeeder extends Seeder
{
    private $departments = [
        ['name' => 'Direction Générale', 'color' => '#1E40AF', 'positions' => ['Directeur Général', 'Directeur Adjoint', 'Assistant de Direction']],
        ['name' => 'Ressources Humaines', 'color' => '#059669', 'positions' => ['DRH', 'Responsable RH', 'Chargé de recrutement', 'Gestionnaire Paie']],
        ['name' => 'Finance & Comptabilité', 'color' => '#D97706', 'positions' => ['DAF', 'Chef Comptable', 'Comptable', 'Contrôleur de gestion', 'Trésorier']],
        ['name' => 'Commercial', 'color' => '#DC2626', 'positions' => ['Directeur Commercial', 'Chef des ventes', 'Commercial Senior', 'Commercial Junior', 'Téléconseiller']],
        ['name' => 'Marketing', 'color' => '#7C3AED', 'positions' => ['Directeur Marketing', 'Chef de produit', 'Chargé de communication', 'Community Manager', 'Graphiste']],
        ['name' => 'Informatique', 'color' => '#2563EB', 'positions' => ['DSI', 'Chef de projet IT', 'Développeur Senior', 'Développeur Junior', 'Administrateur Système', 'Support IT']],
        ['name' => 'Production', 'color' => '#84CC16', 'positions' => ['Directeur de Production', 'Chef d\'équipe', 'Opérateur', 'Technicien', 'Magasinier']],
        ['name' => 'Logistique', 'color' => '#06B6D4', 'positions' => ['Responsable Logistique', 'Coordinateur', 'Chauffeur', 'Manutentionnaire']],
        ['name' => 'Qualité', 'color' => '#F59E0B', 'positions' => ['Responsable Qualité', 'Inspecteur Qualité', 'Technicien Qualité']],
        ['name' => 'Juridique', 'color' => '#6366F1', 'positions' => ['Directeur Juridique', 'Juriste', 'Paralegal']],
    ];

    private $firstNames = [
        'Kouamé', 'Adjoua', 'Yao', 'Ama', 'Konan', 'Affoué', 'Kouassi', 'Ahou', 'Koffi', 'Akissi',
        'Brou', 'Aya', 'Tra', 'Lou', 'Gba', 'Marie', 'Jean', 'Pierre', 'Paul', 'Jacques',
        'Fatou', 'Aminata', 'Mamadou', 'Ibrahim', 'Oumar', 'Kadiatou', 'Mariame', 'Sekou', 'Moussa', 'Aissatou',
        'Emmanuel', 'Christelle', 'Serge', 'Patricia', 'Roger', 'Sandrine', 'Roland', 'Véronique', 'Martial', 'Clarisse',
        'Gnoan', 'Bleu', 'Zadi', 'Tape', 'Dago', 'Adon', 'Tano', 'Lou', 'Guessan', 'Assi',
        'Olivier', 'Catherine', 'Michel', 'Joséphine', 'Bernard', 'Christine', 'Franck', 'Nicole', 'Alain', 'Monique'
    ];

    private $lastNames = [
        'Kouadio', 'Koné', 'Traoré', 'Coulibaly', 'Diallo', 'Bamba', 'Sylla', 'Touré', 'Sanogo', 'Konaté',
        'Yao', 'Koffi', 'Kouassi', 'Aka', 'Ake', 'Adou', 'Aké', 'Allou', 'Assi', 'Bahi',
        'N\'Guessan', 'N\'Dri', 'N\'Goran', 'N\'Gatta', 'Gnagne', 'Gnamien', 'Goly', 'Gohi', 'Guei', 'Irie',
        'Lago', 'Loua', 'Lou', 'Mahan', 'Mian', 'Okou', 'Ouattara', 'Sery', 'Sess', 'Sie',
        'Tia', 'Tie', 'Toure', 'Yapi', 'Yapo', 'Yeo', 'Zahui', 'Zeze', 'Zie', 'Zoukou'
    ];

    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeding massif...');

        // 1. Créer l'admin
        $this->createAdmin();

        // 2. Créer les départements et postes
        $this->createDepartmentsAndPositions();

        // 3. Créer les paramètres de paie (CIV)
        $this->createPayrollSettings();

        // 4. Créer les paramètres généraux
        $this->createGeneralSettings();

        // 5. Créer 100+ employés
        $this->createEmployees(110);

        // 6. Créer 6 mois de données
        $this->createSixMonthsData();

        $this->command->info('✅ Seeding terminé avec succès!');
    }

    private function createAdmin(): void
    {
        $this->command->info('👤 Création de l\'administrateur...');

        User::create([
            'name' => 'Administrateur ManageX',
            'email' => 'admin@managex.ci',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'telephone' => '+225 07 00 00 00 00',
            'address' => 'Abidjan, Côte d\'Ivoire',
            'gender' => 'male',
            'date_of_birth' => Carbon::now()->subYears(45),
            'hire_date' => Carbon::now()->subYears(5),
            'marital_status' => 'married',
            'children_count' => 3,
            'number_of_parts' => 4,
            'email_verified_at' => now(),
        ]);
    }

    private function createDepartmentsAndPositions(): void
    {
        $this->command->info('🏢 Création des départements et postes...');

        foreach ($this->departments as $deptData) {
            $department = Department::create([
                'name' => $deptData['name'],
                'description' => 'Département ' . $deptData['name'],
                'color' => $deptData['color'],
                'is_active' => true,
            ]);

            foreach ($deptData['positions'] as $positionName) {
                Position::create([
                    'department_id' => $department->id,
                    'name' => $positionName,
                    'description' => 'Poste de ' . $positionName,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function createPayrollSettings(): void
    {
        $this->command->info('💰 Création des paramètres de paie CIV...');

        // Créer le pays Côte d'Ivoire
        $civ = PayrollCountry::create([
            'code' => 'CIV',
            'name' => 'Côte d\'Ivoire',
            'currency_code' => 'XOF',
            'currency_symbol' => 'FCFA',
            'is_active' => true,
        ]);

        // Règles IS
        PayrollCountryRule::create([
            'country_id' => $civ->id,
            'rule_type' => 'tax_is',
            'rule_key' => 'is_rate',
            'rule_value' => ['rate' => 0.012],
            'description' => 'Impôt sur Salaire (1.2%)',
            'is_active' => true,
        ]);

        // Règles CN
        PayrollCountryRule::create([
            'country_id' => $civ->id,
            'rule_type' => 'tax_cn',
            'rule_key' => 'cn_brackets',
            'rule_value' => [
                'brackets' => [
                    ['min' => 0, 'max' => 50000, 'rate' => 0.00],
                    ['min' => 50000, 'max' => 130000, 'rate' => 0.015],
                    ['min' => 130000, 'max' => 200000, 'rate' => 0.05],
                    ['min' => 200000, 'max' => null, 'rate' => 0.10],
                ]
            ],
            'description' => 'Contribution Nationale (barème progressif)',
            'is_active' => true,
        ]);

        // Règles IGR
        PayrollCountryRule::create([
            'country_id' => $civ->id,
            'rule_type' => 'tax_igr',
            'rule_key' => 'igr_table',
            'rule_value' => [
                'brackets' => [
                    ['min' => 0, 'max' => 25000, 'rate' => 0.00, 'deduction' => 0],
                    ['min' => 25000, 'max' => 45583, 'rate' => 0.10, 'deduction' => 2500],
                    ['min' => 45583, 'max' => 81583, 'rate' => 0.15, 'deduction' => 4779],
                    ['min' => 81583, 'max' => 126583, 'rate' => 0.20, 'deduction' => 8858],
                    ['min' => 126583, 'max' => 220333, 'rate' => 0.25, 'deduction' => 15187],
                    ['min' => 220333, 'max' => 389083, 'rate' => 0.35, 'deduction' => 37220],
                    ['min' => 389083, 'max' => 842166, 'rate' => 0.45, 'deduction' => 76128],
                    ['min' => 842166, 'max' => null, 'rate' => 0.60, 'deduction' => 202553],
                ]
            ],
            'description' => 'Impôt Général sur le Revenu',
            'is_active' => true,
        ]);

        // Règles CNPS
        PayrollCountryRule::create([
            'country_id' => $civ->id,
            'rule_type' => 'social_cnps',
            'rule_key' => 'cnps_employee',
            'rule_value' => [
                'rate' => 0.063,
                'ceiling' => 1647315,
            ],
            'description' => 'CNPS Part Salariale (6.3% plafonné)',
            'is_active' => true,
        ]);

        // Définir comme pays par défaut
        Setting::set('payroll_country_id', $civ->id, 'integer', 'payroll');
    }

    private function createGeneralSettings(): void
    {
        $this->command->info('⚙️ Création des paramètres généraux...');

        Setting::set('work_start_time', '08:00', 'time', 'presence');
        Setting::set('work_end_time', '17:00', 'time', 'presence');
        Setting::set('break_start_time', '12:00', 'time', 'presence');
        Setting::set('break_end_time', '13:00', 'time', 'presence');
        Setting::set('late_tolerance_minutes', 15, 'integer', 'presence');
    }

    private function createEmployees(int $count): void
    {
        $this->command->info("👥 Création de {$count} employés...");

        $positions = Position::all();
        $usedEmails = [];

        for ($i = 1; $i <= $count; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $gender = rand(0, 1) ? 'male' : 'female';
            $position = $positions->random();

            // Générer un email unique
            $baseEmail = strtolower($firstName . '.' . $lastName) . '@managex.ci';
            $email = $baseEmail;
            $counter = 1;
            while (in_array($email, $usedEmails)) {
                $email = strtolower($firstName . '.' . $lastName . $counter) . '@managex.ci';
                $counter++;
            }
            $usedEmails[] = $email;

            // Date d'embauche aléatoire (entre 6 mois et 5 ans)
            $hireDate = Carbon::now()->subMonths(rand(6, 60));

            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'employee',
                'telephone' => '+225 0' . rand(1, 9) . ' ' . str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT) . ' ' . str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT) . ' ' . str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT) . ' ' . str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT),
                'address' => 'Abidjan, Côte d\'Ivoire',
                'gender' => $gender,
                'date_of_birth' => Carbon::now()->subYears(rand(22, 55)),
                'hire_date' => $hireDate,
                'marital_status' => rand(0, 1) ? 'married' : 'single',
                'children_count' => rand(0, 5),
                'number_of_parts' => rand(1, 4),
                'department_id' => $position->department_id,
                'position_id' => $position->id,
                'email_verified_at' => now(),
            ]);

            // Créer un contrat
            $baseSalary = $this->getSalaryForPosition($position->name);
            Contract::create([
                'user_id' => $user->id,
                'contract_type' => rand(0, 4) > 0 ? 'CDI' : 'CDD',
                'start_date' => $hireDate,
                'end_date' => null,
                'base_salary' => $baseSalary,
                'is_current' => true,
            ]);

            if ($i % 20 === 0) {
                $this->command->info("  → {$i}/{$count} employés créés");
            }
        }
    }

    private function getSalaryForPosition(string $positionName): int
    {
        $salaryRanges = [
            'Directeur' => [1500000, 3000000],
            'DRH' => [1200000, 2000000],
            'DAF' => [1200000, 2000000],
            'DSI' => [1200000, 2000000],
            'Responsable' => [800000, 1500000],
            'Chef' => [700000, 1200000],
            'Senior' => [500000, 900000],
            'Chargé' => [350000, 600000],
            'Comptable' => [300000, 500000],
            'Développeur' => [400000, 800000],
            'Technicien' => [250000, 400000],
            'Commercial' => [300000, 600000],
            'Opérateur' => [180000, 300000],
            'Junior' => [200000, 350000],
            'Assistant' => [200000, 400000],
            'Chauffeur' => [150000, 250000],
            'default' => [200000, 500000],
        ];

        foreach ($salaryRanges as $keyword => $range) {
            if (stripos($positionName, $keyword) !== false) {
                return rand($range[0], $range[1]);
            }
        }

        return rand($salaryRanges['default'][0], $salaryRanges['default'][1]);
    }

    private function createSixMonthsData(): void
    {
        $this->command->info('📊 Création des données sur 6 mois...');

        $employees = User::where('role', 'employee')->get();
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now();

        // Pour chaque mois
        for ($month = 0; $month < 6; $month++) {
            $currentMonth = $startDate->copy()->addMonths($month);
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();

            $this->command->info("  📅 Traitement de " . $currentMonth->format('F Y') . "...");

            foreach ($employees as $employee) {
                // Présences pour ce mois
                $this->createMonthlyPresences($employee, $monthStart, $monthEnd);

                // Congés occasionnels (10% de chance par mois)
                if (rand(1, 100) <= 10) {
                    $this->createLeave($employee, $monthStart, $monthEnd);
                }

                // Tâches (2-5 par mois)
                $this->createMonthlyTasks($employee, $monthStart, $monthEnd, rand(2, 5));

                // Fiche de paie
                $this->createPayroll($employee, $currentMonth->month, $currentMonth->year);
            }
        }
    }

    private function createMonthlyPresences(User $employee, Carbon $monthStart, Carbon $monthEnd): void
    {
        $current = $monthStart->copy();
        
        while ($current <= $monthEnd && $current <= Carbon::now()) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // 95% de présence
            if (rand(1, 100) <= 95) {
                $checkIn = $current->copy()->setTime(8, rand(0, 30), 0);
                $checkOut = $current->copy()->setTime(17, rand(0, 45), 0);
                
                // 15% de retards
                if (rand(1, 100) <= 15) {
                    $checkIn->addMinutes(rand(5, 60));
                }

                // 10% de départ anticipé
                if (rand(1, 100) <= 10) {
                    $checkOut->subMinutes(rand(15, 90));
                }

                $workedMinutes = $checkOut->diffInMinutes($checkIn) - 60; // -1h pause

                Presence::create([
                    'user_id' => $employee->id,
                    'date' => $current->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'worked_hours' => round($workedMinutes / 60, 2),
                    'status' => $checkIn->format('H:i') > '08:15' ? 'late' : 'present',
                    'is_late' => $checkIn->format('H:i') > '08:15',
                    'late_minutes' => max(0, $checkIn->diffInMinutes($current->copy()->setTime(8, 0, 0))),
                ]);
            }

            $current->addDay();
        }
    }

    private function createLeave(User $employee, Carbon $monthStart, Carbon $monthEnd): void
    {
        $types = ['congé_payé', 'maladie', 'personnel', 'familial'];
        $startDay = rand(1, 20);
        $duration = rand(1, 5);

        $leaveStart = $monthStart->copy()->addDays($startDay);
        $leaveEnd = $leaveStart->copy()->addDays($duration);

        if ($leaveEnd > $monthEnd) {
            $leaveEnd = $monthEnd->copy();
        }

        Leave::create([
            'user_id' => $employee->id,
            'type' => $types[array_rand($types)],
            'start_date' => $leaveStart,
            'end_date' => $leaveEnd,
            'reason' => 'Demande de congé',
            'status' => rand(0, 4) > 0 ? 'approved' : (rand(0, 1) ? 'pending' : 'rejected'),
        ]);
    }

    private function createMonthlyTasks(User $employee, Carbon $monthStart, Carbon $monthEnd, int $count): void
    {
        $taskTitles = [
            'Rapport mensuel', 'Réunion d\'équipe', 'Formation', 'Projet client', 'Mise à jour documentation',
            'Analyse des données', 'Préparation présentation', 'Suivi dossier', 'Évaluation performance',
            'Planification stratégique', 'Review code', 'Test qualité', 'Audit interne', 'Prospection',
            'Négociation contrat', 'Support technique', 'Inventaire', 'Maintenance', 'Livraison',
        ];

        for ($i = 0; $i < $count; $i++) {
            $dueDate = $monthStart->copy()->addDays(rand(5, 28));
            $isCompleted = $dueDate < Carbon::now() && rand(0, 10) > 2;

            Task::create([
                'user_id' => $employee->id,
                'assigned_by' => 1, // Admin
                'title' => $taskTitles[array_rand($taskTitles)] . ' - ' . $monthStart->format('M Y'),
                'description' => 'Tâche assignée pour ' . $monthStart->format('F Y'),
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'status' => $isCompleted ? 'completed' : ($dueDate < Carbon::now() ? 'in_progress' : 'pending'),
                'progress' => $isCompleted ? 100 : rand(0, 90),
                'due_date' => $dueDate,
                'completed_at' => $isCompleted ? $dueDate->copy()->addDays(rand(0, 3)) : null,
            ]);
        }
    }

    private function createPayroll(User $employee, int $month, int $year): void
    {
        $contract = $employee->currentContract;
        if (!$contract) return;

        $baseSalary = $contract->base_salary;
        $transport = rand(0, 1) ? 25000 : 0;
        $taxableGross = $baseSalary;

        // Calculs simplifiés
        $cnps = min($taxableGross, 1647315) * 0.063;
        $is = $taxableGross * 0.012;
        $cn = $this->calculateCN($taxableGross);
        $igrBase = $taxableGross - $is - $cn - $cnps;
        $igr = $this->calculateIGR($igrBase, $employee->number_of_parts ?? 1);

        $totalDeductions = $is + $cn + $igr + $cnps;
        $netSalary = $taxableGross - $totalDeductions + $transport;

        Payroll::create([
            'user_id' => $employee->id,
            'contract_id' => $contract->id,
            'mois' => $month,
            'annee' => $year,
            'statut' => 'validated',
            'workflow_status' => 'validated',
            'gross_salary' => $taxableGross,
            'transport_allowance' => $transport,
            'taxable_gross' => $taxableGross,
            'tax_is' => floor($is),
            'tax_cn' => floor($cn),
            'tax_igr' => floor($igr),
            'cnps_employee' => floor($cnps),
            'total_deductions' => floor($totalDeductions),
            'net_salary' => floor($netSalary),
            'fiscal_parts' => $employee->number_of_parts ?? 1,
            'validated_at' => Carbon::create($year, $month, 28),
            'validated_by' => 1,
        ]);
    }

    private function calculateCN(float $gross): float
    {
        $tax = 0;
        $brackets = [
            ['min' => 0, 'max' => 50000, 'rate' => 0.00],
            ['min' => 50000, 'max' => 130000, 'rate' => 0.015],
            ['min' => 130000, 'max' => 200000, 'rate' => 0.05],
            ['min' => 200000, 'max' => PHP_INT_MAX, 'rate' => 0.10],
        ];

        foreach ($brackets as $bracket) {
            if ($gross > $bracket['min']) {
                $base = min($gross, $bracket['max']) - $bracket['min'];
                $tax += $base * $bracket['rate'];
            }
        }
        return $tax;
    }

    private function calculateIGR(float $base, float $parts): float
    {
        if ($parts <= 0) $parts = 1;
        $Q = $base / $parts;

        $table = [
            ['min' => 0, 'max' => 25000, 'rate' => 0.00, 'ded' => 0],
            ['min' => 25000, 'max' => 45583, 'rate' => 0.10, 'ded' => 2500],
            ['min' => 45583, 'max' => 81583, 'rate' => 0.15, 'ded' => 4779],
            ['min' => 81583, 'max' => 126583, 'rate' => 0.20, 'ded' => 8858],
            ['min' => 126583, 'max' => 220333, 'rate' => 0.25, 'ded' => 15187],
            ['min' => 220333, 'max' => 389083, 'rate' => 0.35, 'ded' => 37220],
            ['min' => 389083, 'max' => 842166, 'rate' => 0.45, 'ded' => 76128],
            ['min' => 842166, 'max' => PHP_INT_MAX, 'rate' => 0.60, 'ded' => 202553],
        ];

        foreach ($table as $bracket) {
            if ($Q > $bracket['min'] && $Q <= $bracket['max']) {
                return max(0, ($Q * $bracket['rate'] - $bracket['ded']) * $parts);
            }
        }
        return 0;
    }
}
