# start-n8n-no-docker.ps1
# Script optimizado para iniciar n8n sin Docker en entorno Laragon
# Carga variables de entorno desde .env y configura n8n correctamente

param(
    [switch]$NoAuth,          # -NoAuth para deshabilitar autenticación básica
    [switch]$ImportWorkflows, # -ImportWorkflows para importar workflows automáticamente
    [switch]$Background,      # -Background para ejecutar en segundo plano
    [int]$Port = 5678        # Puerto personalizado para n8n
)

# Configuración de rutas
$LARAVEL_DIR = "C:\laragon\www\INICIO\crm-vite-react\laravel-backend"
$WORKFLOWS_DIR = "$LARAVEL_DIR\n8n-workflows"
$ENV_FILE = "$LARAVEL_DIR\.env"

Write-Host "=== INICIANDO N8N SIN DOCKER ===" -ForegroundColor Green
Write-Host "Laravel Dir: $LARAVEL_DIR" -ForegroundColor Cyan
Write-Host "Workflows Dir: $WORKFLOWS_DIR" -ForegroundColor Cyan
Write-Host "Puerto N8N: $Port" -ForegroundColor Cyan

# Verificar que las rutas existen
if (!(Test-Path $LARAVEL_DIR)) {
    Write-Error "No se encuentra el directorio de Laravel: $LARAVEL_DIR"
    exit 1
}

if (!(Test-Path $WORKFLOWS_DIR)) {
    Write-Warning "No se encuentra el directorio de workflows: $WORKFLOWS_DIR"
}

# Cambiar al directorio de Laravel
Set-Location $LARAVEL_DIR

# Cargar variables de entorno desde .env
Write-Host "`nCargando variables de entorno desde .env..." -ForegroundColor Yellow
if (Test-Path $ENV_FILE) {
    Get-Content $ENV_FILE | ForEach-Object {
        $line = $_.Trim()
        if ($line -and -not $line.StartsWith('#')) {
            if ($line -match '^\s*([^=]+?)\s*=\s*(.*)$') {
                $key = $matches[1].Trim()
                $value = $matches[2].Trim()
                
                # Limpiar comillas
                if ($value.StartsWith('"') -and $value.EndsWith('"')) {
                    $value = $value.Substring(1, $value.Length - 2)
                } elseif ($value.StartsWith("'") -and $value.EndsWith("'")) {
                    $value = $value.Substring(1, $value.Length - 2)
                }
                
                # Exportar variables relevantes para n8n
                $n8nVars = @(
                    'APP_URL', 'FRONTEND_URL', 'N8N_TO_LARAVEL_TOKEN', 'N8N_WEBHOOK_TOKEN',
                    'RSS_FEED_URL', 'RSS_FEED_URL_MOVILIDAD', 'RSS_FEED_URL_TECNOLOGIA',
                    'OPENAI_API_KEY', 'GEMINI_API_KEY', 'PEXELS_API_KEY'
                )
                
                if ($key -in $n8nVars) {
                    Set-Item -Path "Env:\$key" -Value $value
                    $masked = if ($key.Contains('TOKEN') -or $key.Contains('KEY')) {
                        if ($value.Length -gt 8) { $value.Substring(0,4) + '...' + $value.Substring($value.Length-4) } else { '****' }
                    } else { $value }
                    Write-Host "  ✓ $key = $masked" -ForegroundColor Green
                }
            }
        }
    }
} else {
    Write-Warning "Archivo .env no encontrado en $ENV_FILE"
}

# Configurar variables específicas para n8n sin Docker
Write-Host "`nConfigurando variables específicas para n8n..." -ForegroundColor Yellow

# Configuración básica
$env:N8N_HOST = "127.0.0.1"
$env:N8N_PORT = $Port.ToString()
$env:N8N_PROTOCOL = "http"

# Deshabilitar autenticación si se especifica
if ($NoAuth) {
    $env:N8N_BASIC_AUTH_ACTIVE = "false"
    Write-Host "  ✓ Autenticación básica deshabilitada" -ForegroundColor Green
} else {
    # Configurar autenticación básica por defecto
    if (-not $env:N8N_BASIC_AUTH_USER) { $env:N8N_BASIC_AUTH_USER = "admin" }
    if (-not $env:N8N_BASIC_AUTH_PASSWORD) { $env:N8N_BASIC_AUTH_PASSWORD = "admin123" }
    $env:N8N_BASIC_AUTH_ACTIVE = "true"
    Write-Host "  ✓ Autenticación básica habilitada (admin/admin123)" -ForegroundColor Yellow
}

# Configurar proxy bypass para desarrollo local
$noProxyList = @('127.0.0.1', 'localhost', '::1', 'localhost.localdomain')
$env:NO_PROXY = $noProxyList -join ','
$env:no_proxy = $noProxyList -join ','
Write-Host "  ✓ NO_PROXY configurado: $($env:NO_PROXY)" -ForegroundColor Green

# Configurar carpeta de usuario n8n
$n8nUserFolder = "$env:USERPROFILE\.n8n"
$env:N8N_USER_FOLDER = $n8nUserFolder
Write-Host "  ✓ Carpeta de usuario n8n: $n8nUserFolder" -ForegroundColor Green

# Configurar logs
$env:N8N_LOG_LEVEL = "info"
$env:N8N_LOG_OUTPUT = "console"

# Configurar seguridad para desarrollo
$env:N8N_SECURE_COOKIE = "false"
$env:N8N_DISABLE_UI = "false"

Write-Host "`nVerificando instalación de n8n..." -ForegroundColor Yellow
try {
    $n8nVersion = & n8n --version 2>$null
    Write-Host "  ✓ n8n encontrado: $n8nVersion" -ForegroundColor Green
} catch {
    Write-Error "n8n no está instalado. Instálalo con: npm install -g n8n"
    exit 1
}

# Función para esperar que n8n responda
function Wait-N8nReady {
    param([int]$TimeoutSeconds = 60)
    
    Write-Host "Esperando que n8n responda en http://127.0.0.1:$Port..." -ForegroundColor Yellow
    $timeout = (Get-Date).AddSeconds($TimeoutSeconds)
    
    while ((Get-Date) -lt $timeout) {
        try {
            $response = Invoke-WebRequest -Uri "http://127.0.0.1:$Port" -UseBasicParsing -TimeoutSec 2 -ErrorAction Stop
            if ($response.StatusCode -eq 200) {
                Write-Host "  ✓ n8n está respondiendo" -ForegroundColor Green
                return $true
            }
        } catch {
            Start-Sleep -Seconds 1
        }
    }
    
    Write-Warning "Timeout esperando que n8n responda"
    return $false
}

# Función para importar workflows
function Import-Workflows {
    Write-Host "`nImportando workflows desde $WORKFLOWS_DIR..." -ForegroundColor Yellow
    
    if (!(Test-Path $WORKFLOWS_DIR)) {
        Write-Warning "Directorio de workflows no encontrado: $WORKFLOWS_DIR"
        return
    }
    
    $workflowFiles = Get-ChildItem "$WORKFLOWS_DIR\*.json" | Where-Object { $_.Name -notlike "*check-env*" }
    
    foreach ($file in $workflowFiles) {
        try {
            $workflowContent = Get-Content $file.FullName -Raw | ConvertFrom-Json
            $workflowName = $workflowContent.name
            Write-Host "  → Importando: $workflowName ($($file.Name))" -ForegroundColor Cyan
            
            # Aquí puedes agregar lógica para importar via API REST de n8n
            # Por ahora solo mostramos el archivo
            
        } catch {
            Write-Warning "  ✗ Error importando $($file.Name): $($_.Exception.Message)"
        }
    }
}

# Iniciar n8n
Write-Host "`n=== INICIANDO N8N ===" -ForegroundColor Green
Write-Host "Comando: n8n start --port $Port" -ForegroundColor Cyan

if ($Background) {
    Write-Host "Iniciando n8n en segundo plano..." -ForegroundColor Yellow
    $process = Start-Process -FilePath "n8n" -ArgumentList "start", "--port", $Port -NoNewWindow -PassThru
    Write-Host "  ✓ Proceso iniciado con PID: $($process.Id)" -ForegroundColor Green
    
    # Esperar que esté listo
    if (Wait-N8nReady) {
        Write-Host "`n=== N8N LISTO ===" -ForegroundColor Green
        Write-Host "URL: http://127.0.0.1:$Port" -ForegroundColor Cyan
        
        if ($ImportWorkflows) {
            Import-Workflows
        }
        
        Write-Host "`nPara detener n8n, usa: Stop-Process -Id $($process.Id)" -ForegroundColor Yellow
    }
} else {
    Write-Host "Iniciando n8n en primer plano (Ctrl+C para detener)..." -ForegroundColor Yellow
    
    if ($ImportWorkflows) {
        # Iniciar en background temporalmente para importar workflows
        $tempProcess = Start-Process -FilePath "n8n" -ArgumentList "start", "--port", $Port -NoNewWindow -PassThru
        
        if (Wait-N8nReady -TimeoutSeconds 30) {
            Import-Workflows
            Stop-Process -Id $tempProcess.Id -Force
            Start-Sleep -Seconds 2
        }
    }
    
    Write-Host "`n=== N8N INICIANDO ===" -ForegroundColor Green
    Write-Host "URL: http://127.0.0.1:$Port" -ForegroundColor Cyan
    Write-Host "Presiona Ctrl+C para detener" -ForegroundColor Yellow
    
    # Ejecutar en primer plano
    & n8n start --port $Port
}

Write-Host "`nScript finalizado." -ForegroundColor Green