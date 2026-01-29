#!/bin/bash

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Cache config/routes for performance (optional, good for prod)
# php artisan config:cache
# php artisan route:cache

# Start Apache
echo "Starting Apache..."
apache2-foreground
