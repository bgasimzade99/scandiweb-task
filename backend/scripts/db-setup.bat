@echo off
chcp 65001 >nul
cd /d "%~dp0.."
echo Database setup (schema + seed)...
set PHP_CMD=php
where php >nul 2>&1 || (echo PHP not found. Install PHP and add to PATH. & pause & exit /b 1)
php scripts/import-schema.php
if errorlevel 1 (echo Schema import failed. Check .env and DB connection. & pause & exit /b 1)
php scripts/seed-db.php
if errorlevel 1 (echo Seed failed. & pause & exit /b 1)
echo Database ready.
pause
