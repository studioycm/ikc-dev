#!/usr/bin/env bash
set -euo pipefail

# Simple Forge deploy script (minimal, close to the initial recommended one)
# Assumes running in the project root and Forge env vars are available:
#   $FORGE_SITE_BRANCH, $FORGE_PHP, $FORGE_COMPOSER, $FORGE_PHP_FPM
cd /home/forge/ikc.data4.work
# 1) Get latest code
git pull origin "${FORGE_SITE_BRANCH:-main}"

# 2) Install PHP dependencies (always run; Composer cache makes this fast)
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3) Migrate database on the default connection
if [ -f artisan ]; then
  $FORGE_PHP artisan migrate --force
fi

# 4) Restart Horizon and queues
$FORGE_PHP artisan horizon:terminate || true
$FORGE_PHP artisan queue:restart || true

# 5) Reload PHP-FPM to apply changes
( flock -w 10 9 || exit 1
    echo 'Reloading PHP FPM...'; sudo -S service "${FORGE_PHP_FPM:-php8.4-fpm}" reload ) 9>/tmp/fpmlock

echo "Deploy complete"
