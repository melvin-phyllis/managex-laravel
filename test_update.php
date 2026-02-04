<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$deleted = App\Models\Presence::where('user_id', 2)->delete();
echo "Présences de l'employé supprimées: $deleted\n";
echo "Vous pouvez maintenant tester le pointage!\n";
