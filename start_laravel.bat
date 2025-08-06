@echo off
REM Caminho até a pasta do seu projeto Laravel
cd /d C:\Users\Rafael Males\medigest_clean>

REM Abre o servidor Laravel
start cmd /k "php artisan serve"

REM Abre o Vite (npm run dev)
start cmd /k "npm run dev"
