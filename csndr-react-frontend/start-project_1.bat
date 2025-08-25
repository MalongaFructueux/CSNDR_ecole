@echo off
echo ========================================
echo   DEMARRAGE DU PROJET CSNDR
echo ========================================
echo.

echo [1/4] Demarrage du serveur Laravel...
cd csndr-laravel-backend
start "Laravel Backend" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 3 /nobreak >nul

echo [2/4] Demarrage du serveur React...
cd ..
start "React Frontend" cmd /k "npm start"
timeout /t 3 /nobreak >nul

echo [3/4] Ouverture du navigateur...
start http://localhost:3000
timeout /t 2 /nobreak >nul

echo [4/4] Ouverture de l'API Laravel...
start http://127.0.0.1:8000
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo   PROJET DEMARRE AVEC SUCCES !
echo ========================================
echo.
echo Frontend React: http://localhost:3000
echo Backend Laravel: http://127.0.0.1:8000
echo API: http://127.0.0.1:8000/api
echo.
echo Appuyez sur une touche pour fermer cette fenetre...
pause >nul
