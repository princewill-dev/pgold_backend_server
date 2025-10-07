@echo off
setlocal enabledelayedexpansion

echo ========================================
echo    PGold Application Startup Script
echo ========================================
echo.

echo Checking if Docker is running...
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker is not running!
    echo Please start Docker Desktop and try again.
    pause
    exit /b 1
)

echo [OK] Docker is running
echo.

echo Building Docker images...
docker-compose build
if errorlevel 1 (
    echo [ERROR] Failed to build Docker images
    pause
    exit /b 1
)

echo.
echo Starting services...
echo    - PostgreSQL Database (port 5432)
echo    - Laravel Application (port 8000)
echo    - Queue Worker (background jobs)
echo.

docker-compose up -d
if errorlevel 1 (
    echo [ERROR] Failed to start services
    pause
    exit /b 1
)

echo.
echo Waiting for database to be ready...
timeout /t 5 /nobreak >nul

echo.
echo Running migrations...
docker-compose exec pgold_app php artisan migrate --force

echo.
echo Seeding rates...
docker-compose exec pgold_app php artisan db:seed --class=RateSeeder

echo.
echo Generating application key...
docker-compose exec pgold_app php artisan key:generate

echo.
echo Clearing caches...
docker-compose exec pgold_app php artisan config:clear
docker-compose exec pgold_app php artisan cache:clear
docker-compose exec pgold_app php artisan route:clear

echo.
echo ========================================
echo    Application Started Successfully!
echo ========================================
echo.
echo Application URL: http://localhost:8000
echo Database: localhost:5432
echo.
echo Useful Commands:
echo   View logs:        docker-compose logs -f
echo   Stop services:    docker-compose down
echo   Restart services: docker-compose restart
echo   Enter container:  docker-compose exec pgold_app sh
echo.
echo Queue Worker Status:
docker-compose ps pgold_queue
echo.
echo Press any key to exit...
pause >nul
