# Start-all-windows.ps1
# Stops existing php/node processes (optional) and launches three PowerShell windows:
#  - Laravel backend (php artisan serve --host 127.0.0.1 --port 8095)
#  - Frontend Vite (npm run dev) in ./frontend
#  - n8n (npx n8n) on port 5678

param(
  [int]$LaravelPort = 8095,
  [int]$VitePort = 5174,
  [int]$N8nPort = 5678
)

Write-Host "Stopping any existing vite/php/node processes (this is best-effort)..."
# Attempt to stop common processes (node, php) launched from this workspace.
Get-Process node, php -ErrorAction SilentlyContinue | Where-Object { $_.Path -like '*node*' -or $_.Path -like '*php*' } | ForEach-Object {
  try { Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue; Write-Host "Stopped process $($_.Id) $($_.ProcessName)" } catch { }
}

function Start-Window($title, $script, $cwd) {
  $args = @('-NoExit','-Command', "Set-Location -LiteralPath '$cwd'; $script")
  Start-Process -FilePath powershell -ArgumentList $args -WindowStyle Normal | Out-Null
  Write-Host "Started: $title"
}

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$repoRoot = Split-Path -Parent $scriptDir

Write-Host "Launching Laravel backend on http://127.0.0.1:$LaravelPort"
Start-Window "Laravel" "php artisan serve --host=127.0.0.1 --port=$LaravelPort" (Join-Path $repoRoot 'laravel-backend')

Write-Host "Launching Frontend (Vite) in $repoRoot\frontend on port $VitePort"
Start-Window "Frontend" "npm install; npm run dev -- --host --port $VitePort" (Join-Path $repoRoot 'frontend')

Write-Host "Launching n8n on port $N8nPort"
Start-Window "n8n" "npx n8n start --port $N8nPort" $repoRoot

Write-Host "All windows started. Wait a few seconds and then open the URLs in your browser."
