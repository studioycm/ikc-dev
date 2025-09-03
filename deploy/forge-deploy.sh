#!/usr/bin/env bash
set -euo pipefail

# Simple Forge deploy script (minimal, close to the initial recommended one)
# Assumes running in the project root and Forge env vars are available:
#   $FORGE_SITE_BRANCH, $FORGE_PHP, $FORGE_COMPOSER, $FORGE_PHP_FPM
cd /home/forge/ikc.data4.work

# 1) Get latest code
git pull origin "${FORGE_SITE_BRANCH:-main}"

if [ -f artisan ]; then
    $FORGE_PHP artisan down
fi
# 2) Install PHP dependencies (always run; Composer cache makes this fast)
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm ci
npm run build

# optional if you must reclaim space
#[ -d node_modules ] && rm -rf node_modules

# 3) Migrate database on the default connection
#if [ -f artisan ]; then
#  $FORGE_PHP artisan migrate --force
#fi

# 4) Optimize the application
if [ -f artisan ]; then
    $FORGE_PHP artisan optimize
    $FORGE_PHP artisan filament:optimize || true
    $FORGE_PHP artisan up
fi

# 5) Restart Horizon and queues
$FORGE_PHP artisan horizon:terminate || true
$FORGE_PHP artisan queue:restart || true

# 6) Reload PHP-FPM to apply changes
( flock -w 10 9 || exit 1
    echo 'Reloading PHP FPM...'; sudo -S service "${FORGE_PHP_FPM:-php8.4-fpm}" reload ) 9>/tmp/fpmlock

echo "Deploy complete"
