<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Créer ou trouver l'utilisateur test
$user = \App\Models\User::where('email', 'phyllismelvin000@gmail.com')->first();

if (!$user) {
    echo '❌ Utilisateur non trouvé. Veuillez le créer d\'abord.'."\n";
    exit;
}

echo '📧 Envoi de l\'email de bienvenue (Test Logo Rond)...'."\n";

try {
    $user->notify(new \App\Notifications\WelcomeEmployeeNotification($user->name, null, 'NouveauPass123!'));
    echo '✅ Email envoyé avec succès !'."\n";
} catch (\Exception $e) {
    echo '❌ Erreur : '.$e->getMessage()."\n";
}
