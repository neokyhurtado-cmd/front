# 🚀 PANORAMA IAS - Development Server Orchestrator
# Arranque completo Laravel + Vite en un solo comando
# Uso: .\tools\run.ps1 [-Port 9999] [-SkipBrowser] [-Debug]

param(
    [int]$Port = 9999,
    [switch]$SkipBrowser,
    [switch]$Debug,
    [switch]$KillExisting
)

$ErrorActionPreference = "Continue"

Write-Host "🚀 PANORAMA IAS Development Server" -ForegroundColor Green
Write-Host "Port: $Port | Debug: $Debug | Skip Browser: $SkipBrowser" -ForegroundColor Gray

# Kill existing processes if requested
if ($KillExisting) {
    Write-Host "🔄 Killing existing processes..." -ForegroundColor Yellow
    Get-Process php -ErrorAction SilentlyContinue | Where-Object { $_.ProcessName -eq "php" } | Stop-Process -Force
    Get-Process node -ErrorAction SilentlyContinue | Where-Object { $_.CommandLine -like "*vite*" } | Stop-Process -Force
    Start-Sleep 2
}

# 1) Environment setup
Write-Host "▶ Setting up environment..." -ForegroundColor Cyan
try {
    $envContent = Get-Content .env -Raw
    $newEnvContent = $envContent -replace '^APP_URL=.*', "APP_URL=http://127.0.0.1:$Port"
    Set-Content .env -Value $newEnvContent -NoNewline
    Write-Host "  ✅ APP_URL updated to http://127.0.0.1:$Port" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Could not update .env: $($_.Exception.Message)" -ForegroundColor Yellow
}

# 2) Clear Laravel caches
Write-Host "▶ Clearing Laravel caches..." -ForegroundColor Cyan
try {
    $cacheCommands = @("route:clear", "view:clear", "config:clear")
    foreach ($cmd in $cacheCommands) {
        php artisan $cmd | Out-Null
        Write-Host "  ✅ $cmd completed" -ForegroundColor Green
    }
} catch {
    Write-Host "  ⚠️  Cache clear failed: $($_.Exception.Message)" -ForegroundColor Yellow
}

# 3) Check if port is available
Write-Host "▶ Checking port availability..." -ForegroundColor Cyan
$portInUse = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
if ($portInUse) {
    Write-Host "  ⚠️  Port $Port is already in use" -ForegroundColor Yellow
    if ($KillExisting) {
        Write-Host "  🔄 Attempting to free port..." -ForegroundColor Yellow
        Stop-Process -Id $portInUse.OwningProcess -Force -ErrorAction SilentlyContinue
        Start-Sleep 2
    } else {
        Write-Host "  💡 Use -KillExisting to terminate existing process" -ForegroundColor Cyan
    }
}

# 4) Start PHP server
Write-Host "▶ Starting PHP server..." -ForegroundColor Cyan
try {
    $phpArgs = @("-S", "127.0.0.1:$Port", "-t", "public")
    if ($Debug) {
        Write-Host "  🔍 PHP command: php $($phpArgs -join ' ')" -ForegroundColor Gray
    }
    
    $backend = Start-Process -PassThru -WindowStyle Hidden -FilePath "php" -ArgumentList $phpArgs
    Write-Host "  ✅ PHP server started (PID: $($backend.Id))" -ForegroundColor Green
    
    # Store PID for cleanup
    $backend.Id | Out-File "storage/logs/php-server.pid" -Encoding ASCII
    
} catch {
    Write-Host "  ❌ Failed to start PHP server: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "     Try manually: php -S 127.0.0.1:$Port -t public" -ForegroundColor Yellow
    exit 1
}

# 5) Start Vite (check if already running)
Write-Host "▶ Starting Vite dev server..." -ForegroundColor Cyan
try {
    # Check if Vite is already running on port 5174
    $viteRunning = Get-NetTCPConnection -LocalPort 5174 -State Listen -ErrorAction SilentlyContinue
    
    if ($viteRunning) {
        Write-Host "  ✅ Vite already running on port 5174" -ForegroundColor Green
    } else {
        if ($Debug) {
            Write-Host "  🔍 Starting Vite with: npm run dev" -ForegroundColor Gray
        }
        
        $vite = Start-Process -PassThru -WindowStyle Hidden -FilePath "npm" -ArgumentList @("run", "dev")
        Write-Host "  ✅ Vite dev server started (PID: $($vite.Id))" -ForegroundColor Green
        
        # Store PID for cleanup
        $vite.Id | Out-File "storage/logs/vite-server.pid" -Encoding ASCII
        Start-Sleep 3  # Give Vite time to start
    }
} catch {
    Write-Host "  ⚠️  Could not start Vite: $($_.Exception.Message)" -ForegroundColor Yellow
    Write-Host "     Try manually: npm run dev" -ForegroundColor Cyan
}

# 6) Health check
Write-Host "▶ Running health checks..." -ForegroundColor Cyan
Start-Sleep 2

try {
    $healthResponse = Invoke-WebRequest "http://127.0.0.1:$Port/healthz" -UseBasicParsing -TimeoutSec 5
    if ($healthResponse.StatusCode -eq 200) {
        Write-Host "  ✅ Laravel health check: OK" -ForegroundColor Green
    }
} catch {
    Write-Host "  ❌ Laravel health check failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "     Server might still be starting up..." -ForegroundColor Gray
}

# Check Vite
try {
    $viteResponse = Invoke-WebRequest "http://127.0.0.1:5174" -UseBasicParsing -TimeoutSec 3
    Write-Host "  ✅ Vite dev server: OK" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Vite dev server check failed (this is often normal)" -ForegroundColor Yellow
}

# 7) Quick QA check
Write-Host "▶ Running quick QA validation..." -ForegroundColor Cyan
try {
    $qaResult = & "./tools/continue-qa.ps1" -Mode quick 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  ✅ QA validation passed" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  QA validation has warnings (check with: .\tools\continue-qa.ps1 -Mode quick)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ⚠️  Could not run QA check" -ForegroundColor Yellow
}

# 8) Final status and browser launch
Write-Host ""
Write-Host "🎉 PANORAMA IAS Development Environment Ready!" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Services:" -ForegroundColor Cyan
Write-Host "   Laravel:  http://127.0.0.1:$Port/" -ForegroundColor White
Write-Host "   Vite HMR: http://127.0.0.1:5174" -ForegroundColor White
Write-Host "   Health:   http://127.0.0.1:$Port/healthz" -ForegroundColor White
Write-Host ""
Write-Host "🔧 Development Commands:" -ForegroundColor Cyan
Write-Host "   Quick QA:     .\tools\continue-qa.ps1 -Mode quick" -ForegroundColor Gray
Write-Host "   Safe Commit:  .\tools\safe-commit.ps1 -Message 'feat: ...'" -ForegroundColor Gray
Write-Host "   Stop Servers: .\tools\stop-dev.ps1" -ForegroundColor Gray
Write-Host ""

# Open browser
if (-not $SkipBrowser) {
    Write-Host "🌐 Opening browser..." -ForegroundColor Cyan
    Start-Process "http://127.0.0.1:$Port/"
}

Write-Host "✨ Ready for development! Happy coding! 🚀" -ForegroundColor Green