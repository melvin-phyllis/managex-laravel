<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Presence;
use App\Models\Task;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer l'administrateur
        $admin = User::create([
            'name' => 'Administrateur ManageX',
            'email' => 'admin@managex.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'poste' => 'Administrateur Système',
            'telephone' => '+33 1 23 45 67 89',
            'email_verified_at' => now(),
        ]);

        // Créer un employé test avec identifiants connus
        $testEmployee = User::create([
            'name' => 'Jean Dupont',
            'email' => 'employe@managex.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'poste' => 'Développeur Web',
            'telephone' => '+33 6 12 34 56 78',
            'email_verified_at' => now(),
        ]);

        // Créer 9 autres employés aléatoires
        $randomEmployees = User::factory(9)->create();

        // Tous les employés
        $employees = collect([$testEmployee])->merge($randomEmployees);

        // Créer des présences pour les 30 derniers jours
        foreach ($employees as $employee) {
            for ($i = 30; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // Sauter les weekends
                if ($date->isWeekend()) {
                    continue;
                }

                // 90% de chance d'avoir une présence
                if (fake()->boolean(90)) {
                    $checkIn = $date->copy()->setTime(fake()->numberBetween(7, 9), fake()->numberBetween(0, 59));
                    $checkOut = $i > 0 ? $date->copy()->setTime(fake()->numberBetween(17, 19), fake()->numberBetween(0, 59)) : null;

                    Presence::create([
                        'user_id' => $employee->id,
                        'date' => $date->format('Y-m-d'),
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'notes' => fake()->boolean(20) ? fake()->sentence() : null,
                    ]);
                }
            }
        }

        // Créer des tâches
        $statuts = ['pending', 'approved', 'completed'];
        $priorites = ['low', 'medium', 'high'];

        foreach ($employees as $employee) {
            $nbTasks = fake()->numberBetween(3, 8);
            for ($i = 0; $i < $nbTasks; $i++) {
                $statut = fake()->randomElement($statuts);
                $progression = match($statut) {
                    'completed' => 100,
                    'approved' => fake()->numberBetween(10, 90),
                    default => 0,
                };

                Task::create([
                    'user_id' => $employee->id,
                    'titre' => fake()->sentence(4),
                    'description' => fake()->paragraph(),
                    'progression' => $progression,
                    'statut' => $statut,
                    'priorite' => fake()->randomElement($priorites),
                    'date_debut' => Carbon::now()->subDays(fake()->numberBetween(1, 30)),
                    'date_fin' => Carbon::now()->addDays(fake()->numberBetween(1, 30)),
                ]);
            }
        }

        // Créer des demandes de congés
        $typesConge = ['conge', 'maladie', 'autre'];
        $statutsConge = ['pending', 'approved', 'rejected'];

        foreach ($employees as $employee) {
            $nbLeaves = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $nbLeaves; $i++) {
                $dateDebut = Carbon::now()->subDays(fake()->numberBetween(-30, 60));
                $dateFin = $dateDebut->copy()->addDays(fake()->numberBetween(1, 10));
                $statut = fake()->randomElement($statutsConge);

                Leave::create([
                    'user_id' => $employee->id,
                    'type' => fake()->randomElement($typesConge),
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'motif' => fake()->paragraph(),
                    'statut' => $statut,
                    'commentaire_admin' => $statut !== 'pending' ? fake()->sentence() : null,
                ]);
            }
        }

        // Créer des fiches de paie pour les 6 derniers mois
        for ($month = 5; $month >= 0; $month--) {
            $date = Carbon::now()->subMonths($month);

            foreach ($employees as $employee) {
                Payroll::create([
                    'user_id' => $employee->id,
                    'mois' => $date->month,
                    'annee' => $date->year,
                    'montant' => fake()->randomFloat(2, 2000, 5000),
                    'statut' => $month > 0 ? 'paid' : fake()->randomElement(['paid', 'pending']),
                    'notes' => fake()->boolean(30) ? fake()->sentence() : null,
                ]);
            }
        }

        // Créer des sondages
        $survey1 = Survey::create([
            'admin_id' => $admin->id,
            'titre' => 'Satisfaction au travail 2024',
            'description' => 'Évaluez votre niveau de satisfaction concernant votre environnement de travail.',
            'is_active' => true,
            'date_limite' => Carbon::now()->addDays(14),
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey1->id,
            'question' => 'Comment évaluez-vous votre satisfaction globale au travail ?',
            'type' => 'rating',
            'options' => ['min' => 1, 'max' => 5],
            'is_required' => true,
            'ordre' => 1,
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey1->id,
            'question' => 'Êtes-vous satisfait de votre équilibre vie professionnelle/vie personnelle ?',
            'type' => 'choice',
            'options' => ['Très satisfait', 'Satisfait', 'Neutre', 'Insatisfait', 'Très insatisfait'],
            'is_required' => true,
            'ordre' => 2,
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey1->id,
            'question' => 'Quels aspects aimeriez-vous améliorer ?',
            'type' => 'choice',
            'options' => ['Horaires flexibles', 'Télétravail', 'Formation', 'Équipement', 'Ambiance'],
            'is_required' => false,
            'ordre' => 3,
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey1->id,
            'question' => 'Avez-vous des suggestions pour améliorer notre environnement de travail ?',
            'type' => 'text',
            'options' => null,
            'is_required' => false,
            'ordre' => 4,
        ]);

        $survey2 = Survey::create([
            'admin_id' => $admin->id,
            'titre' => 'Évaluation des outils de travail',
            'description' => 'Donnez votre avis sur les outils et logiciels utilisés quotidiennement.',
            'is_active' => true,
            'date_limite' => Carbon::now()->addDays(7),
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey2->id,
            'question' => 'Comment évaluez-vous la qualité de vos outils de travail ?',
            'type' => 'rating',
            'options' => ['min' => 1, 'max' => 10],
            'is_required' => true,
            'ordre' => 1,
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey2->id,
            'question' => 'Quels outils utilisez-vous le plus ?',
            'type' => 'choice',
            'options' => ['Email', 'Slack', 'Teams', 'Jira', 'GitHub', 'Notion', 'Autre'],
            'is_required' => true,
            'ordre' => 2,
        ]);

        // Créer des réponses pour quelques employés
        foreach ($employees->take(5) as $employee) {
            foreach ($survey1->questions as $question) {
                $reponse = match($question->type) {
                    'rating' => (string) fake()->numberBetween(1, 5),
                    'choice' => fake()->randomElement($question->options),
                    'yesno' => fake()->randomElement(['Oui', 'Non']),
                    'text' => fake()->paragraph(),
                    default => '',
                };

                SurveyResponse::create([
                    'survey_question_id' => $question->id,
                    'user_id' => $employee->id,
                    'reponse' => $reponse,
                ]);
            }
        }

        // Sondage terminé (inactif)
        $survey3 = Survey::create([
            'admin_id' => $admin->id,
            'titre' => 'Sondage Team Building 2023',
            'description' => 'Choisissez l\'activité pour le prochain team building.',
            'is_active' => false,
            'date_limite' => Carbon::now()->subDays(30),
        ]);

        SurveyQuestion::create([
            'survey_id' => $survey3->id,
            'question' => 'Quelle activité préférez-vous ?',
            'type' => 'choice',
            'options' => ['Escape Game', 'Karting', 'Bowling', 'Laser Game', 'Restaurant'],
            'is_required' => true,
            'ordre' => 1,
        ]);
    }
}
