@echo off
chcp 65001 >nul
echo Backend (GraphQL) baslatiliyor: http://localhost:8000
echo Durdurmak icin Ctrl+C
echo.

set PHP_CMD=
if exist "C:\php\php.exe" set PHP_CMD=C:\php\php.exe & goto :found
if exist "C:\xampp\php\php.exe" set PHP_CMD=C:\xampp\php\php.exe & goto :found
for /d %%p in (C:\laragon\bin\php\php-*) do if exist "%%p\php.exe" set PHP_CMD=%%p\php.exe & goto :found
if exist "C:\laragon\bin\php\php.exe" set PHP_CMD=C:\laragon\bin\php\php.exe & goto :found
where php >nul 2>&1 && set PHP_CMD=php && goto :found

echo PHP bulunamadi! PHP kurun veya PATH'e ekleyin.
pause
exit /b 1

:found
cd /d "%~dp0"
"%PHP_CMD%" -S localhost:8000 -t public
pause
