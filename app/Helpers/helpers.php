<?php

if (!function_exists('avatar_url')) {
    /**
     * Génère l'URL d'un avatar utilisateur
     * 
     * @param string|null $avatarPath Le chemin de l'avatar (ex: "avatars/filename.jpg")
     * @return string L'URL complète de l'avatar ou une chaîne vide si aucun avatar
     */
    function avatar_url(?string $avatarPath): string
    {
        if (!$avatarPath) {
            return '';
        }

        // Si c'est déjà une URL complète, extraire le chemin relatif
        if (str_starts_with($avatarPath, 'http://') || str_starts_with($avatarPath, 'https://')) {
            // Extraire le chemin après /storage/
            if (preg_match('#/storage/(.+)$#', $avatarPath, $matches)) {
                $avatarPath = $matches[1];
            } else {
                // URL externe ou format inconnu - retourner tel quel
                return $avatarPath;
            }
        }

        // Nettoyer le chemin (enlever les slashes en début et "storage/" si présent)
        $avatarPath = ltrim($avatarPath, '/');
        if (str_starts_with($avatarPath, 'storage/')) {
            $avatarPath = substr($avatarPath, 8); // Enlever "storage/"
        }
        
        // Vérifier si le fichier existe dans le stockage public
        $fullPath = storage_path('app/public/' . $avatarPath);
        
        if (file_exists($fullPath)) {
            // Utiliser asset() pour générer l'URL correcte vers /storage/avatars/...
            return asset('storage/' . $avatarPath);
        }

        // Fallback vers Storage::url() si le fichier n'existe pas localement
        // (pour les environnements avec stockage distant comme S3)
        try {
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($avatarPath);
            // S'assurer que l'URL ne contient pas de chemin admin incorrect
            if (strpos($url, '/admin/') !== false) {
                return asset('storage/' . $avatarPath);
            }
            return $url;
        } catch (\Exception $e) {
            // Dernier recours : construire l'URL manuellement
            return asset('storage/' . $avatarPath);
        }
    }
}
