$ErrorActionPreference = "Stop"

# A. Estado base
Write-Host "A) Estado base..."
if (-not (Test-Path ".env")) { throw ".env no existe" }
# Verificar APP_URL correcto para puerto actual
$appUrl = (Get-Content .env | Where-Object { $_ -match "^APP_URL=" }) -replace "APP_URL=", ""
if (-not ($appUrl -match "http://127\.0\.0\.1:(9000|7070|8000)")) { 
    throw "APP_URL debe ser http://127.0.0.1:PUERTO válido, actual: $appUrl" 
}

# A1. Validación anti-Vite: buscar @vite huérfanas
Write-Host "A1) Verificando @vite calls..."
$bladeFiles = Get-ChildItem -Recurse -Path resources -Filter "*.blade.php" -ErrorAction SilentlyContinue

foreach ($file in $bladeFiles) {
    $content = Get-Content $file.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -match "^\s*@vite\s*$") { 
        throw "❌ @vite sin argumentos en: $($file.FullName)" 
    }
    if ($content -match "@vite\(\s*\[\s*\]\s*\)") { 
        throw "❌ @vite con array vacío en: $($file.FullName)" 
    }
}

# A2. Verificar archivos básicos existen
if (-not (Test-Path "resources/css/app.css")) { throw "❌ resources/css/app.css no existe" }
if (-not (Test-Path "resources/js/app.js")) { throw "❌ resources/js/app.js no existe" }

# B. PHP/Laravel
Write-Host "B) PHP/Laravel..."
php -v | Out-Null

# B1. Limpiar cachés completamente
Write-Host "B1) Limpiando cachés..."
php artisan view:clear | Out-Null
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null

# B2. Limpiar vistas compiladas manualmente si existen
$compiledViews = "storage/framework/views"
if (Test-Path $compiledViews) {
    Get-ChildItem $compiledViews -Filter "*.php" | Remove-Item -Force -ErrorAction SilentlyContinue
}

# B3. Validar estructura Laravel
php artisan view:cache | Out-Null
php artisan route:list | Out-Null

# C. Blade/plantillas
Write-Host "C) Blade/plantillas..."

# C1. Validar partials (sin directivas de sección)
if (Test-Path "resources/views/partials") {
    $partials = Get-ChildItem -Recurse resources/views/partials -Filter *.blade.php
    foreach ($f in $partials) {
        $bad = Select-String -Path $f.FullName -Pattern '^\s*@extends|^\s*@section|^\s*@endsection|^\s*@push|^\s*@endpush'
        if ($bad) { throw "❌ Directivas Blade prohibidas en partial: $($f.FullName):$($bad.LineNumber)" }
    }
}

# C2. Validar layout principal
$layout = "resources/views/layouts/app.blade.php"
if (-not (Test-Path $layout)) { throw "❌ Layout principal no existe: $layout" }
if (-not (Select-String -Path $layout -Pattern "@yield\('content'\)")) { 
    throw "❌ Layout falta @yield('content')" 
}

# C3. Validar home template
$homeFile = "resources/views/home.blade.php"
if (Test-Path $homeFile) {
    if (-not (Select-String -Path $homeFile -Pattern "@extends\('layouts\.app'\)")) { 
        throw "❌ home.blade: falta @extends('layouts.app')" 
    }
    if (-not (Select-String -Path $homeFile -Pattern "@section\('content'\)")) { 
        throw "❌ home.blade: falta @section('content')" 
    }
    if (-not (Select-String -Path $homeFile -Pattern "@endsection")) { 
        throw "❌ home.blade: falta @endsection" 
    }
}

# D. CSS/Tailwind/Vite
Write-Host "D) Vite/Assets..."

# D1. Verificar vite.config.js existe y es válido
if (-not (Test-Path "vite.config.js")) { throw "❌ vite.config.js no existe" }
if (-not (Test-Path "package.json")) { throw "❌ package.json no existe" }

# D2. Verificar node_modules
npm -v | Out-Null
if (-not (Test-Path "node_modules")) { 
    Write-Host "D2) Instalando dependencias..."
    npm install | Out-Null 
}

# D3. Verificar que @vite en layout es correcto
if (-not (Select-String -Path $layout -Pattern "resources/css/app\.css")) { 
    throw "❌ Layout: falta resources/css/app.css en @vite" 
}
if (-not (Select-String -Path $layout -Pattern "resources/js/app\.js")) { 
    throw "❌ Layout: falta resources/js/app.js en @vite" 
}

# D4. Build de prueba (solo verificar que no falle)
Write-Host "D4) Testing Vite build..."
$buildResult = npm run build 2>&1
if ($LASTEXITCODE -ne 0) { throw "❌ npm run build falló: $buildResult" }

# E. UI (smoke)
Write-Host "E) UI (smoke)..."

# E1. Detectar puerto correcto del APP_URL
$port = if ($appUrl -match ":(\d+)") { $matches[1] } else { "8000" }

# E2. Verificar healthcheck
$healthUrl = "http://127.0.0.1:$port/healthz"
try {
    $response = Invoke-WebRequest $healthUrl -UseBasicParsing -TimeoutSec 5
    if ($response.Content -ne "ok") { throw "Healthcheck falló: $($response.Content)" }
} catch {
    throw "❌ Servidor no responde en $healthUrl : $($_.Exception.Message)"
}

# E3. Verificar home page carga sin errores
try {
    $homeResponse = Invoke-WebRequest "http://127.0.0.1:$port/" -UseBasicParsing -TimeoutSec 10
    if ($homeResponse.StatusCode -ne 200) { throw "Home page error: HTTP $($homeResponse.StatusCode)" }
} catch {
    throw "❌ Home page falló: $($_.Exception.Message)"
}

Write-Host "✅ QA ANTI-VITE COMPLETO: TODO OK" -ForegroundColor Green
Write-Host "   - @vite calls validadas ✓" -ForegroundColor DarkGreen
Write-Host "   - Archivos de assets existen ✓" -ForegroundColor DarkGreen  
Write-Host "   - Vistas compiladas limpiadas ✓" -ForegroundColor DarkGreen
Write-Host "   - Build Vite exitoso ✓" -ForegroundColor DarkGreen
Write-Host "   - Servidor responde OK ✓" -ForegroundColor DarkGreen