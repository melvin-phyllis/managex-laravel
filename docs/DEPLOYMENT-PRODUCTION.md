# Déploiement en production – ManageX

Ce guide liste les étapes nécessaires pour que l’application soit **pleinement fonctionnelle** en production.

---

## 1. Prérequis serveur

- **PHP** 8.2+ avec extensions : `bcmath`, `ctype`, `curl`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- **Base de données** : MySQL 8+ / MariaDB 10.3+ (ou PostgreSQL)
- **Node.js** 18+ et npm (pour compiler les assets une fois)
- **Optionnel** : Redis (cache, sessions, files d’attente, Reverb)

---

## 2. Checklist de déploiement

### 2.1 Environnement

```bash
# Copier l’exemple production et éditer
cp .env.production.example .env

# Générer la clé d’application (obligatoire)
php artisan key:generate

# Vérifier : APP_DEBUG=false, APP_ENV=production, APP_URL=https://votre-domaine.com
```

### 2.2 Base de données

```bash
# Migrations
php artisan migrate --force

# Tables optionnelles si vous utilisez cache/session/queue en "database"
# (déjà créées par les migrations Laravel : cache, sessions, jobs)
```

### 2.3 Stockage et médias

```bash
# Lien symbolique public/storage → storage/app/public (avatars, fichiers)
php artisan storage:link
```

Sans cette commande, les URLs `Storage::url()` (avatars, etc.) ne fonctionneront pas.

### 2.4 Assets (CSS/JS)

L’application utilise **Vite** (`@vite()`). En production il faut des assets compilés :

```bash
npm ci
npm run build
```

Cela remplit `public/build/` (manifest + fichiers). **Sans `npm run build`**, les pages n’auront pas de CSS/JS en production.

### 2.5 Optimisations Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

À refaire après toute modification de `config/`, des routes ou des vues.

### 2.6 File d’attente (notifications)

Les notifications (tâches, congés, messages, etc.) utilisent **ShouldQueue**. Il faut un worker actif :

```bash
# En production, lancer via Supervisor ou systemd, par exemple :
php artisan queue:work --sleep=3 --tries=3
```

- Si **QUEUE_CONNECTION=database** (défaut si non défini) : pas besoin de Redis, mais la table `jobs` doit exister.
- Si **QUEUE_CONNECTION=redis** : Redis doit être configuré et accessible.

Sans worker, les notifications seront mises en file et ne seront pas envoyées.

### 2.7 Planificateur (scheduler)

Les commandes planifiées (rappels d’évaluations, pénalités de retard, etc.) nécessitent le scheduler Laravel :

**Linux / cron :**

```bash
* * * * * cd /chemin/vers/managex && php artisan schedule:run >> /dev/null 2>&1
```

**Windows (Task Scheduler) :** exécuter `run-scheduler.bat` toutes les minutes (adapter le chemin dans le script si besoin).

Sans cela, les tâches planifiées ne s’exécuteront pas.

---

## 3. Options selon l’infrastructure

### Sans Redis

L’application fonctionne avec les valeurs par défaut Laravel :

- **CACHE_STORE** : non défini → `database` (ou `file` selon config)
- **SESSION_DRIVER** : non défini → `database` (table `sessions`)
- **QUEUE_CONNECTION** : non défini → `database` (table `jobs`)

Assurez-vous que les migrations ont bien créé les tables `cache`, `sessions`, `jobs`.

### Avec Redis (recommandé pour forte charge)

Dans `.env` (voir `.env.production.example`) :

- `CACHE_STORE=redis`
- `SESSION_DRIVER=redis`
- `QUEUE_CONNECTION=redis`

Puis lancer un worker sur la connexion Redis :  
`php artisan queue:work redis --sleep=3 --tries=3`

### Broadcast / temps réel (Reverb)

Les notifications temps réel (Echo) utilisent Reverb si configuré. Si Reverb n’est pas installé ou pas configuré :

- L’app continue de fonctionner.
- Le front fait du **polling** (notifications, messagerie) ; pas de push temps réel.

Pour activer le temps réel en production : configurer Reverb (ou un autre driver broadcast) et les variables `REVERB_*` / `VITE_REVERB_*`, puis rebuild des assets (`npm run build`).

---

## 4. Résumé « prêt production »

| Étape | Commande / action | Obligatoire |
|-------|-------------------|-------------|
| Clé app | `php artisan key:generate` | Oui |
| Migrations | `php artisan migrate --force` | Oui |
| Lien storage | `php artisan storage:link` | Oui (avatars/fichiers) |
| Build assets | `npm run build` | Oui (pages avec CSS/JS) |
| Cache config/routes/vues | `config:cache`, `route:cache`, `view:cache` | Recommandé |
| Worker file d’attente | `php artisan queue:work` (permanent) | Oui (notifications) |
| Scheduler | Cron / Task Scheduler → `schedule:run` | Oui (évaluations, pénalités) |
| Redis | Optionnel | Non (sauf si vous l’exigez) |
| Reverb / broadcast | Optionnel | Non (fallback polling) |

---

## 5. Vérification rapide

Après déploiement :

1. **Page d’accueil / login** : pas d’erreur 500, CSS/JS chargés (onglet Réseau).
2. **Connexion** : login admin ou employé OK.
3. **Avatar** : une photo de profil s’affiche (vérifie `storage:link`).
4. **Création d’une tâche / congé** : notification reçue (vérifie queue worker).
5. **Horodatage** : timezone correcte (`APP_TIMEZONE`, ex. `Africa/Abidjan`).

Si ces points sont OK, l’application est prête pour un usage pleinement fonctionnel en production.
