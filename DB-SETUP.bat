@echo off
chcp 65001 >nul
echo ========================================
echo   Veritabani Kurulumu (Schema + Seed)
echo ========================================
echo.
echo NOT: Laragon kullaniyorsaniz once Laragon'u baslatin (Sag tik - Start All)
echo      veya MySQL - Start
echo.

set PHP_CMD=
if exist "C:\php\php.exe" set PHP_CMD=C:\php\php.exe & goto :found
if exist "C:\xampp\php\php.exe" set PHP_CMD=C:\xampp\php\php.exe & goto :found
for /d %%p in (C:\laragon\bin\php\php-*) do if exist "%%p\php.exe" set PHP_CMD=%%p\php.exe & goto :found
if exist "C:\laragon\bin\php\php.exe" set PHP_CMD=C:\laragon\bin\php\php.exe & goto :found
if exist "C:\wamp64\bin\php\php.exe" set PHP_CMD=C:\wamp64\bin\php\php.exe & goto :found
if exist "%LOCALAPPDATA%\Programs\PHP\php.exe" set PHP_CMD=%LOCALAPPDATA%\Programs\PHP\php.exe & goto :found
where php >nul 2>&1 && set PHP_CMD=php && goto :found

echo [HATA] PHP bulunamadi!
echo.
echo PHP kurmaniz gerekiyor. Secenekler:
echo   - https://windows.php.net/download/
echo   - XAMPP: https://www.apachefriends.org/
echo   - Laragon: https://laragon.org/
echo.
echo PHP kurduktan sonra bu dosyayi tekrar calistirin.
pause
exit /b 1

:found
echo PHP: %PHP_CMD%
echo.

set PHP_INI=%~dp0php-local.ini
if exist "%PHP_INI%" set PHP_CMD="%PHP_CMD%" -c "%PHP_INI%"

echo [1/2] Schema import ediliyor...
%PHP_CMD% scripts/import-schema.php
if errorlevel 1 (
  echo Hata! .env dosyasini ve DB baglantisini kontrol edin.
  pause
  exit /b 1
)

echo [2/2] Seed calistiriliyor...
%PHP_CMD% scripts/seed-db.php
if errorlevel 1 (
  echo Seed hatasi!
  pause
  exit /b 1
)

echo.
echo Tamam! Veritabani hazir.
pause
