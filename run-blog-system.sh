#!/bin/bash
# Script para ejecutar el sistema de blog automatizado
# Panorama Ingeniería IA

echo "🚀 Sistema de Blog Automatizado - Panorama Ingeniería IA"
echo "========================================================="

echo "📡 1. Trayendo feeds RSS y procesando con IA..."
php artisan feeds:fetch

echo "⚙️ 2. Procesando contenido con IA (5 posts)..."
php artisan queue:work --max-jobs=5

echo "📅 3. Programando posts para las franjas del día..."
php artisan posts:schedule-daily

echo "📊 4. Estado actual del sistema:"
php artisan posts:rotate-monthly

echo ""
echo "✅ Sistema ejecutado exitosamente!"
echo "💡 Para automatizar, configura en cron: * * * * * php /ruta/artisan schedule:run"
echo "🌐 Para ver el blog: php artisan serve"
