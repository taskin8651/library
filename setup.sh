#!/bin/bash
echo "============================================"
echo " LibraryCRM - Auto Setup Script"
echo "============================================"
echo ""

echo "[1/5] Installing PHP dependencies..."
composer install
if [ $? -ne 0 ]; then
    echo "ERROR: composer install failed."
    exit 1
fi

echo ""
echo "[2/5] Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env file created"
else
    echo ".env already exists, skipping..."
fi

echo ""
echo "[3/5] Generating application key..."
php artisan key:generate

echo ""
echo "[4/5] Creating storage symlink..."
php artisan storage:link

echo ""
echo "Please update your .env file with database credentials."
echo "DB_DATABASE, DB_USERNAME, DB_PASSWORD"
read -p "Press ENTER after updating .env to continue..."

echo ""
echo "[5/5] Running migrations and seeding..."
php artisan migrate --seed

echo ""
echo "============================================"
echo " SETUP COMPLETE!"
echo "============================================"
echo ""
echo " Run: php artisan serve"
echo " Open: http://localhost:8000"
echo ""
echo " Demo Login:"
echo " Admin : admin@librarycrm.com / password"
echo " Owner : owner@demo.com / password"
echo "============================================"
