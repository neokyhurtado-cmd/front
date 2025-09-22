# start-laravel-only.ps1
# Stops existing php/node processes (best-effort) and launches a single PowerShell window
# that runs `php artisan serve` from the `laravel-backend` folder.

param(
  [int]$LaravelPort = 8095
)

Write-Host "Stopping any stray node/php processes (best-effort)..."
Get-Process node, php -ErrorAction SilentlyContinue | ForEach-Object {
  try { Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue; Write-Host "Stopped process $($_.Id) $($_.ProcessName)" } catch { }
}

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$repoRoot = Split-Path -Parent $scriptDir
$laravelPath = Join-Path $repoRoot 'laravel-backend'

if (-not (Test-Path $laravelPath)) {
  Write-Error "No se encontró la carpeta laravel-backend en: $laravelPath"
  exit 1
}

Write-Host "Starting Laravel at http://127.0.0.1:$LaravelPort"
$args = @('-NoExit','-Command', "Set-Location -LiteralPath '$laravelPath'; php artisan serve --host=127.0.0.1 --port=$LaravelPort")
Start-Process -FilePath powershell -ArgumentList $args -WindowStyle Normal | Out-Null
Write-Host "Laravel window started. Open http://127.0.0.1:$LaravelPort/tailwind-news"
