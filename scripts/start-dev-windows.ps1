<# start-dev-windows.ps1 #>
# Script mejorado para iniciar todo el entorno de desarrollo sin Docker

# ==== CONFIGURACIÓN DE RUTAS ====
$LARAVEL_DIR = "c:\laragon\www\INICIO\crm-vite-react\laravel-backend" # ← tu backend Laravel
$FRONTEND_DIR = "c:\laragon\www\INICIO\crm-vite-react\frontend"        # ← frontend Vite
$SCRIPTS_DIR = "c:\laragon\www\INICIO\crm-vite-react\scripts"          # ← scripts del proyecto

# ==== CONFIGURACIÓN DE PUERTOS ====
$LARAVEL_PORT = 8095
$VITE_PORT    = 5174
$N8N_PORT     = 5678

Write-Host "=== INICIANDO ENTORNO DE DESARROLLO SIN DOCKER ===" -ForegroundColor Green
Write-Host "Laravel: http://127.0.0.1:$LARAVEL_PORT" -ForegroundColor Cyan
Write-Host "Frontend: http://127.0.0.1:$VITE_PORT" -ForegroundColor Cyan  
Write-Host "n8n: http://127.0.0.1:$N8N_PORT" -ForegroundColor Cyan

function Assert-Dir([string]$path, [string]$label) {
  if (!(Test-Path $path)) { throw "La carpeta de $label no existe: $path" }
}

# 1) Validar rutas
Assert-Dir $LARAVEL_DIR "Laravel"
Assert-Dir $FRONTEND_DIR "Frontend"
Assert-Dir $SCRIPTS_DIR "Scripts"

# 2) Asegurar .env Laravel
$envFile = Join-Path $LARAVEL_DIR ".env"
$envExample = Join-Path $LARAVEL_DIR ".env.example"
if (!(Test-Path $envFile)) {
  if (Test-Path $envExample) {
    Copy-Item $envExample $envFile
    Write-Host ".env creado desde .env.example en $LARAVEL_DIR"
  } else {
    Write-Warning ".env no existe y tampoco .env.example en $LARAVEL_DIR (créalo tú o ajusta la ruta)"
  }
}

# 3) Comandos
$laravelCmd = "php artisan serve --host 127.0.0.1 --port $LARAVEL_PORT"
$frontendCmd = "npm run dev -- --host --port $VITE_PORT"
$n8nCmd = "n8n start --port $N8N_PORT"

# 4) Levantar ventanas con títulos descriptivos
Write-Host "`nIniciando servicios..." -ForegroundColor Yellow

# Laravel Backend
Start-Process -FilePath pwsh -WorkingDirectory $LARAVEL_DIR -ArgumentList '-NoExit','-Command', "Write-Host 'LARAVEL BACKEND - Puerto $LARAVEL_PORT' -ForegroundColor Green; $laravelCmd"

# Frontend Vite 
Start-Process -FilePath pwsh -WorkingDirectory $FRONTEND_DIR -ArgumentList '-NoExit','-Command', "Write-Host 'FRONTEND VITE - Puerto $VITE_PORT' -ForegroundColor Blue; $frontendCmd"

# n8n con script mejorado
$n8nScript = "$SCRIPTS_DIR\start-n8n-no-docker.ps1"
if (Test-Path $n8nScript) {
    Start-Process -FilePath pwsh -WorkingDirectory $SCRIPTS_DIR -ArgumentList '-NoExit','-Command', "& '$n8nScript' -Port $N8N_PORT -NoAuth"
} else {
    # Fallback al método anterior
    Start-Process -FilePath pwsh -WorkingDirectory $LARAVEL_DIR -ArgumentList '-NoExit','-Command', "Write-Host 'N8N - Puerto $N8N_PORT' -ForegroundColor Magenta; $n8nCmd"
}

Write-Host "`n=== SERVICIOS INICIADOS ===" -ForegroundColor Green
Write-Host "Todas las ventanas de desarrollo están abiertas." -ForegroundColor White
Write-Host "Verifica que todos los servicios inicien correctamente." -ForegroundColor Yellow