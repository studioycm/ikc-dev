#!/usr/bin/env bash
set -euo pipefail

# This script is designed to be used as the Forge deploy script.
# Assumptions:
# - Running in the project root on the Forge server.
# - $FORGE_PHP, $FORGE_COMPOSER, and $FORGE_PHP_FPM are available in the environment.
# - You build assets on CI. No Node steps here.

php_bin="${FORGE_PHP:-php}"
composer_bin="${FORGE_COMPOSER:-composer}"

# 1) Maintenance mode
$php_bin artisan down || true

# 2) Update code – Forge usually checks out the latest commit automatically.
#    If you keep this file in the repo and want to run it locally, uncomment:
# git pull origin "${FORGE_SITE_BRANCH:-main}" || true

# 3) Conditional Composer install — only if composer.lock changed (first deploy will run)
need_composer=1
if git rev-parse HEAD^ >/dev/null 2>&1; then
  if ! git diff --name-only HEAD^ HEAD | grep -q '^composer.lock$'; then
    need_composer=0
  fi
fi

if [ "$need_composer" -eq 1 ]; then
  echo "composer.lock changed or first deploy — running composer install"
  $composer_bin install --no-dev --no-interaction --prefer-dist --optimize-autoloader
else
  echo "composer.lock unchanged — skipping composer install"
fi

# 4) Ensure storage symlink exists
if [ ! -L public/storage ]; then
  $php_bin artisan storage:link || true
fi

# 5) Clear caches
$php_bin artisan optimize:clear

# 6) Run database migrations for the default connection only
#    (leaving out --database uses config('database.default'), i.e., your main DB)
$php_bin artisan migrate --force

# 7) Seed super admin (idempotent, does not change password if user already exists)
$php_bin artisan db:seed --class=Database\\Seeders\\SuperAdminSeeder --force || true

# 8) Warm caches
$php_bin artisan config:cache
$php_bin artisan route:cache
$php_bin artisan view:cache

# 9) Graceful Horizon / queue restart
$php_bin artisan horizon:terminate || true
$php_bin artisan queue:restart || true

# 10) Reload PHP-FPM (Forge pattern)
( flock -w 10 9 || exit 1
    echo 'Reloading PHP FPM...'; sudo -S service "${FORGE_PHP_FPM:-php8.3-fpm}" reload ) 9>/tmp/fpmlock

# 11) Bring app back up
$php_bin artisan up

# 12) Health check — try APP_URL + /up if available
app_url=$($php_bin -r "require __DIR__.'/vendor/autoload.php'; $app=require __DIR__.'/bootstrap/app.php'; $app->make(Illuminate\\Contracts\\Console\\Kernel::class); echo (string) config('app.url');" || true)
if command -v curl >/dev/null 2>&1; then
  if [ -n "$app_url" ]; then
    echo "Health check: $app_url/up"
    if ! curl -fsS "$app_url/up" >/dev/null; then
      echo "[WARN] Health check failed at $app_url/up" >&2
    fi
  else
    echo "[INFO] APP_URL not set; skipping HTTP health check"
  fi
fi

echo "Deploy complete"
