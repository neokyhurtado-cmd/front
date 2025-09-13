# Script para ejecutar el sistema de blog automatizado
# Panorama Ingeniería IA

Write-Host "🚀 Sistema de Blog Automatizado - Panorama Ingeniería IA" -ForegroundColor Cyan
Write-Host "=========================================================" -ForegroundColor Cyan

Write-Host "📡 1. Trayendo feeds RSS y procesando con IA..." -ForegroundColor Yellow
php artisan feeds:fetch

Write-Host "⚙️ 2. Procesando contenido con IA (5 posts)..." -ForegroundColor Yellow
php artisan queue:work --max-jobs=5

Write-Host "📅 3. Programando posts para las franjas del día..." -ForegroundColor Yellow
php artisan posts:schedule-daily

Write-Host "📊 4. Estado actual del sistema:" -ForegroundColor Yellow
php artisan posts:rotate-monthly

Write-Host ""
Write-Host "✅ Sistema ejecutado exitosamente!" -ForegroundColor Green
Write-Host "💡 Para automatizar, configura en cron: * * * * * php /ruta/artisan schedule:run" -ForegroundColor Cyan
Write-Host "🌐 Para ver el blog: php artisan serve" -ForegroundColor Cyan
