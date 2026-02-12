<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "App Timezone Config: " . config('app.timezone') . "\n";
echo "Current App Time: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Current Server Time (PHP date): " . date('Y-m-d H:i:s') . "\n";
echo "Difference from UTC: " . (new DateTime())->getOffset() / 3600 . " hours\n";

if (config('app.timezone') == 'UTC') {
    echo "Timezone OK (Abidjan/GMT)\n";
} elseif (config('app.timezone') == 'Africa/Abidjan') {
    echo "Timezone OK (Abidjan)\n";
} else {
    echo "WARNING: Timezone is NOT Abidjan/GMT. Please check .env file.\n";
}
