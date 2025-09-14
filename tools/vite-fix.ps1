# tools/vite-fix.ps1
# Script específico para diagnosticar y arreglar problemas de Vite en Laravel

param(
    [switch]$Fix = $false,  # Si está presente, aplica correcciones automáticamente
    [switch]$Dev = $false   # Si está presente, inicia modo desarrollo
)

$ErrorActionPreference = "Stop"

Write-Host "🔍 DIAGNÓSTICO VITE + LARAVEL" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# 1. Buscar @vite problemáticas
Write-Host "`n1️⃣ Buscando llamadas @vite problemáticas..."
$orphanVite = Select-String -Path "resources/**/*.blade.php" -Pattern "^\s*@vite\s*$" -ErrorAction SilentlyContinue
$emptyVite = Select-String -Path "resources/**/*.blade.php" -Pattern "@vite\(\s*\[\s*\]\s*\)" -ErrorAction SilentlyContinue

if ($orphanVite) {
    Write-Host "❌ @vite sin argumentos encontradas:" -ForegroundColor Red
    $orphanVite | ForEach-Object { Write-Host "   $($_.Filename):$($_.LineNumber)" -ForegroundColor Yellow }
}

if ($emptyVite) {
    Write-Host "❌ @vite con arrays vacíos:" -ForegroundColor Red
    $emptyVite | ForEach-Object { Write-Host "   $($_.Filename):$($_.LineNumber)" -ForegroundColor Yellow }
}

if (-not $orphanVite -and -not $emptyVite) {
    Write-Host "✅ Todas las llamadas @vite son válidas" -ForegroundColor Green
}

# 2. Verificar archivos referenciados en @vite existen
Write-Host "`n2️⃣ Verificando archivos en @vite..."
$viteMatches = Select-String -Path "resources/**/*.blade.php" -Pattern "@vite\(\s*\[(.*?)\]\s*\)" -ErrorAction SilentlyContinue
$missingFiles = @()

foreach ($match in $viteMatches) {
    $filesList = $match.Matches[0].Groups[1].Value
    # Extraer archivos entre comillas
    $files = [regex]::Matches($filesList, "['`"]([^'`"]+)['`"]") | ForEach-Object { $_.Groups[1].Value }
    
    foreach ($file in $files) {
        if (-not (Test-Path $file)) {
            $missingFiles += @{ File = $file; Location = "$($match.Filename):$($match.LineNumber)" }
            Write-Host "❌ Archivo no existe: $file" -ForegroundColor Red
            Write-Host "   Referenciado en: $($match.Filename):$($match.LineNumber)" -ForegroundColor Yellow
        }
    }
}

if ($missingFiles.Count -eq 0) {
    Write-Host "✅ Todos los archivos referenciados existen" -ForegroundColor Green
}

# 3. Verificar vite.config.js
Write-Host "`n3️⃣ Verificando configuración Vite..."
if (-not (Test-Path "vite.config.js")) {
    Write-Host "❌ vite.config.js no existe" -ForegroundColor Red
} else {
    $viteConfig = Get-Content "vite.config.js" -Raw
    if ($viteConfig -notmatch "laravel\s*\(\s*\{[^}]*input\s*:") {
        Write-Host "⚠️  vite.config.js no define inputs claramente" -ForegroundColor Yellow
    } else {
        Write-Host "✅ vite.config.js parece correcto" -ForegroundColor Green
    }
}

# 4. Verificar package.json y node_modules
Write-Host "`n4️⃣ Verificando dependencias Node..."
if (-not (Test-Path "package.json")) {
    Write-Host "❌ package.json no existe" -ForegroundColor Red
} elseif (-not (Test-Path "node_modules")) {
    Write-Host "⚠️  node_modules no existe, ejecutar: npm install" -ForegroundColor Yellow
} else {
    Write-Host "✅ Dependencias Node OK" -ForegroundColor Green
}

# 5. Limpiar cachés si hay problemas
if ($orphanVite -or $emptyVite -or $missingFiles.Count -gt 0) {
    Write-Host "`n🧹 Problemas detectados, limpiando cachés..."
    php artisan view:clear | Out-Null
    php artisan config:clear | Out-Null
    
    # Limpiar vistas compiladas manualmente
    $compiledViews = "storage/framework/views"
    if (Test-Path $compiledViews) {
        Get-ChildItem $compiledViews -Filter "*.php" | Remove-Item -Force -ErrorAction SilentlyContinue
        Write-Host "✅ Vistas compiladas eliminadas" -ForegroundColor Green
    }
}

# 6. Aplicar correcciones automáticas si se solicita
if ($Fix) {
    Write-Host "`n🔧 APLICANDO CORRECCIONES AUTOMÁTICAS..." -ForegroundColor Magenta
    
    # Crear archivos faltantes básicos si no existen
    if (-not (Test-Path "resources/css/app.css")) {
        @"
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Variables básicas */
:root {
  --bg: #ffffff;
  --fg: #0f172a;
  --card: #ffffff;
  --card-border: #e2e8f0;
}

.dark {
  --bg: #0b1220;
  --fg: #e5e7eb;
  --card: #0f1629;
  --card-border: #1f2937;
}
"@ | Out-File -FilePath "resources/css/app.css" -Encoding UTF8
        Write-Host "✅ Creado resources/css/app.css básico" -ForegroundColor Green
    }
    
    if (-not (Test-Path "resources/js/app.js")) {
        @"
import './bootstrap';

// Tema básico
const theme = localStorage.getItem('theme') || 'light';
document.documentElement.classList.toggle('dark', theme === 'dark');

window.toggleTheme = function() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
};
"@ | Out-File -FilePath "resources/js/app.js" -Encoding UTF8
        Write-Host "✅ Creado resources/js/app.js básico" -ForegroundColor Green
    }
}

# 7. Iniciar modo desarrollo si se solicita
if ($Dev) {
    Write-Host "`n🚀 INICIANDO MODO DESARROLLO..." -ForegroundColor Magenta
    Write-Host "Ejecutando npm run dev..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "cd '$PWD'; npm run dev; Read-Host 'Presiona Enter para cerrar'"
    Start-Sleep 2
    Write-Host "✅ Vite dev server iniciado en proceso separado" -ForegroundColor Green
}

# Resumen
Write-Host "`n📋 RESUMEN:" -ForegroundColor Cyan
Write-Host "=========" -ForegroundColor Cyan
if ($orphanVite -or $emptyVite) {
    Write-Host "❌ Hay llamadas @vite problemáticas que necesitan corrección manual" -ForegroundColor Red
}
if ($missingFiles.Count -gt 0) {
    Write-Host "❌ Hay $($missingFiles.Count) archivos faltantes referenciados en @vite" -ForegroundColor Red
}
if (-not $orphanVite -and -not $emptyVite -and $missingFiles.Count -eq 0) {
    Write-Host "✅ No hay problemas detectados con Vite" -ForegroundColor Green
}

Write-Host "`n🔗 COMANDOS ÚTILES:" -ForegroundColor Cyan
Write-Host "   ./tools/vite-fix.ps1 -Fix        # Aplicar correcciones automáticas" -ForegroundColor Gray
Write-Host "   ./tools/vite-fix.ps1 -Dev        # Iniciar modo desarrollo" -ForegroundColor Gray
Write-Host "   npm run dev                      # Servidor desarrollo Vite" -ForegroundColor Gray
Write-Host "   npm run build                    # Build para producción" -ForegroundColor Gray
Write-Host "   php artisan view:clear           # Limpiar vistas compiladas" -ForegroundColor Gray