<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SECURITY AUDIT FIX: Prépare les colonnes pour le chiffrement
 *
 * Les données chiffrées par Laravel sont beaucoup plus longues que les données originales
 * (base64 JSON avec iv, value, mac, tag). Cette migration :
 * 1. Change les colonnes en TEXT pour accommoder les données chiffrées
 * 2. Chiffre les données existantes
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ÉTAPE 0: Supprimer les index uniques AVANT de changer les colonnes en TEXT
        // MySQL ne permet pas d'index sur TEXT sans longueur de clé spécifiée
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les index uniques créés par la migration précédente
            try {
                $table->dropUnique('users_ssn_unique');
            } catch (\Exception $e) {
                // Index n'existe peut-être pas
            }

            try {
                $table->dropUnique('users_iban_unique');
            } catch (\Exception $e) {
                // Index n'existe peut-être pas
            }
        });

        // ÉTAPE 1: Changer les colonnes en TEXT pour accommoder les données chiffrées
        Schema::table('users', function (Blueprint $table) {
            $table->text('social_security_number')->nullable()->change();
            $table->text('bank_iban')->nullable()->change();
            $table->text('bank_bic')->nullable()->change();
        });

        // ÉTAPE 2: Chiffrer les données existantes
        $users = DB::table('users')
            ->where(function ($query) {
                $query->whereNotNull('social_security_number')
                    ->orWhereNotNull('bank_iban')
                    ->orWhereNotNull('bank_bic');
            })
            ->get(['id', 'social_security_number', 'bank_iban', 'bank_bic']);

        foreach ($users as $user) {
            $updates = [];

            if ($user->social_security_number && ! $this->isEncrypted($user->social_security_number)) {
                $updates['social_security_number'] = Crypt::encryptString($user->social_security_number);
            }

            if ($user->bank_iban && ! $this->isEncrypted($user->bank_iban)) {
                $updates['bank_iban'] = Crypt::encryptString($user->bank_iban);
            }

            if ($user->bank_bic && ! $this->isEncrypted($user->bank_bic)) {
                $updates['bank_bic'] = Crypt::encryptString($user->bank_bic);
            }

            if (! empty($updates)) {
                DB::table('users')->where('id', $user->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas déchiffrer pour des raisons de sécurité
        // Les colonnes restent en TEXT pour ne pas perdre de données
    }

    private function isEncrypted(string $value): bool
    {
        if (strlen($value) < 100) {
            return false;
        }

        try {
            Crypt::decryptString($value);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
};
