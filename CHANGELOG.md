# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/),
et ce projet adhère au [Versionnage Sémantique](https://semver.org/lang/fr/).

## [Non publié]

### Ajouté
- Vue calendrier pour les tâches (FullCalendar) - admin et employé
- Guide utilisateur complet (docs/USER-GUIDE.md)
- Tests Feature pour les fonctionnalités principales (tâches, congés, présences, paie)
- Pipeline CI/CD GitHub Actions (lint, tests, sécurité)
- Lightbox2 pour l'affichage des images dans la messagerie
- Interface de messagerie style WhatsApp (bulles, messages vocaux avec waveform)
- Support des messages vocaux (enregistrement et lecture)
- Support des images dans la messagerie
- Module d'évaluations des stagiaires

### Modifié
- Configuration phpunit.xml pour utiliser SQLite en mémoire
- Amélioration de l'UI des messages vocaux
- Amélioration de l'affichage des pièces jointes

### Corrigé
- Correction du MIME type pour les messages vocaux WebM
- Correction des en-têtes de sécurité pour le microphone

---

## [1.0.0] - 2026-01-15

### Ajouté
- Système d'authentification complet avec rôles (admin/employé)
- Gestion des employés (CRUD, import/export)
- Système de pointage avec géolocalisation
- Gestion des tâches avec vue Kanban et liste
- Gestion des congés (demande, approbation, refus)
- Génération de fiches de paie multi-pays (Côte d'Ivoire)
- Système de sondages internes
- Module d'annonces avec ciblage
- Gestion documentaire (documents personnels et globaux)
- Messagerie interne avec conversations directes et groupes
- Dashboard analytics avec KPIs et graphiques
- Notifications en temps réel (Laravel Reverb)
- Système de géolocalisation pour le pointage
- Export des données (PDF, Excel, CSV)

### Sécurité
- Rate limiting sur les routes sensibles
- En-têtes de sécurité HTTP (CSP, HSTS, etc.)
- Validation des fichiers uploadés
- Protection CSRF sur tous les formulaires
- Hashage des mots de passe (bcrypt)
