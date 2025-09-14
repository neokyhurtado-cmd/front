# 🛑 PANORAMA IAS - Stop Development Servers
# Para los servidores PHP y Vite de manera limpia
# Uso: .\tools\stop-dev.ps1 [-Force] [-KeepVite]

param(
    [switch]$Force,
    [switch]$KeepVite
)

Write-Host "🛑 Stopping PANORAMA IAS Development Servers" -ForegroundColor Red

$processesKilled = 0

# 1) Stop PHP server using PID file
Write-Host "▶ Stopping PHP server..." -ForegroundColor Cyan
try {
    if (Test-Path "storage/logs/php-server.pid") {
        $phpPid = Get-Content "storage/logs/php-server.pid" -Raw | Where-Object { $_.Trim() -ne "" }
        if ($phpPid) {
            $phpProcess = Get-Process -Id $phpPid -ErrorAction SilentlyContinue
            if ($phpProcess) {
                Stop-Process -Id $phpPid -Force
                Write-Host "  ✅ Stopped PHP server (PID: $phpPid)" -ForegroundColor Green
                $processesKilled++
            } else {
                Write-Host "  ℹ️  PHP server process not found (already stopped)" -ForegroundColor Gray
            }
        }
        Remove-Item "storage/logs/php-server.pid" -ErrorAction SilentlyContinue
    }
} catch {
    Write-Host "  ⚠️  Could not stop PHP server via PID: $($_.Exception.Message)" -ForegroundColor Yellow
}

# 2) Fallback: kill PHP processes by name
if ($Force) {
    Write-Host "▶ Force killing PHP processes..." -ForegroundColor Yellow
    $phpProcesses = Get-Process php -ErrorAction SilentlyContinue
    foreach ($proc in $phpProcesses) {
        try {
            Stop-Process -Id $proc.Id -Force
            Write-Host "  🔪 Force killed PHP process (PID: $($proc.Id))" -ForegroundColor Yellow
            $processesKilled++
        } catch {
            Write-Host "  ❌ Could not kill PHP process $($proc.Id)" -ForegroundColor Red
        }
    }
}

# 3) Stop Vite server
if (-not $KeepVite) {
    Write-Host "▶ Stopping Vite dev server..." -ForegroundColor Cyan
    try {
        if (Test-Path "storage/logs/vite-server.pid") {
            $vitePid = Get-Content "storage/logs/vite-server.pid" -Raw | Where-Object { $_.Trim() -ne "" }
            if ($vitePid) {
                $viteProcess = Get-Process -Id $vitePid -ErrorAction SilentlyContinue
                if ($viteProcess) {
                    Stop-Process -Id $vitePid -Force
                    Write-Host "  ✅ Stopped Vite server (PID: $vitePid)" -ForegroundColor Green
                    $processesKilled++
                } else {
                    Write-Host "  ℹ️  Vite server process not found (already stopped)" -ForegroundColor Gray
                }
            }
            Remove-Item "storage/logs/vite-server.pid" -ErrorAction SilentlyContinue
        }
    } catch {
        Write-Host "  ⚠️  Could not stop Vite server via PID: $($_.Exception.Message)" -ForegroundColor Yellow
    }

    # Fallback: kill Node processes running Vite
    if ($Force) {
        Write-Host "▶ Force killing Node/Vite processes..." -ForegroundColor Yellow
        $nodeProcesses = Get-Process node -ErrorAction SilentlyContinue | Where-Object { 
            $_.CommandLine -like "*vite*" -or $_.CommandLine -like "*dev*" 
        }
        foreach ($proc in $nodeProcesses) {
            try {
                Stop-Process -Id $proc.Id -Force
                Write-Host "  🔪 Force killed Node process (PID: $($proc.Id))" -ForegroundColor Yellow
                $processesKilled++
            } catch {
                Write-Host "  ❌ Could not kill Node process $($proc.Id)" -ForegroundColor Red
            }
        }
    }
} else {
    Write-Host "  ℹ️  Keeping Vite server running (--KeepVite flag)" -ForegroundColor Gray
}

# 4) Check port usage
Write-Host "▶ Checking port usage..." -ForegroundColor Cyan
$ports = @(9999, 5174, 8000, 7070)
$portsInUse = @()

foreach ($port in $ports) {
    $connection = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue
    if ($connection) {
        $portsInUse += $port
        Write-Host "  ⚠️  Port $port still in use" -ForegroundColor Yellow
    }
}

if ($portsInUse.Count -eq 0) {
    Write-Host "  ✅ All common ports are free" -ForegroundColor Green
} else {
    Write-Host "  💡 To free ports forcefully: .\tools\stop-dev.ps1 -Force" -ForegroundColor Cyan
}

# 5) Summary
Write-Host ""
if ($processesKilled -gt 0) {
    Write-Host "🎯 Stopped $processesKilled process(es)" -ForegroundColor Green
} else {
    Write-Host "ℹ️  No active development servers found" -ForegroundColor Gray
}

Write-Host ""
Write-Host "🔧 Next steps:" -ForegroundColor Cyan
Write-Host "   Start servers: .\tools\run.ps1" -ForegroundColor Gray
Write-Host "   Check status:  Get-Process php,node -ErrorAction SilentlyContinue" -ForegroundColor Gray
Write-Host ""
Write-Host "✅ Development environment cleaned!" -ForegroundColor Green