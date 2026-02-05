# Guide de Deploiement ManageX sur cPanel (Namecheap)

## Pre-requis

- Hebergement Namecheap avec cPanel
- PHP 8.2+ active
- Extensions PHP: `zip`, `gd`, `intl`, `mbstring`, `xml`, `pdo_mysql`
- Acces SSH (recommande) ou File Manager

## Etape 1: Preparer les fichiers localement

```bash
# Compiler les assets
npm run build

# Installer les dependances de production
composer install --optimize-autoloader --no-dev

# Creer l'archive (exclure node_modules et .git)
zip -r managex.zip . -x "node_modules/*" -x ".git/*" -x "tests/*" -x ".env"
```

## Etape 2: Creer la base de donnees

1. Connectez-vous a **cPanel**
2. Allez dans **MySQL Databases**
3. Creez une base: `votreuser_managex`
4. Creez un utilisateur: `votreuser_dbuser` avec mot de passe fort
5. Associez l'utilisateur avec **ALL PRIVILEGES**

## Etape 3: Structure des fichiers

```
/home/votreuser/
|
+-- managex/              <- Tout sauf /public
|   +-- app/
|   +-- bootstrap/
|   +-- config/
|   +-- database/
|   +-- resources/
|   +-- routes/
|   +-- storage/
|   +-- vendor/
|   +-- .env              <- Copie de .env.cpanel
|   +-- artisan
|   +-- composer.json
|
+-- public_html/          <- Contenu de /public
    +-- index.php         <- MODIFIER (voir ci-dessous)
    +-- .htaccess
    +-- build/
    +-- storage/          <- Lien symbolique
```

## Etape 4: Upload des fichiers

### Via File Manager ou FTP:

1. Creez le dossier `/home/votreuser/managex/`
2. Uploadez tout le contenu SAUF `/public` dans `managex/`
3. Uploadez le contenu de `/public` dans `public_html/`

## Etape 5: Modifier public_html/index.php

Remplacez le contenu par:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Chemin vers le projet Laravel (MODIFIE pour cPanel)
require __DIR__.'/../managex/vendor/autoload.php';
$app = require_once __DIR__.'/../managex/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();
$kernel->terminate($request, $response);
```

## Etape 6: Configurer .env

1. Copiez `.env.cpanel` vers `/home/votreuser/managex/.env`
2. Modifiez les valeurs:
   - `APP_URL` = votre domaine
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` = vos identifiants MySQL
   - `RESEND_API_KEY` = votre cle API Resend (ou configurez SMTP)
   - `MISTRAL_API_KEY` = votre cle API Mistral

3. Generez la cle d'application:
```bash
cd ~/managex
php artisan key:generate
```

## Etape 7: Permissions

Via SSH ou Terminal cPanel:

```bash
cd ~/managex

# Permissions des dossiers
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework

# Creer le lien storage
rm -f ~/public_html/storage
ln -s ~/managex/storage/app/public ~/public_html/storage
```

## Etape 8: Migrations

```bash
cd ~/managex

# Executer les migrations
php artisan migrate --force

# Vider et recreer les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Etape 9: Cron Job (Taches planifiees)

Dans cPanel > **Cron Jobs**, ajoutez:

```
* * * * * cd /home/votreuser/managex && php artisan schedule:run >> /dev/null 2>&1
```

## Etape 10: Verifier le .htaccess

Assurez-vous que `public_html/.htaccess` contient:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Depannage

### Erreur 500
- Verifiez les permissions de `storage/` et `bootstrap/cache/`
- Consultez `storage/logs/laravel.log`

### Page blanche
- Activez temporairement `APP_DEBUG=true` dans `.env`
- Verifiez la version PHP (8.2+ requise)

### Images/fichiers non accessibles
- Verifiez le lien symbolique `storage`
- Recreez-le si necessaire

### Emails non envoyes
- Verifiez la cle API Resend
- Ou configurez SMTP avec les parametres de votre hebergeur

## Fonctionnalites limitees sur cPanel

| Fonctionnalite | Status | Alternative |
|----------------|--------|-------------|
| Chat temps reel | Polling (OK) | Actualisation auto 3s |
| Notifications push | Non | Via email |
| Queue workers | Sync | Execution immediate |
| WebSockets | Non | Non necessaire |

---

Votre application ManageX est maintenant deployee sur cPanel!
