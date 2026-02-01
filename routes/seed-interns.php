<?php

use App\Models\InternEvaluation;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

Route::get('/seed-interns', function () {
    try {
        // Get or create a position for interns
        $internPosition = Position::firstOrCreate(
            ['name' => 'Stagiaire'],
            ['description' => 'Poste de stagiaire']
        );

        // Get first department
        $department = Department::first();
        if (!$department) {
            return response()->json(['error' => 'No department found']);
        }

        // Get a tutor
        $tutor = User::where('role', 'admin')->first();
        if (!$tutor) {
            return response()->json(['error' => 'No admin/tutor found']);
        }

        // Create interns
        $interns = [
            ['name' => 'Koné Aminata', 'email' => 'aminata.kone@stagiaire.managex.com'],
            ['name' => 'Traoré Ibrahim', 'email' => 'ibrahim.traore@stagiaire.managex.com'],
            ['name' => 'Kouassi Marie', 'email' => 'marie.kouassi@stagiaire.managex.com'],
            ['name' => 'Diallo Moussa', 'email' => 'moussa.diallo@stagiaire.managex.com'],
            ['name' => 'Bamba Fatou', 'email' => 'fatou.bamba@stagiaire.managex.com'],
        ];

        $created = [];
        foreach ($interns as $index => $data) {
            $intern = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'employee',
                    'status' => 'active',
                    'department_id' => $department->id,
                    'position_id' => $internPosition->id,
                    'hire_date' => now()->subMonths(rand(1, 3)),
                    'employee_id' => 'STG-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'contract_type' => 'stage',
                    'supervisor_id' => $tutor->id,
                ]
            );
            $created[] = $intern->id;
        }

        // Create evaluations
        $evalCount = 0;
        foreach (User::whereIn('id', $created)->get() as $index => $intern) {
            for ($week = 0; $week < rand(4, 8); $week++) {
                $weekStart = Carbon::now()->subWeeks($week)->startOfWeek();
                
                if (InternEvaluation::where('intern_id', $intern->id)->where('week_start', $weekStart)->exists()) {
                    continue;
                }

                $baseScore = 1.5 + ($index * 0.15);
                InternEvaluation::create([
                    'intern_id' => $intern->id,
                    'tutor_id' => $tutor->id,
                    'week_start' => $weekStart,
                    'discipline_score' => min(2.5, max(0.5, $baseScore + rand(-5, 5) / 10)),
                    'behavior_score' => min(2.5, max(0.5, $baseScore + rand(-5, 5) / 10)),
                    'skills_score' => min(2.5, max(0.5, $baseScore + rand(-3, 7) / 10)),
                    'communication_score' => min(2.5, max(0.5, $baseScore + rand(-5, 5) / 10)),
                    'discipline_comment' => 'Bon respect des horaires.',
                    'behavior_comment' => 'Attitude professionnelle.',
                    'skills_comment' => 'Bonne progression.',
                    'communication_comment' => 'Communication claire.',
                    'general_comment' => 'Semaine positive.',
                    'objectives_next_week' => 'Continuer les efforts.',
                    'status' => 'submitted',
                    'submitted_at' => $weekStart->copy()->addDays(5),
                ]);
                $evalCount++;
            }
        }

        return response()->json([
            'success' => true,
            'interns_created' => count($created),
            'evaluations_created' => $evalCount,
            'intern_ids' => $created,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});
