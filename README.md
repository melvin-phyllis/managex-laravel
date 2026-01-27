# ManageX - Système de Gestion RH

![Laravel](https://img.shields.io/badge/Laravel-11.48-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**ManageX** est une application web moderne de gestion des ressources humaines, construite avec Laravel 11. Elle permet aux entreprises de gérer efficacement leurs employés, suivre les présences, gérer les congés, les documents, la paie multi-pays et bien plus encore.

---

## ✨ Fonctionnalités

### 👨‍💼 Espace Administrateur
- **Dashboard analytique** avec statistiques en temps réel
- **Gestion des employés** - CRUD complet avec filtres avancés
- **Suivi des présences** - Vue globale, master view et détail par employé
- **Gestion des congés** - Approbation/refus avec notifications
- **Attribution des tâches** - Suivi de progression avec rappels automatiques
- **Génération de fiches de paie** (PDF) - Multi-pays (Côte d'Ivoire, etc.)
- **Gestion documentaire** - Documents globaux et personnels
- **Demandes de documents** - Workflow de validation
- **Annonces** - Communication interne avec accusés de lecture
- **Création de sondages** pour le feedback employé
- **Alertes** - Anniversaires, fins de contrat, documents expirants

### 👤 Espace Employé
- **Pointage intelligent** avec géolocalisation
- **Horloge en temps réel** et timer de travail
- **Calendrier mensuel** coloré des présences
- **Graphiques** de performance hebdomadaire
- **Streak de ponctualité** - Gamification des présences
- **Demandes de congés** en ligne
- **Gestion des documents personnels** - Upload et téléchargement
- **Documents d'entreprise** - Accès aux documents globaux (par poste)
- **Demandes de documents** - Workflow de demande
- **Suivi des tâches** assignées
- **Consultation des fiches de paie**
- **Annonces** - Lecture des communications internes
- **Participation aux sondages**
- **Messagerie interne** en temps réel

### 🔔 Notifications Temps Réel
- Notifications push avec **Laravel Reverb** (WebSockets)
- **Laravel Echo** pour la mise à jour en temps réel
- Alertes par email
- Système de notification in-app avec badge de compteur

---

## 🛠️ Stack Technique

| Technologie | Version | Utilisation |
|-------------|---------|-------------|
| **Laravel** | 11.48 | Framework Backend |
| **PHP** | 8.2+ | Langage serveur |
| **MySQL/SQLite** | 8.x | Base de données |
| **Tailwind CSS** | 3.x | Styling |
| **Alpine.js** | 3.x | Interactivité frontend |
| **Chart.js** | 4.x | Graphiques |
| **Laravel Reverb** | 2.x | WebSockets temps réel |
| **Laravel Echo** | 2.x | Évènements frontend |
| **Vite** | 6.x | Build tool |
| **DomPDF** | 3.x | Génération PDF |
| **Maatwebsite Excel** | 3.x | Export Excel/CSV |

---

## 📦 Installation

### Prérequis
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL 8.x ou SQLite

### Étapes d'installation

```bash
# 1. Cloner le repository
git clone https://github.com/melvin-phyllis/managex-laravel.git
cd managex-laravel

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

# 6. Exécuter les migrations et seeders
php artisan migrate --seed

# 7. Compiler les assets
npm run build

# 8. Lancer le serveur
php artisan serve
```

### 🚀 Mode Développement

```bash
# Terminal 1 - Serveur Laravel
php artisan serve

# Terminal 2 - WebSockets (Reverb)
php artisan reverb:start

# Terminal 3 - Queue Worker
php artisan queue:work

# Terminal 4 - Vite (assets)
npm run dev
```

---

## 👥 Utilisateurs par défaut

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@managex.com | password |
| Employé | employee@managex.com | password |

---

## 📁 Structure du Projet

```
managex/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Contrôleurs administrateur
│   │   ├── Employee/       # Contrôleurs employé
│   │   └── Messaging/      # Messagerie interne
│   ├── Models/             # Modèles Eloquent
│   ├── Notifications/      # Classes de notification
│   ├── Observers/          # Observers (Leave, Task)
│   ├── Policies/           # Politiques d'autorisation
│   └── Services/           # Services métier (Payroll, Documents)
├── database/
│   ├── migrations/         # Migrations de BDD
│   └── seeders/            # Données de test
├── resources/
│   ├── views/
│   │   ├── admin/          # Vues administrateur
│   │   ├── employee/       # Vues employé
│   │   ├── messaging/      # Messagerie
│   │   ├── pdf/            # Templates PDF
│   │   └── components/     # Composants Blade
│   ├── css/                # Styles
│   └── js/                 # JavaScript & Echo
└── routes/
    ├── web.php             # Routes principales
    ├── messaging.php       # Routes messagerie
    └── channels.php        # Canaux WebSocket
```

---

## 🔐 Rôles et Permissions

| Fonctionnalité | Admin | Employé |
|----------------|:-----:|:-------:|
| Dashboard global | ✅ | ❌ |
| Gestion employés | ✅ | ❌ |
| Voir toutes les présences | ✅ | ❌ |
| Pointer (check-in/out) | ❌ | ✅ |
| Approuver congés | ✅ | ❌ |
| Demander congés | ❌ | ✅ |
| Créer tâches | ✅ | ❌ |
| Voir ses tâches | ❌ | ✅ |
| Générer fiches de paie | ✅ | ❌ |
| Voir sa fiche de paie | ❌ | ✅ |
| Gérer documents globaux | ✅ | ❌ |
| Voir documents globaux | ✅ | ✅ |
| Upload documents perso | ❌ | ✅ |
| Valider demandes docs | ✅ | ❌ |
| Créer annonces | ✅ | ❌ |
| Lire annonces | ✅ | ✅ |
| Créer sondages | ✅ | ❌ |
| Répondre aux sondages | ❌ | ✅ |
| Messagerie interne | ✅ | ✅ |

---

## 📊 Fonctionnalités Avancées

### Géolocalisation
Le système de pointage utilise la géolocalisation pour vérifier que les employés sont dans la zone autorisée lors du check-in/check-out.

### Calcul Automatique
- **Heures travaillées** - Calcul automatique basé sur les pointages
- **Retards** - Détection automatique avec tolérance configurable
- **Heures supplémentaires** - Calcul au-delà de 8h/jour
- **Score de ponctualité** - Pourcentage calculé mensuellement

### Système de Paie Multi-Pays
- Configuration par pays (Côte d'Ivoire inclus)
- Règles fiscales dynamiques (IS, CN, IGR, CNPS)
- Templates de bulletins de paie personnalisables
- Export PDF

### Gestion Documentaire
- **Documents globaux** - Partagés par l'admin (règlement, procédures)
- **Documents personnels** - Uploadés par l'employé (diplômes, pièces)
- **Demandes de documents** - Workflow admin → employé
- Catégorisation et expiration automatique

### Export de Données
- Export CSV/Excel des listes d'employés
- Génération PDF des fiches de paie
- Rapports statistiques

---

## 🧪 Tests

```bash
# Exécuter les tests
php artisan test

# Avec couverture
php artisan test --coverage
```

---

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push sur la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

---

## 📧 Contact

Pour toute question ou suggestion, n'hésitez pas à ouvrir une issue sur GitHub.

---

<p align="center">
  Realiser par <a href="https://github.com/melvin-phyllis">Melvin Phyllis</a> 
</p>
