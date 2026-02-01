# Audit de sécurité – ManageX

*Dernière mise à jour : février 2026*

## Résumé

L'application présente une **bonne posture de sécurité** pour un projet Laravel : CSRF, auth, autorisation par rôles et policies, validation, rate limiting, en-têtes de sécurité et gestion des fichiers sont en place. Quelques points restent à renforcer (voir recommandations).

---

## Points positifs

### 1. Authentification et autorisation
- **Auth** : Routes protégées par `middleware(['auth'])`, redirection vers login si non connecté.
- **Rôles** : Séparation nette `admin` / `employee` via `RoleMiddleware` ; pas d’élévation de privilège par mass assignment (champ `role` retiré de `$fillable`, utilisation de `setRole()`).
- **Policies** : Task, Leave, Payroll, Document, Conversation, Message – vérification ownership (ex. `user_id === $task->user_id`) ou rôle admin.
- **Contrôle d’accès** : `abort(403)` cohérent dans les contrôleurs (messaging, documents, évaluations, etc.).

### 2. CSRF
- Formulaires : `@csrf` ou `csrf_token()` dans les vues.
- Layouts : meta `csrf-token` pour les appels AJAX/fetch.
- Requêtes API internes : en-tête `X-CSRF-TOKEN` utilisé (messaging, tâches, etc.).

### 3. Validation des entrées
- Validation Laravel (`$request->validate(...)`) sur les contrôleurs (congés, tâches, messages, employés, paramètres, etc.).
- Paramètres de période/analytics : types forcés (`(int) $request->get(...)`), pas de concaténation directe en SQL.

### 4. Requêtes SQL
- Pas d’injection évidente : `selectRaw` / `whereRaw` / `orderByRaw` utilisent des chaînes fixes ou des paramètres liés (`?`), pas de concaténation de saisie utilisateur.

### 5. XSS (affichage)
- Contenu utilisateur : `nl2br(e($announcement->content))` pour les annonces (échappement + retours à la ligne).
- Composants (icônes, attributs) : usage de `{!! !!}` limité à du contenu maîtrisé (icônes, composants Blade).
- Pas d’affichage brut de contenu utilisateur non échappé.

### 6. Mots de passe
- Hash systématique : `Hash::make()` à la création / modification du mot de passe (employés, auth, profil, reset password).
- Cast `'password' => 'hashed'` sur le modèle User.
- Règles de complexité (min 8 caractères, mixedCase, numbers) sur changement de mot de passe.

### 7. Rate limiting
- Login : `throttle:5,1`.
- Messaging : `throttle:messaging` (30/min), uploads (10/min), exports (5/min), actions sensibles (20/min).
- Limitation par utilisateur ou IP selon le contexte.

### 8. En-têtes de sécurité (`SecurityHeaders`)
- X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy.
- HSTS et CSP en production.
- Suppression de X-Powered-By.

### 9. Fichiers (messaging)
- Types MIME et extensions autorisés et vérifiés.
- Liste d’extensions dangereuses bloquée (php, exe, etc.).
- Noms de fichiers sécurisés (UUID + extension validée), stockage privé (`local`).

### 10. Proxies
- Configuration `TRUSTED_PROXIES` documentée ; en local uniquement `*` pour faciliter le dev.

---

## Recommandations

### Priorité haute
1. **Mass assignment – mot de passe**  
   Retirer `password` de `$fillable` du modèle User et ne définir le mot de passe que dans les contrôleurs via `Hash::make()`. Évite tout changement de mot de passe accidentel ou malveillant via `update($request->all())` ou équivalent.

### Priorité moyenne
2. **CSP en production**  
   La CSP actuelle autorise `'unsafe-inline'` et `'unsafe-eval'` pour scripts/styles. À long terme, viser des nonces ou hashes pour réduire la surface XSS.

3. **Vérification d’email**  
   Si l’app gère des données sensibles, activer `MustVerifyEmail` sur User et les routes `verified` pour les zones sensibles.

4. **Logs et erreurs**  
   En production : pas de stack traces ni de données sensibles dans les réponses ; `APP_DEBUG=false`, logging adapté.

### Priorité basse
5. **Audit des policies**  
   Vérifier que toute ressource “par ID” (congés, documents, paie, etc.) est protégée par une policy ou un scope utilisateur, pour éviter tout IDOR.

6. **Sessions**  
   Utiliser des cookies de session sécurisés (HTTPS, SameSite, durée de vie raisonnable).

---

## Conclusion

L’application est **globalement bien sécurisée** pour un contexte interne / RH : auth, rôles, CSRF, validation, rate limiting, en-têtes de sécurité et gestion des pièces jointes sont correctement pris en charge. En appliquant la recommandation sur le champ `password` (et les autres points selon ton niveau d’exigence), tu renforces encore la posture de sécurité.
