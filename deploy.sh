#!/bin/bash

# Pull latest code from your desired branch
BRANCH="main"  # Change this if needed

echo "🔄 Pulling latest changes from $BRANCH..."
git pull origin $BRANCH

# Run Composer
echo "📦 Installing PHP dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Build front-end assets if package.json exists
if [ -f package.json ]; then
    echo "📦 Installing Node dependencies..."
    npm ci --no-audit --prefer-offline
    echo "🛠️ Building assets..."
    npm run build
fi

# Prevent concurrent PHP-FPM reloads
touch /tmp/fpmlock 2>/dev/null || true
( flock -w 10 9 || exit 1
    echo '🔁 Reloading PHP-FPM...'
    sudo service php8.1-fpm reload
) 9</tmp/fpmlock

# Run migrations
if [ -f artisan ]; then
    echo "🧩 Running migrations..."
    php artisan migrate --force
fi

# Restart Horizon
echo "🚀 Restarting Horizon..."
php artisan horizon:terminate

echo "✅ Deployment complete!"
