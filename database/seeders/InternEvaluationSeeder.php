<?php

namespace Database\Seeders;

use App\Models\InternEvaluation;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting intern evaluation seeder...');

        // Get or create a position for interns
        $internPosition = Position::firstOrCreate(
            ['name' => 'Stagiaire'],
            ['description' => 'Poste de stagiaire']
        );
        $this->command->info('Position created/found: ' . $internPosition->id);

        // Get first department or create one
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'name' => 'Ressources Humaines',
                'description' => 'Département RH'
            ]);
        }
        $this->command->info('Department found: ' . $department->id);

        // Get a tutor (admin or any existing employee)
        $tutor = User::where('role', 'admin')->first() 
            ?? User::where('role', 'employee')->first();

        if (!$tutor) {
            $this->command->error('Aucun tuteur disponible. Créez d\'abord un admin ou employé.');
            return;
        }
        $this->command->info('Tutor found: ' . $tutor->id . ' - ' . $tutor->name);

        // Create test interns using DB insert instead of Eloquent
        $internEmails = [
            ['name' => 'Koné Aminata', 'email' => 'aminata.kone@stagiaire.managex.com'],
            ['name' => 'Traoré Ibrahim', 'email' => 'ibrahim.traore@stagiaire.managex.com'],
            ['name' => 'Kouassi Marie', 'email' => 'marie.kouassi@stagiaire.managex.com'],
            ['name' => 'Diallo Moussa', 'email' => 'moussa.diallo@stagiaire.managex.com'],
            ['name' => 'Bamba Fatou', 'email' => 'fatou.bamba@stagiaire.managex.com'],
        ];

        $createdInterns = [];

        foreach ($internEmails as $index => $internData) {
            $this->command->info('Creating intern: ' . $internData['name']);
            
            // Check if user exists
            $intern = User::where('email', $internData['email'])->first();
            
            if (!$intern) {
                // Create using DB facade to avoid any model issues
                $internId = DB::table('users')->insertGetId([
                    'name' => $internData['name'],
                    'email' => $internData['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'employee',
                    'status' => 'active',
                    'department_id' => $department->id,
                    'position_id' => $internPosition->id,
                    'hire_date' => now()->subMonths(rand(1, 3)),
                    'employee_id' => 'STG-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'contract_type' => 'stage',
                    'supervisor_id' => $tutor->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $intern = User::find($internId);
            } else {
                // Update existing
                $intern->update([
                    'contract_type' => 'stage',
                    'supervisor_id' => $tutor->id,
                ]);
            }

            $createdInterns[] = $intern;
            $this->command->info('  -> Intern ID: ' . $intern->id);
        }

        $this->command->info('✅ ' . count($createdInterns) . ' stagiaires créés/mis à jour');

        // Create evaluations
        $evaluationsCreated = 0;

        foreach ($createdInterns as $index => $intern) {
            $weeksToGenerate = rand(4, 8);
            $this->command->info('Creating ' . $weeksToGenerate . ' evaluations for ' . $intern->name);

            for ($week = 0; $week < $weeksToGenerate; $week++) {
                $weekStart = Carbon::now()->subWeeks($week)->startOfWeek();

                // Skip if evaluation already exists
                $exists = InternEvaluation::where('intern_id', $intern->id)
                    ->where('week_start', $weekStart)
                    ->exists();
                    
                if ($exists) {
                    continue;
                }

                // Generate scores
                $baseScore = 1.5 + ($index * 0.15);
                $weekBonus = $week * 0.05;

                InternEvaluation::create([
                    'intern_id' => $intern->id,
                    'tutor_id' => $tutor->id,
                    'week_start' => $weekStart,
                    'discipline_score' => min(2.5, max(0.5, $baseScore + $weekBonus + (rand(-5, 5) / 10))),
                    'behavior_score' => min(2.5, max(0.5, $baseScore + $weekBonus + (rand(-5, 5) / 10))),
                    'skills_score' => min(2.5, max(0.5, $baseScore + $weekBonus + (rand(-3, 7) / 10))),
                    'communication_score' => min(2.5, max(0.5, $baseScore + $weekBonus + (rand(-5, 5) / 10))),
                    'discipline_comment' => 'Bon respect des horaires.',
                    'behavior_comment' => 'Attitude professionnelle.',
                    'skills_comment' => 'Bonne progression.',
                    'communication_comment' => 'Communication claire.',
                    'general_comment' => 'Semaine positive.',
                    'objectives_next_week' => 'Continuer sur cette lancée.',
                    'status' => 'submitted',
                    'submitted_at' => $weekStart->copy()->addDays(rand(5, 7)),
                ]);

                $evaluationsCreated++;
            }
        }

        $this->command->info('✅ ' . $evaluationsCreated . ' évaluations créées');
        $this->command->newLine();
        $this->command->info('📋 Récapitulatif:');
        $this->command->table(
            ['Stagiaire', 'Email', 'Tuteur', 'Évaluations'],
            collect($createdInterns)->map(fn($i) => [
                $i->name,
                $i->email,
                $tutor->name,
                InternEvaluation::where('intern_id', $i->id)->count()
            ])->toArray()
        );
    }
}
