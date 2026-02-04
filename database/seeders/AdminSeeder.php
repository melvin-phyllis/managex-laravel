<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder pour créer le compte administrateur initial
 * 
 * Usage: php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si un admin existe déjà
        if (User::where('role', 'admin')->exists()) {
            $this->command->info('Un compte admin existe déjà. Aucune action nécessaire.');
            return;
        }

        // Créer le compte admin
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@managex.com',
            'password' => Hash::make('Admin2026!'),
            'role' => 'admin',
            'poste' => 'Administrateur Système',
            'telephone' => '+225 00 00 00 00',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Compte admin créé avec succès !');
        $this->command->info('   Email: admin@managex.com');
        $this->command->info('   Mot de passe: Admin2026!');
        $this->command->warn('   ⚠️  CHANGEZ CE MOT DE PASSE IMMÉDIATEMENT après connexion !');
    }
}
