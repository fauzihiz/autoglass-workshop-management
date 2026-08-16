#!/bin/sh
set -e

echo "🚀 Starting Workshop Management System..."

# ── Wait for database to be ready ──────────────────────────────
echo "⏳ Waiting for database connection..."
tries=0
max_tries=30
until php artisan migrate --force --no-interaction 2>/dev/null; do
    tries=$((tries + 1))
    if [ "$tries" -ge "$max_tries" ]; then
        echo "❌ Could not connect to database after $max_tries attempts."
        exit 1
    fi
    echo "   Database not ready yet (attempt $tries/$max_tries)... waiting 3s"
    sleep 3
done
echo "✅ Database connection established and migrations applied."

# ── Seed database (safe to re-run — seeders use firstOrCreate patterns) ──
echo "🌱 Seeding demo data..."
php artisan db:seed --force --no-interaction || true
echo "✅ Seeding complete."

# ── Cache config, routes, and views (AFTER env vars are available) ──
echo "⚙️  Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Configuration cached."

# ── Ensure storage directories exist and are writable ───────────
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "🎯 Application ready. Starting server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
