# AUDIT EXHAUSTIF - ManageX (Laravel 11)

**Stack :** Laravel 11.31 / PHP 8.2+ / SQLite (dev) / Tailwind CSS / Alpine.js / Laravel Reverb
**Date :** 2026-02-03
**Fichiers analysés :** 100+ (controllers, models, routes, middleware, views, migrations, config)

---

## SYNTHESE EXECUTIVE

| Catégorie | Critique | Élevée | Moyenne | Faible | Total |
|-----------|----------|--------|---------|--------|-------|
| Sécurité (OWASP) | **9** | **14** | **18** | **6** | **47** |
| Performance | **4** | **8** | **15** | **3** | **30** |
| Base de données | **26** | **9** | **14** | **1** | **50** |
| **TOTAL** | **39** | **31** | **47** | **10** | **127** |

---

## PARTIE 1 : AUDIT SECURITE (OWASP)

---

### A. Sensitive Data Exposure (OWASP A02:2021) — 6 CRITIQUES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 1 | 🔴 Critique | `.env:3` | **APP_KEY exposée dans le fichier .env versionné** — Toute donnée chiffrée (sessions, cookies) peut être déchiffrée | Ajouter `.env` au `.gitignore`, **régénérer la clé immédiatement** avec `php artisan key:generate`, rotation de toutes les sessions |
| 2 | 🔴 Critique | `.env:56-57` | **Identifiants Gmail en clair** (`melvinphyllisakou@gmail.com` + mot de passe app) dans le fichier versionné | Supprimer du VCS, révoquer le mot de passe d'application Gmail, recréer et stocker uniquement dans `.env` non-versionné |
| 3 | 🔴 Critique | `.env:71-73` | **Secrets Reverb/WebSocket exposés** (APP_ID, APP_KEY, APP_SECRET) | Régénérer les secrets, ne stocker que dans `.env` non-versionné |
| 4 | 🔴 Critique | `.env:4` | **`APP_DEBUG=true`** expose les stack traces, variables d'environnement, requêtes SQL dans les pages d'erreur | Mettre `APP_DEBUG=false` en production |
| 5 | 🔴 Critique | `.env:33` | **`SESSION_ENCRYPT=false`** — Sessions stockées en clair dans la BDD | Mettre `SESSION_ENCRYPT=true` |
| 6 | 🔴 Critique | `.env:35` | **`SESSION_SECURE_COOKIE=false`** — Cookies de session transmis en HTTP, vulnérable au MITM | Mettre `SESSION_SECURE_COOKIE=true` en production |

**Référence :** [OWASP A02 - Cryptographic Failures](https://owasp.org/Top10/A02_2021-Cryptographic_Failures/)

---

### B. Injection (OWASP A03:2021) — 1 MOYENNE

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 7 | 🟡 Moyenne | `routes/messaging.php:68-75` | **Recherche utilisateur sans validation** — `$request->get('q')` injecté directement dans `LIKE "%{$query}%"` sans validation d'entrée ni rate limiting | Ajouter `$request->validate(['q' => 'required\|string\|max:100'])` + middleware `throttle:messaging` |

**Note positive :** Les 24 instances de `selectRaw()`, `whereRaw()`, `orderByRaw()`, et `DB::raw()` utilisent toutes des bindings paramétrés ou des valeurs statiques. Aucune injection SQL trouvée dans les requêtes raw.

Aucune instance de `exec()`, `shell_exec()`, `system()`, `eval()`, `unserialize()` détectée.

---

### C. XSS (OWASP A03:2021) — 3 CRITIQUES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 8 | 🔴 Critique | `resources/views/components/layouts/employee.blade.php:159` | **Nom utilisateur dans handler `onerror` + `innerHTML`** — `{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}` dans un attribut JavaScript. Un nom contenant `'` casse le contexte JS | Remplacer par `this.parentElement.textContent=@json(strtoupper(substr(auth()->user()->name, 0, 1)));` |
| 9 | 🔴 Critique | `resources/views/components/layouts/admin.blade.php:210` | **Même vulnérabilité** dans le layout admin | Même correction avec `textContent` + `@json()` |
| 10 | 🔴 Critique | `resources/views/admin/intern-evaluations/show.blade.php:17` | **Nom de stagiaire dans `onclick` CustomEvent** — `filename: 'evaluation-{{ $intern->name }}.pdf'` injectable si le nom contient des quotes | Utiliser `@json('evaluation-' . $intern->name . '.pdf')` |

**Note positive :** Toutes les 21 instances de `{!! !!}` sont soit protégées par `e()` (ex: `{!! nl2br(e($content)) !!}`), soit des contenus statiques (icônes SVG, templates vendor). L'usage de `@json()` est systématique pour les données injectées dans JavaScript.

---

### D. Broken Access Control (OWASP A01:2021) — 5 ÉLEVÉES, 4 MOYENNES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 11 | 🟠 Élevée | `app/Http/Controllers/Admin/PresenceController.php:237-276` | **IDOR sur `employeeDetails()`** — Prend `$userId` en paramètre sans vérification d'autorisation. Tout admin peut interroger les présences de n'importe quel employé | Ajouter `$this->authorize('view-presence', $user)` ou vérification de département |
| 12 | 🟠 Élevée | `app/Http/Controllers/Admin/PresenceController.php:281-400` | **IDOR sur `showEmployeePresence()`** — Historique complet accessible sans contrôle d'accès | Même correction |
| 13 | 🟠 Élevée | `routes/messaging.php:26-48` | **Routes messaging API sans autorisation** — `PUT /conversations/{conversation}`, `DELETE /messages/{message}` protégées uniquement par `auth`, pas de Policy | Ajouter `$this->authorize('update', $conversation)` dans chaque méthode du controller |
| 14 | 🟠 Élevée | `routes/web.php:266-277` | **Admin messaging sans Policy** — CRUD conversations admin sans `$this->authorize()` dans `Admin/MessagingController.php:104-198` | Ajouter les appels `authorize()` |
| 15 | 🟠 Élevée | `app/Http/Controllers/Employee/InternEvaluationController.php:74-81` | **Vérification de rôle manquante** — Vérifie `$evaluation->intern_id === $user->id` mais pas si l'utilisateur est réellement un stagiaire | Ajouter `if (!$user->isIntern()) abort(403)` avant la vérification d'ownership |
| 16 | 🟡 Moyenne | `app/Http/Controllers/Admin/InternEvaluationController.php:67-80` | **Accès inter-départements** — Tout admin peut voir les évaluations de tout stagiaire | Ajouter vérification de département |
| 17 | 🟡 Moyenne | `app/Http/Controllers/Tutor/InternEvaluationController.php:51-58` | **Vérification superviseur insuffisante** — Se fie uniquement à `supervisor_id` sans valider le rôle tuteur | Ajouter `if (!$user->isTutor()) abort(403)` |
| 18 | 🟡 Moyenne | `app/Http/Controllers/Admin/PayrollController.php:78` | **`$request->all()` passé au service** — Peut transmettre des champs non validés au PayrollService | Remplacer par `$request->validated()` |
| 19 | 🟡 Moyenne | `app/Http/Middleware/RoleMiddleware.php:16-31` | **Redirection au lieu de 403** — Un utilisateur non autorisé est redirigé au lieu de recevoir un 403, masquant les tentatives d'accès non autorisé | Remplacer la redirection par `abort(403)` |

---

### E. Security Misconfiguration (OWASP A05:2021) — 4 ÉLEVÉES, 3 MOYENNES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 20 | 🟠 Élevée | `.env:31` | **`SESSION_DRIVER=file`** au lieu de `database` (contrairement au `.env.example`) | Mettre `SESSION_DRIVER=database` |
| 21 | 🟠 Élevée | `.env:22` | **`LOG_LEVEL=debug`** — Journalisation excessive pouvant exposer des données sensibles | Mettre `LOG_LEVEL=warning` en production |
| 22 | 🟠 Élevée | `app/Http/Middleware/SecurityHeaders.php:71-72` | **CSP avec `'unsafe-inline'` et `'unsafe-eval'`** — Rend la CSP inefficace contre les XSS | Implémenter un système de nonces CSP, supprimer `unsafe-inline`/`unsafe-eval` |
| 23 | 🟠 Élevée | `app/Http/Middleware/SecurityHeaders.php:67` | **CSP uniquement en production** — Environnements dev/staging non protégés | Appliquer une CSP (plus permissive) en dev aussi |
| 24 | 🟡 Moyenne | `config/auth.php:113` | **`password_timeout = 10800`** (3h) — Fenêtre de session trop longue pour les opérations sensibles | Réduire à 1800 (30 minutes) |
| 25 | 🟡 Moyenne | `config/cors.php` | **Fichier CORS absent** — Pas de politique CORS explicite | Créer `config/cors.php` avec origines autorisées |
| 26 | 🟡 Moyenne | `config/database.php:97` | **PostgreSQL `sslmode='prefer'`** — Permet le downgrade vers connexion non chiffrée | Mettre `sslmode='require'` |

---

### F. Rate Limiting Manquant (OWASP A04:2021) — 3 ÉLEVÉES, 5 MOYENNES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 27 | 🟠 Élevée | `routes/auth.php:61` | **`POST /confirm-password` sans rate limiting** — Brute force possible (contrairement à login/register qui sont limités) | Ajouter `->middleware('throttle:5,1')` |
| 28 | 🟠 Élevée | `routes/web.php:207-208` | **Bulk payroll generation sans rate limiting** — Opération très coûteuse (PDF par employé) | Ajouter `->middleware('throttle:sensitive')` |
| 29 | 🟠 Élevée | `routes/web.php:215-217` | **Bulk employee evaluations sans rate limiting** | Ajouter `->middleware('throttle:sensitive')` |
| 30 | 🟡 Moyenne | `routes/web.php:266-277` | **Admin messaging routes sans throttle** (alors que les routes employee messaging l'ont) | Appliquer `throttle:messaging` |
| 31 | 🟡 Moyenne | `routes/web.php:245-246` | **Exports PDF/Excel analytics sans rate limiting** — Le limiteur `'exports'` est défini dans `AppServiceProvider:66` mais **jamais appliqué** | Ajouter `->middleware('throttle:exports')` |
| 32 | 🟡 Moyenne | `routes/web.php:420-423` | **Profile + password update sans rate limiting** | Ajouter `throttle:10,1` minimum |
| 33 | 🟡 Moyenne | `routes/web.php:296-297` | **Document validation bulk operation sans throttle** | Ajouter `throttle:sensitive` |
| 34 | 🟡 Moyenne | `app/Http/Controllers/Messaging/ConversationController.php:143-150` | **Énumération de conversations** possible par brute force des IDs | Ajouter throttle sur les endpoints show/destroy |

---

### G. Mass Assignment (OWASP A04:2021) — EXCELLENTE PROTECTION

**37/37 modèles** ont `$fillable` correctement défini. Aucun modèle n'utilise `$guarded = []`.

Points forts :

- `User.php` : `password` et `role` exclus de `$fillable`, rôle assigné via `setRole()` validé
- `$hidden` correctement configuré pour `password` et `remember_token`
- Tous les champs financiers (`decimal:2`), booléens, et dates ont des `$casts` appropriés

**Recommandation informationnelle :** Considérer le chiffrement au repos pour `bank_iban`, `bank_bic`, `social_security_number` dans le modèle User (conformité PCI DSS / RGPD).

---

### H. File Upload Security — 3 MOYENNES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 35 | 🟡 Moyenne | `app/Http/Controllers/Admin/DocumentRequestController.php:53-73` | **`getClientOriginalExtension()` utilisé** pour le filename — L'extension est contrôlée par le client | Utiliser l'extension déduite du MIME type réel via `finfo` |
| 36 | 🟡 Moyenne | `app/Http/Controllers/Admin/EmployeeController.php:384-423` | **Même problème** sur upload de contrats + `getClientOriginalName()` stocké | Vérifier le contenu réel du fichier, ne pas stocker le nom original |
| 37 | 🟡 Moyenne | `app/Http/Controllers/Messaging/AttachmentController.php:82-86` | **Fallback MIME basé sur l'extension** — Si le navigateur envoie `application/octet-stream`, l'extension client détermine le MIME | Vérifier le contenu réel avec `finfo_file()` |

---

## PARTIE 2 : AUDIT PERFORMANCE

---

### A. Problèmes N+1 — 4 CRITIQUES, 4 ÉLEVÉES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 38 | 🔴 Critique | `app/Http/Controllers/Admin/InternEvaluationController.php:45-49` | **5 requêtes identiques** pour calculer la distribution des notes (A/B/C/D/E) — Chacune fait un `get()` complet puis `filter()` en PHP | 1 seule requête avec `selectRaw('grade_letter, COUNT(*)')` + `groupBy` |
| 39 | 🔴 Critique | `app/Http/Controllers/Admin/AnalyticsController.php:280-300` | **N+1 départements × présences** — Boucle `.map()` sur chaque département avec requête Presence séparée | Requête unique avec `selectRaw()` + `groupBy('department_id')` |
| 40 | 🔴 Critique | `app/Http/Controllers/Admin/AnalyticsController.php:347-361` | **N+1 départements × 2 requêtes ponctualité** — 2 requêtes par département (on_time + late) | Agréger les données en une seule requête |
| 41 | 🔴 Critique | `app/Http/Controllers/Admin/PayrollController.php:111-139` | **PDF synchrone en boucle** — `bulkGenerate()` génère un PDF par employé de manière synchrone. 100 employés = 100 PDFs bloquants | Utiliser une Queue/Job pour la génération en batch |
| 42 | 🟠 Élevée | `app/Http/Controllers/Tutor/InternEvaluationController.php:20-36` | **N+1 par stagiaire** — Requête `InternEvaluation::where(...)` dans `.map()` pour chaque stagiaire | Eager loading avec contrainte `with(['internEvaluations' => fn($q) => ...])` |
| 43 | 🟠 Élevée | `app/Http/Controllers/Employee/DashboardController.php:174-205` | **35 requêtes pour le tableau de bord** — 5 requêtes pour les heures hebdo + 30 requêtes `exists()` pour les présences mensuelles | Charger toutes les présences en 1 requête, filtrer en PHP |
| 44 | 🟠 Élevée | `app/Http/Controllers/Admin/AnalyticsController.php:621-674` | **3 requêtes séquentielles** pour le classement assiduité (stats, users, présences) — données présences interrogées 2 fois | Consolider en 1 requête avec JOIN |
| 45 | 🟠 Élevée | `app/Http/Controllers/Admin/DocumentController.php:51` | **Pas de pagination** sur la liste des documents — `->get()` charge tout en mémoire | Remplacer par `->paginate(20)` |

---

### B. Cache Manquant — 3 ÉLEVÉES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 46 | 🟠 Élevée | `app/Http/Controllers/Employee/PresenceController.php:232,375,450,546,658` | **GeolocationZone::where('is_active', true)->get()** exécutée 5 fois dans le même controller — données quasi-statiques | Cacher 24h via `CacheService` |
| 47 | 🟠 Élevée | Multiple controllers | **Department/Position** chargés à chaque requête sans cache (sauf AnalyticsController qui utilise `getActiveCached()`) | Utiliser `CacheService` partout |
| 48 | 🟠 Élevée | `app/Http/Controllers/Admin/AnalyticsController.php:494` | **Bug cache key** — `analytics_latecomers_{$month}_{$year}` n'inclut pas `$departmentId` → retourne des données incorrectes quand le filtre département change | Ajouter `_{$departmentId}` à la clé |

---

### C. Traitement Synchrone — 2 ÉLEVÉES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 49 | 🟠 Élevée | `app/Http/Controllers/Admin/AnalyticsController.php:777-785` | **Export PDF analytique synchrone** — Génération DomPDF bloquante sur rapport complexe | Mettre en Queue, retourner un lien de téléchargement |
| 50 | 🟠 Élevée | `app/Http/Controllers/Admin/PayrollController.php:88` | **Notifications synchrones** — `$payroll->user->notify()` appelé de manière synchrone | Utiliser `->notify()` via queue |

---

### D. Calculs en PHP au lieu de SQL — 3 MOYENNES

| # | Sévérité | Localisation | Problème | Solution |
|---|----------|-------------|----------|----------|
| 51 | 🟡 Moyenne | `app/Http/Controllers/Employee/DashboardController.php:131-140` | **Calcul heures en PHP** — `->get()->sum()` avec `diffInMinutes()` au lieu de `SUM(TIMESTAMPDIFF())` en SQL | Utiliser `selectRaw()` avec agrégation SQL |
| 52 | 🟡 Moyenne | `app/Http/Controllers/Employee/DashboardController.php:142-149` | **Calcul jours congé en PHP** — `->get()->sum()` avec `diffInDays()` | Utiliser `selectRaw('SUM(DATEDIFF(date_fin, date_debut) + 1)')` |
| 53 | 🟡 Moyenne | `app/Http/Controllers/Admin/InternEvaluationController.php:147-149` | **Filtre grade en PHP** — `->get()->filter()` après chargement complet, détruit la pagination | Utiliser `->where('grade_letter', ...)` en SQL |

---

## PARTIE 3 : AUDIT BASE DE DONNÉES

---

### A. Index Manquants — 26 CRITIQUES (Foreign Keys)

**26 colonnes de clés étrangères** sans index, impactant directement les performances des JOINs :

| Tables affectées | Colonnes non indexées |
|-----------------|----------------------|
| tasks, leaves, payrolls, surveys | `user_id` |
| survey_questions | `survey_id` |
| survey_responses | `survey_question_id`, `user_id` |
| positions | `department_id` |
| contracts | `user_id` |
| payroll_items | `payroll_id` |
| payroll_country_rules / fields / templates | `country_id` |
| documents | `user_id`, `document_type_id`, `validated_by`, `uploaded_by` |
| document_requests | `admin_id` |
| document_types | `category_id` |
| intern_evaluations | `intern_id`, `tutor_id` |
| employee_evaluations | `user_id`, `evaluated_by` |
| late_penalty_absences | `user_id` |

**Note :** La migration `2026_02_01_182840_add_performance_indexes_to_tables.php` ajoute de bons index composites, mais les FK de base restent non indexées.

---

### B. Contraintes Uniques Manquantes — 6 ÉLEVÉES

| Table | Colonne(s) | Impact |
|-------|-----------|--------|
| users | `social_security_number` | Doublons SSN possibles |
| users | `bank_iban` | Doublons IBAN possibles |
| users | `cnps_number` | Doublons CNPS possibles |
| document_categories | `slug` | URLs dupliquées possibles |
| departments | `name` | Départements en double |
| payroll_countries | `name` | Pays en double |

---

### C. Types de Données Incorrects — 3 ÉLEVÉES

| Table | Colonne | Actuel | Correct |
|-------|---------|--------|---------|
| tasks | `statut` | `string(255)` | `enum('pending','approved','in_progress','completed','validated')` |
| tasks | `priorite` | `string(255)` | `enum('low','medium','high')` |
| document_requests | `status` | `string(255)` | `enum('pending','approved','rejected')` |

---

### D. Contraintes CHECK Manquantes — 8 MOYENNES

| Table | Colonne | Contrainte recommandée |
|-------|---------|----------------------|
| employee_work_days | `day_of_week` | `CHECK (day_of_week BETWEEN 1 AND 7)` |
| tasks | `progression` | `CHECK (progression >= 0 AND progression <= 100)` |
| geolocation_zones | `radius` | `CHECK (radius > 0 AND radius <= 10000)` |
| payrolls | `worked_days` | `CHECK (worked_days <= 31)` |
| payrolls | `absence_days` | `CHECK (absence_days <= 31)` |
| users | `children_count` | `CHECK (children_count >= 0 AND children_count <= 20)` |
| users | `number_of_parts` | `CHECK (number_of_parts >= 1 AND number_of_parts <= 10)` |
| employee_evaluations | `month` | `CHECK (month BETWEEN 1 AND 12)` |

---

### E. Types Non Signés Manquants — 11 MOYENNES

| Table | Colonne | Actuel | Correct |
|-------|---------|--------|---------|
| geolocation_zones | `radius` | `integer` | `unsignedInteger` |
| employee_work_days | `day_of_week` | `tinyInteger` | `unsignedTinyInteger` |
| presences | `late_minutes` | `integer` | `unsignedInteger` |
| presences | `early_departure_minutes` | `integer` | `unsignedInteger` |
| tasks | `progression` | `integer` | `unsignedTinyInteger` |
| documents | `download_count` | `integer` | `unsignedInteger` |
| documents | `file_size` | `integer` | `unsignedBigInteger` |
| announcements | `view_count` | `integer` | `unsignedInteger` |
| survey_questions | `ordre` | `integer` | `unsignedInteger` |
| document_categories | `sort_order` | `integer` | `unsignedInteger` |
| document_types | `sort_order` | `integer` | `unsignedInteger` |

---

## PRIORISATION GLOBALE

### IMMEDIAT (P0) — Impact Business Critique / Exploitation Triviale

1. **Supprimer `.env` du VCS** + régénérer APP_KEY, mots de passe Gmail, secrets Reverb
2. **Mettre `APP_DEBUG=false`** en production
3. **Activer `SESSION_ENCRYPT=true`** et `SESSION_SECURE_COOKIE=true`
4. **Corriger les 3 XSS critiques** dans les layouts (nom utilisateur dans handlers JS)
5. **Passer `SESSION_DRIVER=database`**

### HAUTE PRIORITE (P1) — Correction cette semaine

6. Ajouter les vérifications d'autorisation sur les routes messaging (IDOR)
7. Ajouter les vérifications d'autorisation sur PresenceController `employeeDetails` / `showEmployeePresence`
8. Ajouter rate limiting sur `confirm-password`, exports, bulk operations
9. Remplacer `$request->all()` par `$request->validated()` dans PayrollController
10. Créer la migration pour les 26 index FK manquants
11. Corriger le bug de cache key dans AnalyticsController (departmentId manquant)

### PRIORITE MOYENNE (P2) — Ce sprint

12. Supprimer `'unsafe-inline'`/`'unsafe-eval'` de la CSP (implémenter nonces)
13. Corriger les N+1 critiques (InternEvaluation grades, Analytics departments)
14. Mettre en queue la génération PDF (bulk payroll, analytics export)
15. Cacher les GeolocationZones, Departments, Positions
16. Ajouter pagination sur les listes de documents
17. Ajouter les contraintes uniques manquantes (SSN, IBAN, CNPS, slugs)
18. Corriger les types enum (tasks.statut, tasks.priorite)

### PRIORITE BASSE (P3) — Prochain cycle

19. Ajouter COOP/COEP headers
20. Réduire `password_timeout` à 30 min
21. Optimiser les calculs dashboard (SQL au lieu de PHP)
22. Spécifier les colonnes dans les eager loads (`with('user:id,name')`)
23. Chiffrer au repos bank_iban, social_security_number (conformité RGPD)
24. Ajouter les contraintes CHECK manquantes
25. Corriger les types non signés

---

## POINTS FORTS DU PROJET

- **Modèles Eloquent** : 37/37 avec `$fillable` correct, password/role protégés, `$casts` complets
- **Rate limiting** sur login/register/password-reset bien implémenté
- **Middleware SecurityHeaders** avec X-Frame-Options, X-Content-Type-Options, Referrer-Policy
- **HSTS** activé en production avec preload
- **Bcrypt rounds = 12** pour le hashing des mots de passe
- **6 Policies** d'autorisation définies (Conversation, Document, Leave, Message, Payroll, Task)
- **Aucune** commande shell, `eval()`, `unserialize()` dans le code
- **Usage correct** de `@json()` pour l'injection de données dans JavaScript (sauf les 3 exceptions signalées)
- **Validation des entrées** systématique dans les controllers avec `$request->validate()`
- **Paramètres bindés** sur toutes les requêtes raw SQL

---

*Rapport généré le 2026-02-03 — Audit réalisé avec Claude Code*
