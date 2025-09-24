# scripts/start-servers.ps1
# Ejecutar desde la raíz del proyecto: powershell -ExecutionPolicy Bypass -File .\scripts\start-servers.ps1
# Detiene servidores antiguos y arranca Laravel (php artisan serve) y Vite (npx vite).

param(
    [string]$BackendDir = "c:/laragon/www/INICIO/crm-vite-react/laravel-backend",
    [string]$FrontendDir = "c:/laragon/www/INICIO/crm-vite-react/frontend",
    [int]$PhpPort = 8095,
    [int]$VitePort = 5173
)

function Write-Sep { param($title) $t = "---- $title " + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss') + " ----"; Write-Host $t -ForegroundColor Cyan }

Write-Sep "Start servers"

# Logs
$logDir = Join-Path $PSScriptRoot 'logs'
if (-not (Test-Path $logDir)) { New-Item -Path $logDir -ItemType Directory | Out-Null }
$backendLog = Join-Path $logDir "laravel_$((Get-Date).ToString('yyyyMMdd_HHmmss')).log"
$viteLog = Join-Path $logDir "vite_$((Get-Date).ToString('yyyyMMdd_HHmmss')).log"
$schedLog = Join-Path $logDir "schedule_$((Get-Date).ToString('yyyyMMdd_HHmmss')).log"

# Stop existing php artisan serve processes that use the backend directory or port
Write-Sep "Stopping existing php processes (artisan serve)"
$phpProcs = Get-CimInstance Win32_Process | Where-Object { $_.Name -match 'php.exe' -or $_.Name -match 'php' }
foreach ($p in $phpProcs) {
    try {
        $cmd = ($p.CommandLine) -join ' '
        if ($cmd -and ($cmd -match 'artisan' -or $cmd -match "--port=$PhpPort" -or $cmd -match [regex]::Escape($BackendDir))) {
            Write-Host "Stopping php pid=$($p.ProcessId) cmd=$cmd" -ForegroundColor Yellow
            Stop-Process -Id $p.ProcessId -Force -ErrorAction SilentlyContinue
        }
    } catch { }
}

Start-Sleep -Milliseconds 400

# Stop Node/Vite processes that are serving on VitePort
Write-Sep "Stopping existing node/vite processes"
$nodeProcs = Get-CimInstance Win32_Process | Where-Object { $_.Name -match 'node.exe' -or $_.Name -match 'node' }
foreach ($n in $nodeProcs) {
    try {
        $cmd = ($n.CommandLine) -join ' '
        if ($cmd -and ($cmd -match 'vite' -or $cmd -match "--port=$VitePort" -or $cmd -match [regex]::Escape($FrontendDir))) {
            Write-Host "Stopping node pid=$($n.ProcessId) cmd=$cmd" -ForegroundColor Yellow
            Stop-Process -Id $n.ProcessId -Force -ErrorAction SilentlyContinue
        }
    } catch { }
}

Start-Sleep -Milliseconds 400

# Start Laravel dev server using cmd.exe to redirect output
Write-Sep "Starting Laravel (php artisan serve)"
$phpCmd = "cd /d `"$BackendDir`" && php artisan serve --host=127.0.0.1 --port $PhpPort"
$phpArgs = @('/c', "$phpCmd > `"$backendLog`" 2>&1")
try {
    $phpProc = Start-Process -FilePath 'cmd.exe' -ArgumentList $phpArgs -WindowStyle Hidden -PassThru
    Write-Host "Laravel started pid=$($phpProc.Id) port=$PhpPort log=$backendLog" -ForegroundColor Green
} catch {
    Write-Host "Failed to start Laravel via cmd.exe: $_" -ForegroundColor Red
}

Start-Sleep -Seconds 1

# Start Vite dev server using cmd /c to ensure npx works in Windows environment
Write-Sep "Starting Vite (npx vite)"
$cmd = "cd /d `"$FrontendDir`" && npx vite --port $VitePort"
# Use cmd.exe to run and redirect output to log file
$arguments = @('/c', "$cmd > `"$viteLog`" 2>&1")
try {
    $viteProc = Start-Process -FilePath 'cmd.exe' -ArgumentList $arguments -WindowStyle Hidden -PassThru
    Write-Host "Vite started pid=$($viteProc.Id) port=$VitePort log=$viteLog" -ForegroundColor Green
} catch {
    Write-Host "Failed to start Vite: $_" -ForegroundColor Red
}

# Start schedule worker (runs scheduled commands)
Write-Sep "Starting schedule:work"
$schedCmd = "cd /d `"$BackendDir`" && php artisan schedule:work"
$schedArgs = @('/c', "$schedCmd > `"$schedLog`" 2>&1")
try {
    $schedProc = Start-Process -FilePath 'cmd.exe' -ArgumentList $schedArgs -WindowStyle Hidden -PassThru
    Write-Host "Schedule worker started pid=$($schedProc.Id) log=$schedLog" -ForegroundColor Green
} catch {
    Write-Host "Failed to start schedule worker: $_" -ForegroundColor Red
}

Write-Sep "Servers launched"
Write-Host "Laravel log: $backendLog"
Write-Host "Vite log:    $viteLog"
Write-Host "Schedule log: $schedLog"
Write-Host "To stop servers: stop-process -Id <pid> or close the processes from Task Manager"

return @{ phpPid = $phpProc.Id; vitePid = $viteProc.Id; schedPid = $schedProc.Id; backendLog = $backendLog; viteLog = $viteLog; schedLog = $schedLog }
