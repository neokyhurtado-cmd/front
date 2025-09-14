# Watcher para detectar problemas en vistas en tiempo real
param(
    [string]$Path = "resources/views"
)

Write-Host "🔍 Iniciando watcher de vistas en: $Path" -ForegroundColor Green
Write-Host "💡 Presiona Ctrl+C para detener" -ForegroundColor Yellow

# Crear el FileSystemWatcher
$fsw = New-Object IO.FileSystemWatcher $Path -Property @{
    IncludeSubdirectories = $true
    EnableRaisingEvents = $true
    Filter = "*.blade.php"
}

# Función de validación
function Test-BladeFile {
    param([string]$FilePath)
    
    try {
        # Test 1: Compilación de vistas
        php artisan view:clear | Out-Null
        php artisan view:cache | Out-Null
        
        # Test 2: Si es partial, validar que no tenga directivas
        if ($FilePath -like "*partials*") {
            $content = Get-Content $FilePath -Raw -ErrorAction SilentlyContinue
            $badDirectives = @('@extends', '@section', '@endsection', '@push', '@endpush')
            
            foreach ($directive in $badDirectives) {
                if ($content -like "*$directive*") {
                    throw "❌ Directiva prohibida '$directive' en partial: $FilePath"
                }
            }
        }
        
        Write-Host "✅ [$((Get-Date).ToString('HH:mm:ss'))] Vistas OK - $FilePath" -ForegroundColor Green
        
    } catch {
        Write-Host "❌ [$((Get-Date).ToString('HH:mm:ss'))] ERROR: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "🔧 Archivo problemático: $FilePath" -ForegroundColor Yellow
    }
}

# Registrar eventos
Register-ObjectEvent $fsw Changed -Action {
    $path = $Event.SourceEventArgs.FullPath
    $name = $Event.SourceEventArgs.Name
    
    # Evitar múltiples eventos del mismo archivo
    Start-Sleep -Milliseconds 500
    
    Write-Host "📝 Cambio detectado: $name" -ForegroundColor Cyan
    Test-BladeFile -FilePath $path
}

# Validación inicial
Write-Host "🚀 Ejecutando validación inicial..."
Test-BladeFile -FilePath "resources/views/home.blade.php"

# Mantener el script corriendo
try {
    while ($true) {
        Start-Sleep -Seconds 1
    }
} finally {
    $fsw.Dispose()
    Write-Host "`n👋 Watcher detenido" -ForegroundColor Yellow
}