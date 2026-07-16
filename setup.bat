@echo off
echo ============================================
echo  LibraryCRM - Auto Setup Script
echo ============================================
echo.

echo [1/5] Installing PHP dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: composer install failed. Make sure Composer is installed.
    pause
    exit /b 1
)

echo.
echo [2/5] Setting up environment file...
if not exist .env (
    copy .env.example .env
    echo .env file created from .env.example
) else (
    echo .env already exists, skipping...
)

echo.
echo [3/5] Generating application key...
php artisan key:generate

echo.
echo [4/5] Please update your .env file with database credentials.
echo       DB_DATABASE, DB_USERNAME, DB_PASSWORD
echo.
set /p continue="Press ENTER after updating .env to continue..."

echo.
echo [5/5] Running database migrations and seeding demo data...
php artisan migrate --seed

echo.
echo ============================================
echo  SETUP COMPLETE!
echo ============================================
echo.
echo  Run: php artisan serve
echo  Then open: http://localhost:8000
echo.
echo  Demo Credentials:
echo  Admin  : admin@librarycrm.com / password
echo  Owner  : owner@demo.com / password
echo ============================================
pause
