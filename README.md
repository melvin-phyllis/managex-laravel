# ManageX - Système de Gestion des Ressources Humaines

![Laravel](https://img.shields.io/badge/Laravel-11.48-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

---

## À propos du projet

**ManageX** est une application web complète de gestion des ressources humaines, développée avec **Laravel 11**. Elle permet aux entreprises de toutes tailles de gérer efficacement leurs employés, suivre les présences en temps réel, gérer les congés, attribuer des tâches, générer des fiches de paie multi-pays, et bien plus encore.

L'application est conçue pour être **sécurisée**, **performante** et **facile à utiliser**, avec une interface moderne et responsive.

---

## Auteur

<p align="center">
  <strong>Projet réalisé par Akou Melvin</strong><br>
  Développeur Full-Stack
</p>

---

## Table des matières

1. [Fonctionnalités détaillées](#-fonctionnalités-détaillées)
2. [Guide d'utilisation](#-guide-dutilisation)
3. [Structure de la base de données](#-structure-de-la-base-de-données)
4. [Stack technique](#-stack-technique)
5. [Installation](#-installation)
6. [Configuration](#-configuration)
7. [Déploiement en production](#-déploiement-en-production)

---

## 🚀 Fonctionnalités détaillées

### 1. Gestion des employés

#### Côté Administrateur
- **Création d'employés** : Formulaire complet avec informations personnelles, professionnelles et fiscales
- **Fiche employé détaillée** : Photo, coordonnées, contrat, département, poste, salaire
- **Import/Export** : Export Excel/CSV de la liste des employés avec filtres
- **Gestion des contrats** : Upload et stockage des contrats de travail (PDF)
- **Statuts employés** : Actif, En congé, Suspendu, Terminé
- **Jours de travail personnalisés** : Configuration des jours travaillés par employé

#### Informations gérées par employé
- Données personnelles (nom, date de naissance, genre, adresse)
- Contact d'urgence
- Informations fiscales (situation familiale, nombre de parts, numéro CNPS)
- Données bancaires (IBAN, BIC)
- Soldes de congés (congés payés, maladie, RTT)

---

### 2. Système de pointage et présences

#### Côté Employé
- **Check-in / Check-out** : Pointage avec horodatage précis
- **Géolocalisation** : Vérification de la position lors du pointage (zones autorisées configurables)
- **Horloge temps réel** : Affichage du temps de travail en cours
- **Calendrier mensuel** : Vue colorée des présences (présent, absent, retard, congé)
- **Statistiques personnelles** : Heures travaillées, retards cumulés, score de ponctualité
- **Streak de ponctualité** : Gamification avec compteur de jours consécutifs sans retard

#### Système de rattrapage des retards
- **Sessions de récupération** : L'employé peut rattraper ses minutes de retard
- **Suivi automatique** : Le système comptabilise les heures récupérées
- **Expiration** : Les retards non récupérés après X jours sont convertis en pénalités

#### Côté Administrateur
- **Master View** : Vue globale de toutes les présences en temps réel
- **Filtres avancés** : Par département, date, statut
- **Détail par employé** : Historique complet des pointages
- **Export** : CSV, Excel, PDF des données de présence
- **Alertes retards** : Notification automatique en cas de retard

---

### 3. Gestion des congés

#### Côté Employé
- **Demande de congé** : Formulaire simple avec type (congé payé, maladie, autre), dates et motif
- **Suivi des demandes** : Statut en temps réel (en attente, approuvé, refusé)
- **Soldes** : Visualisation des soldes de congés restants
- **Annulation** : Possibilité d'annuler une demande en attente

#### Côté Administrateur
- **Liste des demandes** : Vue Kanban ou liste avec filtres
- **Approbation/Refus** : Workflow de validation avec commentaire
- **Calcul automatique** : Durée en jours calculée automatiquement
- **Notifications** : L'employé est notifié de la décision

---

### 4. Gestion des tâches

#### Côté Administrateur
- **Création de tâches** : Titre, description, assignation à un employé, priorité, date d'échéance
- **Vue Kanban** : Organisation visuelle par statut (En attente, Approuvé, En cours, Terminé, Validé)
- **Priorités** : Haute, Moyenne, Basse avec code couleur
- **Suivi de progression** : Pourcentage d'avancement
- **Rappels automatiques** : Notifications avant échéance

#### Côté Employé
- **Liste des tâches** : Tâches assignées avec priorité et échéance
- **Mise à jour progression** : Slider pour indiquer l'avancement
- **Soumission** : Marquer une tâche comme terminée pour validation

---

### 5. Système de paie multi-pays

#### Configuration par pays
- **Côte d'Ivoire (CIV)** : Règles fiscales complètes (IS, CN, IGR, CNPS)
- **Extensible** : Ajout de nouveaux pays avec leurs règles spécifiques
- **Règles dynamiques** : Barèmes progressifs, taux variables selon situation familiale

#### Génération de fiches de paie
- **Calcul automatique** : Brut, cotisations, net à payer
- **Éléments variables** : Heures supplémentaires, primes, retenues
- **Export PDF** : Bulletin de paie formaté et téléchargeable
- **Génération en masse** : Créer les fiches de paie pour tous les employés d'un mois

#### Côté Employé
- **Consultation** : Accès à ses fiches de paie
- **Téléchargement PDF** : Export du bulletin

---

### 6. Gestion documentaire

#### Documents personnels (côté employé)
- **Upload** : CV, diplômes, pièces d'identité, certificats
- **Catégorisation** : Documents classés par type
- **Validation admin** : L'admin peut valider ou demander des corrections
- **Expiration** : Alerte automatique pour les documents expirants

#### Documents globaux (côté admin)
- **Règlement intérieur** : Partage avec tous les employés
- **Chartes et procédures** : Documents d'entreprise
- **Ciblage par poste** : Documents visibles uniquement par certains postes
- **Accusé de lecture** : Suivi des employés ayant lu le document

#### Demandes de documents
- **Workflow employé → admin** : L'employé demande un document (attestation de travail, etc.)
- **Réponse avec fichier** : L'admin répond en joignant le document demandé

---

### 7. Annonces et communication

#### Côté Administrateur
- **Création d'annonces** : Titre, contenu, priorité (normale, haute, critique)
- **Ciblage** : Tous les employés ou par département
- **Épinglage** : Annonces importantes en haut de liste
- **Planification** : Date de publication et d'expiration
- **Suivi des lectures** : Voir qui a lu l'annonce

#### Côté Employé
- **Liste des annonces** : Annonces actives avec indicateur de priorité
- **Lecture et accusé** : Marquer comme lu avec confirmation

---

### 8. Sondages et feedback

#### Côté Administrateur
- **Création de sondages** : Questions à choix multiples ou texte libre
- **Activation/Désactivation** : Contrôle de la disponibilité
- **Résultats** : Statistiques et graphiques des réponses
- **Anonymat** : Option de réponses anonymes

#### Côté Employé
- **Participation** : Répondre aux sondages actifs
- **Une seule réponse** : Pas de modification après soumission

---

### 9. Messagerie interne

#### Fonctionnalités
- **Conversations directes** : Messages 1-to-1 entre utilisateurs
- **Groupes** : Création de conversations de groupe
- **Pièces jointes** : Envoi de fichiers (images, PDF, documents Office)
- **Réactions** : Emojis sur les messages
- **Mentions** : @username pour notifier quelqu'un
- **Temps réel** : Mise à jour instantanée via WebSockets (ou polling fallback)
- **Statut en ligne** : Indicateur de présence des utilisateurs

#### Sécurité
- **Types de fichiers** : Liste blanche de MIME types autorisés
- **Extensions dangereuses** : Blocage de .php, .exe, .bat, etc.
- **Stockage sécurisé** : Fichiers non accessibles publiquement

---

### 10. Évaluations

#### Évaluations des employés (CDI/CDD)
- **Critères personnalisables** : Performance, compétences, comportement
- **Notes et commentaires** : Évaluation détaillée
- **Historique** : Suivi de l'évolution dans le temps
- **Validation workflow** : Draft → Validé

#### Évaluations des stagiaires
- **Évaluations hebdomadaires** : Par le tuteur assigné
- **Critères spécifiques** : Discipline, comportement, compétences, communication
- **Rappels automatiques** : Notification au tuteur chaque vendredi
- **Alertes** : Notification RH si évaluation manquante

---

### 11. Analytics et tableaux de bord

#### Dashboard Admin
- **KPIs en temps réel** : Taux de présence, absentéisme, tâches en cours
- **Graphiques** : Évolution des présences, répartition par département
- **Top performers** : Meilleurs employés du mois
- **Alertes RH** : Fins de contrat, documents expirants, anniversaires
- **Activité récente** : Dernières actions dans le système

#### Dashboard Employé
- **Résumé personnel** : Heures travaillées, congés restants
- **Graphique hebdomadaire** : Performance de la semaine
- **Tâches en cours** : Liste des priorités
- **Événements à venir** : Anniversaires, fins de période d'essai

#### Export des données
- **PDF** : Rapports analytiques formatés
- **Excel** : Données brutes pour analyse externe

---

### 12. Notifications

#### Canaux
- **In-app** : Badge de notification avec liste déroulante
- **Email** : Notifications importantes par email
- **Temps réel** : Push via WebSockets (Laravel Reverb)

#### Types de notifications
- Nouvelle tâche assignée
- Changement de statut de congé
- Nouveau message reçu
- Rappel de tâche à échéance
- Nouvelle annonce publiée
- Nouveau sondage disponible
- Évaluation reçue

---

### 13. Paramètres et configuration

#### Paramètres généraux
- **Informations entreprise** : Nom, logo, adresse
- **Horaires de travail** : Heure de début, tolérance de retard
- **Zones de géolocalisation** : Définition des zones autorisées pour le pointage

#### Gestion organisationnelle
- **Départements** : Création et gestion des services
- **Postes** : Définition des postes par département
- **Hiérarchie** : Assignation de superviseurs

---

## 📖 Guide d'utilisation

### Connexion

1. Accédez à l'URL de l'application
2. Entrez votre email et mot de passe
3. Vous êtes redirigé vers le dashboard correspondant à votre rôle

### Pour les administrateurs

#### Ajouter un employé
1. Menu **Employés** → **Ajouter un employé**
2. Remplir le formulaire (informations personnelles, professionnelles)
3. L'employé reçoit un email avec un lien d'activation

#### Gérer les présences
1. Menu **Présences** → Vue globale en temps réel
2. Cliquer sur un employé pour voir son historique
3. Utiliser les filtres pour affiner la recherche

#### Approuver un congé
1. Menu **Congés** → Liste des demandes
2. Cliquer sur une demande en attente
3. Approuver ou Refuser avec un commentaire

#### Créer une fiche de paie
1. Menu **Paie** → **Nouvelle fiche**
2. Sélectionner l'employé et le mois
3. Vérifier les calculs automatiques
4. Générer et télécharger le PDF

### Pour les employés

#### Pointer (Check-in)
1. Dashboard → Bouton **Pointer**
2. Autoriser la géolocalisation si demandé
3. Confirmer l'arrivée

#### Demander un congé
1. Menu **Congés** → **Nouvelle demande**
2. Sélectionner le type et les dates
3. Ajouter un motif (optionnel)
4. Soumettre la demande

#### Mettre à jour une tâche
1. Menu **Tâches** → Sélectionner une tâche
2. Ajuster le slider de progression
3. Marquer comme terminé quand fini

---

## 🗄️ Structure de la base de données

### Schéma des tables principales

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              UTILISATEURS                                    │
├─────────────────────────────────────────────────────────────────────────────┤
│  users                    │ Utilisateurs (admins et employés)               │
│  ├── departments          │ Départements de l'entreprise                    │
│  ├── positions            │ Postes (liés aux départements)                  │
│  ├── employee_work_days   │ Jours de travail par employé                    │
│  └── contracts            │ Contrats de travail (fichiers)                  │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              PRÉSENCES                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│  presences                │ Pointages quotidiens                            │
│  ├── geolocation_zones    │ Zones autorisées pour le pointage               │
│  └── late_penalty_absences│ Pénalités de retard non récupérés               │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              CONGÉS & TÂCHES                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│  leaves                   │ Demandes de congés                              │
│  tasks                    │ Tâches assignées aux employés                   │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              PAIE                                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  payrolls                 │ Fiches de paie générées                         │
│  payroll_items            │ Lignes de détail (primes, retenues)             │
│  payroll_countries        │ Pays configurés (CIV, etc.)                     │
│  payroll_country_rules    │ Règles fiscales par pays                        │
│  payroll_country_fields   │ Champs personnalisés par pays                   │
│  payroll_templates        │ Templates PDF par pays                          │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              DOCUMENTS                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│  documents                │ Documents personnels des employés               │
│  document_categories      │ Catégories de documents                         │
│  document_types           │ Types de documents                              │
│  global_documents         │ Documents d'entreprise (règlement, etc.)        │
│  document_requests        │ Demandes de documents (attestations, etc.)      │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              COMMUNICATION                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│  announcements            │ Annonces d'entreprise                           │
│  announcement_reads       │ Accusés de lecture                              │
│  surveys                  │ Sondages                                        │
│  survey_questions         │ Questions des sondages                          │
│  survey_responses         │ Réponses aux sondages                           │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              MESSAGERIE                                      │
├─────────────────────────────────────────────────────────────────────────────┤
│  conversations            │ Conversations (directes ou groupes)             │
│  conversation_participants│ Participants d'une conversation                 │
│  messages                 │ Messages envoyés                                │
│  message_reads            │ Accusés de lecture des messages                 │
│  message_reactions        │ Réactions (emojis) aux messages                 │
│  attachments              │ Pièces jointes                                  │
│  mentions                 │ Mentions @username                              │
│  user_statuses            │ Statut en ligne des utilisateurs                │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              ÉVALUATIONS                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│  employee_evaluations     │ Évaluations des employés (CDI/CDD)              │
│  intern_evaluations       │ Évaluations hebdomadaires des stagiaires        │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              SYSTÈME                                         │
├─────────────────────────────────────────────────────────────────────────────┤
│  settings                 │ Paramètres de l'application                     │
│  notifications            │ Notifications in-app                            │
│  cache                    │ Cache de données                                │
│  sessions                 │ Sessions utilisateurs                           │
│  jobs                     │ File d'attente des tâches                       │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Relations principales

```
User (1) ──────────────── (N) Presence
User (1) ──────────────── (N) Leave
User (1) ──────────────── (N) Task
User (1) ──────────────── (N) Payroll
User (1) ──────────────── (N) Document
User (N) ──────────────── (1) Department
User (N) ──────────────── (1) Position
User (N) ──────────────── (1) Supervisor (User)

Department (1) ─────────── (N) Position
Department (1) ─────────── (N) User

Conversation (N) ───────── (N) User (via ConversationParticipant)
Conversation (1) ───────── (N) Message
Message (1) ────────────── (N) Attachment
Message (1) ────────────── (N) MessageReaction

Survey (1) ─────────────── (N) SurveyQuestion
Survey (1) ─────────────── (N) SurveyResponse

PayrollCountry (1) ─────── (N) PayrollCountryRule
PayrollCountry (1) ─────── (N) PayrollCountryField
```

---

## 🛠️ Stack technique

| Technologie | Version | Utilisation |
|-------------|---------|-------------|
| **Laravel** | 11.48 | Framework Backend |
| **PHP** | 8.2+ | Langage serveur |
| **MySQL/SQLite** | 8.x | Base de données |
| **Tailwind CSS** | 3.x | Styling |
| **Alpine.js** | 3.x | Interactivité frontend |
| **Chart.js** | 4.x | Graphiques et statistiques |
| **Laravel Reverb** | 2.x | WebSockets temps réel |
| **Laravel Echo** | 2.x | Événements frontend |
| **Vite** | 6.x | Build tool |
| **DomPDF** | 3.x | Génération PDF |
| **Maatwebsite Excel** | 3.x | Export Excel/CSV |

---

## 📦 Installation

### Prérequis
- PHP >= 8.2 avec extensions : bcmath, ctype, curl, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml
- Composer
- Node.js >= 18
- MySQL 8.x ou SQLite

### Étapes d'installation

```bash
# 1. Cloner le repository
git clone https://github.com/votre-repo/managex.git
cd managex

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node.js
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=managex
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Exécuter les migrations
php artisan migrate

# 7. Créer le lien symbolique pour le stockage
php artisan storage:link

# 8. Compiler les assets
npm run build

# 9. Lancer le serveur
php artisan serve
```

### Mode Développement

```bash
# Terminal 1 - Serveur Laravel
php artisan serve

# Terminal 2 - WebSockets (optionnel)
php artisan reverb:start

# Terminal 3 - Queue Worker (notifications)
php artisan queue:work

# Terminal 4 - Vite (hot reload)
npm run dev
```

---

## ⚙️ Configuration

### Variables d'environnement importantes

```env
# Application
APP_NAME=ManageX
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
APP_TIMEZONE=Africa/Abidjan

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=managex
DB_USERNAME=managex_user
DB_PASSWORD=mot_de_passe_securise

# Cache/Session (recommandé : redis en production)
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-provider.com
MAIL_PORT=587
MAIL_USERNAME=votre_email
MAIL_PASSWORD=votre_mot_de_passe
```

---

## 🚀 Déploiement en production

Consultez le fichier **`docs/DEPLOYMENT-PRODUCTION.md`** pour un guide complet incluant :

- Configuration serveur
- Optimisations Laravel (cache, routes, vues)
- Configuration du worker de file d'attente
- Configuration du scheduler (tâches planifiées)
- Configuration optionnelle de Redis et Reverb

### Commandes essentielles

```bash
# Optimisations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue worker (à lancer en daemon)
php artisan queue:work --sleep=3 --tries=3

# Scheduler (ajouter au cron)
* * * * * cd /chemin/vers/managex && php artisan schedule:run >> /dev/null 2>&1
```

---

## 👥 Utilisateurs par défaut (après seeding)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@managex.com | password |
| Employé | employee@managex.com | password |

---

## 🔐 Sécurité

L'application intègre de nombreuses mesures de sécurité :

- **CSRF** : Protection sur tous les formulaires
- **XSS** : Échappement des données utilisateur
- **SQL Injection** : Requêtes préparées via Eloquent
- **Rate limiting** : Protection contre les abus (login, API)
- **En-têtes de sécurité** : X-Frame-Options, CSP, HSTS
- **Gestion des rôles** : Séparation stricte admin/employé
- **Policies** : Vérification des permissions sur chaque ressource
- **Upload sécurisé** : Validation MIME type et extension

Consultez **`docs/SECURITY-AUDIT.md`** pour plus de détails.

---

## 🐳 Déploiement Docker

### Build et exécution rapide

```bash
# Build de l'image
docker build -t managex:latest .

# Exécution avec variables d'environnement
docker run -d \
  --name managex \
  -p 8080:8080 \
  -e APP_KEY=base64:votre_cle_ici \
  -e APP_ENV=production \
  -e DB_HOST=votre_host_mysql \
  -e DB_DATABASE=managex \
  -e DB_USERNAME=utilisateur \
  -e DB_PASSWORD=motdepasse \
  -e RUN_MIGRATIONS=true \
  managex:latest
```

### Docker Compose (développement local)

```bash
# Démarrer tous les services (app + MySQL)
docker-compose up -d

# Voir les logs
docker-compose logs -f app

# Arrêter les services
docker-compose down

# Avec phpMyAdmin (interface de gestion BDD)
docker-compose --profile tools up -d
```

### Variables d'environnement Docker

| Variable | Description | Défaut |
|----------|-------------|--------|
| `APP_ENV` | Environnement (production/local) | `production` |
| `APP_KEY` | Clé d'encryption Laravel | Auto-générée |
| `APP_URL` | URL de l'application | `http://localhost:8080` |
| `DB_HOST` | Hôte MySQL | `localhost` |
| `DB_DATABASE` | Nom de la base de données | `managex` |
| `DB_USERNAME` | Utilisateur MySQL | `root` |
| `DB_PASSWORD` | Mot de passe MySQL | - |
| `RUN_MIGRATIONS` | Exécuter les migrations au démarrage | `false` |
| `QUEUE_WORKER` | Activer le worker de queue | `false` |
| `PORT` | Port d'écoute (Railway/Render) | `8080` |

### Déploiement sur Railway/Render

1. Connecter votre repo GitHub
2. Définir les variables d'environnement (voir ci-dessus)
3. Ajouter une base de données MySQL
4. Déployer automatiquement

Le Dockerfile est optimisé pour ces plateformes avec :
- Health check automatique
- Port dynamique via `$PORT`
- Optimisation OPcache pour la production

---

## 📝 Licence

Ce projet est sous licence MIT.

---

## 🤝 Contribution

Les contributions sont les bienvenues ! 

1. Fork le projet
2. Créer une branche (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit vos changements (`git commit -m 'Ajout d'une fonctionnalité'`)
4. Push sur la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrir une Pull Request

---

<p align="center">
  <strong>ManageX</strong> - Système de Gestion des Ressources Humaines<br>
  Réalisé par <strong>Akou Melvin</strong>
</p>
