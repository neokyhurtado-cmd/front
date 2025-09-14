# Script "A PRUEBA DE BALAS" - Auto detección de puertos libres
# Ejecutar: .\start-dev.ps1

Write-Host "🚀 SCRIPT A PRUEBA DE BALAS - Iniciando..." -ForegroundColor Green

# Paso 1: Cierra todo lo previo
Write-Host "🔥 Matando procesos colgados..." -ForegroundColor Yellow
taskkill /F /IM php.exe /T >$null 2>&1
taskkill /F /IM node.exe /T >$null 2>&1

# Paso 2: Encuentra puerto libre automáticamente
Write-Host "🔍 Buscando puerto libre..." -ForegroundColor Cyan
$ports = @(7070, 8080, 19090, 18080)
$phpPort = $null

foreach ($p in $ports) {
    $inUse = netstat -ano | findstr ":$p"
    if (-not $inUse) { 
        $phpPort = $p
        Write-Host "✅ Puerto libre encontrado: $phpPort" -ForegroundColor Green
        break 
    }
}

# Si todos están ocupados, usa puerto alto
if (-not $phpPort) { 
    $phpPort = 20000 
    Write-Host "⚠️  Usando puerto alto: $phpPort" -ForegroundColor Yellow
}

# Paso 3: Lanza PHP en ventana separada (método directo)
Write-Host "🔥 Arrancando PHP embebido en 127.0.0.1:$phpPort..." -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; Write-Host 'PHP SERVER - 127.0.0.1:$phpPort' -ForegroundColor Green; php -S 127.0.0.1:$phpPort -t public" -WindowStyle Normal

Start-Sleep -Seconds 1

# Paso 4: Lanza Vite en ventana separada
Write-Host "⚡ Arrancando Vite HMR..." -ForegroundColor Blue
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; Write-Host 'VITE HMR - 127.0.0.1:5174' -ForegroundColor Blue; npm run dev" -WindowStyle Normal

# Paso 5: Abrir navegador en /healthz para verificar
Start-Sleep -Seconds 2
Write-Host "🌐 Abriendo navegador con healthcheck..." -ForegroundColor Magenta
Start-Process "http://127.0.0.1:$phpPort/healthz"

Write-Host ""
Write-Host "🎉 ¡SERVIDORES LANZADOS!" -ForegroundColor Green
Write-Host "📍 PHP Server: http://127.0.0.1:$phpPort" -ForegroundColor White
Write-Host "🩺 Health Check: http://127.0.0.1:$phpPort/healthz" -ForegroundColor Green
Write-Host "⚡ Vite HMR: http://127.0.0.1:5174 (solo HMR)" -ForegroundColor White
Write-Host ""
Write-Host "💡 Si /healthz muestra 'ok' → servidor funcionando" -ForegroundColor Yellow
Write-Host "💡 Si /healthz falla → problema de servidor/puerto" -ForegroundColor Yellow
Write-Host "🔄 Para reiniciar: .\start-dev.ps1" -ForegroundColor Cyan