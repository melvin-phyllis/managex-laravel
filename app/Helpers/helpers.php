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

        // Si c'est déjà une URL complète valide vers /storage/, la retourner telle quelle
        if (str_starts_with($avatarPath, 'http://') || str_starts_with($avatarPath, 'https://')) {
            // Vérifier si c'est une URL valide vers notre storage
            if (preg_match('#https?://[^/]+/storage/(.+)$#', $avatarPath, $matches)) {
                // C'est déjà une URL complète vers storage - la retourner
                // Mais reconstruire avec l'URL de l'app actuelle pour éviter les problèmes de domaine
                $relativePath = $matches[1];
                return asset('storage/' . $relativePath);
            }
            // URL externe - retourner tel quel
            return $avatarPath;
        }

        // Nettoyer le chemin
        $avatarPath = ltrim($avatarPath, '/');
        
        // Enlever "storage/" si présent au début
        if (str_starts_with($avatarPath, 'storage/')) {
            $avatarPath = substr($avatarPath, 8);
        }
        
        // S'assurer que le chemin ne contient pas de caractères invalides ou d'URL encodée
        $avatarPath = trim($avatarPath);
        
        // Construire l'URL avec asset()
        return asset('storage/' . $avatarPath);
    }
}
