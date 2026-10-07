#!/bin/bash
# PKTS Karate Deployment Script
# Usage: ./deploy.sh [production|staging]

set -e

ENV=${1:-production}
DB_NAME="pkts_karate"

echo "=== PKTS Karate Deployment ($ENV) ==="

echo "1. Creating database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "2. Running database migrations..."
php setup_db.php

echo "3. Setting permissions..."
chmod -R 755 ../
find . -type f -exec chmod 644 {} \;

echo "4. Optimizing autoload..."
composer dump-autoload --optimize 2>/dev/null || true

echo "5. Clearing cache..."
rm -rf cache/* 2>/dev/null || true

echo "=== Deployment Complete ==="
echo "Site URL: https://your-domain.com"
echo "Admin: https://your-domain.com/admin/login.php"
echo "Admin credentials: admin / admin123"