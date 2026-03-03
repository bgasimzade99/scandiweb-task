@echo off
cd /d "%~dp0.."
echo Backend: http://localhost:8000
php -S localhost:8000 -t public
pause
