#!/bin/sh
set -e

echo "============================================"
echo "   ManageX - Démarrage du Container"
echo "============================================"

# Création des répertoires nécessaires
echo "[1/8] Création des répertoires..."
mkdir -p /var/log/supervisor
mkdir -p storage/framework/{sessions,views,cache/data}
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# Génération de la clé d'application si manquante
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "[2/8] Génération de APP_KEY..."
    php artisan key:generate --force
else
    echo "[2/8] APP_KEY déjà configurée"
fi

# Configuration des permissions
echo "[3/8] Configuration des permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Création du lien symbolique storage
echo "[4/8] Création du lien storage..."
php artisan storage:link --force 2>/dev/null || true

# Exécution des migrations (optionnel via variable d'environnement)
if [ "$RUN_MIGRATIONS" = "true" ] || [ "$RUN_MIGRATIONS" = "1" ]; then
    echo "[5/8] Exécution des migrations..."
    php artisan migrate --force
    
    # Créer le compte admin si nécessaire
    echo "[5.5/8] Vérification du compte admin..."
    php artisan db:seed --class=AdminSeeder --force 2>/dev/null || true
else
    echo "[5/8] Migrations ignorées (RUN_MIGRATIONS non défini)"
fi

# Optimisation du cache (production)
if [ "$APP_ENV" = "production" ]; then
    echo "[6/8] Optimisation pour la production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache 2>/dev/null || true
else
    echo "[6/8] Mode développement - pas de cache"
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

# Configuration du port Nginx (Railway/Render/etc.)
PORT="${PORT:-8080}"
echo "[7/8] Configuration Nginx sur le port $PORT..."
sed -i "s/listen [0-9]*;/listen $PORT;/g" /etc/nginx/http.d/default.conf

# Démarrage des workers de queue (optionnel)
if [ "$QUEUE_WORKER" = "true" ] || [ "$QUEUE_WORKER" = "1" ]; then
    echo "[8/8] Worker de queue activé"
    # Le worker sera géré par supervisor
    cat >> /etc/supervisord.conf << 'EOF'

[program:queue-worker]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
startretries=3
numprocs=1
EOF
else
    echo "[8/8] Worker de queue désactivé"
fi

echo "============================================"
echo "   ManageX prêt sur le port $PORT"
echo "============================================"

# Démarrage via Supervisor
exec /usr/bin/supervisord -c /etc/supervisord.conf
