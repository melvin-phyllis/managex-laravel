<?php
// Script à uploader sur votre serveur cPanel (ex: dans public_html) pour vérifier l'heure

echo "<pre>";
echo "=== VERIFICATION HEURE SERVEUR ===\n";
echo "Heure PHP (date): " . date('Y-m-d H:i:s') . "\n";
echo "Fuseau horaire PHP: " . date_default_timezone_get() . "\n";
echo "\n";
echo "=== TEST DECALAGE ===\n";
echo "Heure UTC (Gmdate): " . gmdate('Y-m-d H:i:s') . "\n";
echo "Heure Paris (Europe/Paris): ";
try {
    $date = new DateTime("now", new DateTimeZone('Europe/Paris'));
    echo $date->format('Y-m-d H:i:s') . "\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
echo "</pre>";
