# 🔧 Continue QA Integration Script
# Compatible con VS Code Continue extension
# Uso: .\tools\continue-qa.ps1 -Mode [quick|full|deploy]

param(
    [Parameter()]
    [ValidateSet("quick", "full", "deploy")]
    [string]$Mode = "quick",
    
    [Parameter()]
    [string]$ContinueContext = "",
    
    [Parameter()]
    [switch]$JsonOutput
)

# Continue-friendly error reporting
class ContinueError {
    [string]$Type
    [string]$File
    [int]$Line
    [string]$Message
    [string]$Suggestion
    [string]$Severity  # critical, warning, info
    
    ContinueError([string]$type, [string]$file, [int]$line, [string]$message, [string]$suggestion, [string]$severity) {
        $this.Type = $type
        $this.File = $file
        $this.Line = $line
        $this.Message = $message
        $this.Suggestion = $suggestion
        $this.Severity = $severity
    }
    
    [hashtable] ToJson() {
        return @{
            type = $this.Type
            file = $this.File
            line = $this.Line
            message = $this.Message
            suggestion = $this.Suggestion
            severity = $this.Severity
        }
    }
}

$global:ContinueErrors = @()
$global:ContinueStats = @{
    filesChecked = 0
    errorsFound = 0
    warningsFound = 0
    startTime = Get-Date
}

function Add-ContinueError {
    param(
        [string]$Type,
        [string]$File,
        [int]$Line = 0,
        [string]$Message,
        [string]$Suggestion = "",
        [string]$Severity = "warning"
    )
    
    $error = [ContinueError]::new($Type, $File, $Line, $Message, $Suggestion, $Severity)
    $global:ContinueErrors += $error
    
    if ($Severity -eq "critical") {
        $global:ContinueStats.errorsFound++
    } else {
        $global:ContinueStats.warningsFound++
    }
    
    if (-not $JsonOutput) {
        $color = switch ($Severity) {
            "critical" { "Red" }
            "warning" { "Yellow" }
            "info" { "Cyan" }
        }
        Write-Host "[$Severity] $Type in $File$(if($Line -gt 0){":$Line"}): $Message" -ForegroundColor $color
        if ($Suggestion) {
            Write-Host "  💡 Suggestion: $Suggestion" -ForegroundColor Gray
        }
    }
}

function Test-BladeIntegrity {
    Write-Host "`n🔍 Checking Blade template integrity..." -ForegroundColor Cyan
    
    # Check for orphan @vite directives
    $bladeFiles = Get-ChildItem "resources/views" -Recurse -Filter "*.blade.php"
    foreach ($file in $bladeFiles) {
        $global:ContinueStats.filesChecked++
        $content = Get-Content $file.FullName -Raw
        $lines = Get-Content $file.FullName
        
        # Check for problematic @vite patterns
        for ($i = 0; $i -lt $lines.Count; $i++) {
            $lineNum = $i + 1
            $line = $lines[$i]
            
            # Orphan @vite directives
            if ($line -match "^\s*@vite\s*$") {
                Add-ContinueError -Type "ViteDirective" -File $file.FullName -Line $lineNum -Message "Empty @vite directive" -Suggestion "Use @vite([`'resources/css/app.css`', `'resources/js/app.js`'])" -Severity "critical"
            }
            
            # @vite with empty array
            if ($line -match "@vite\(\s*\[\s*\]\s*\)") {
                Add-ContinueError -Type "ViteDirective" -File $file.FullName -Line $lineNum -Message "@vite with empty array" -Suggestion "Add valid asset paths to @vite array" -Severity "critical"
            }
            
            # Unescaped @vite in URLs (like @vite/client)
            if ($line -match "@vite/client" -and $line -notmatch "@@vite/client") {
                Add-ContinueError -Type "BladeDirectiveCollision" -File $file.FullName -Line $lineNum -Message "Unescaped @vite in URL causing Blade directive collision" -Suggestion "Change `'@vite/client`' to `'@@vite/client`'" -Severity "critical"
            }
            
            # Missing alt attributes in images
            if ($line -match "<img\s+[^>]*src=" -and $line -notmatch "alt=") {
                Add-ContinueError -Type "Accessibility" -File $file.FullName -Line $lineNum -Message "Image missing alt attribute" -Suggestion "Add alt=`"`"{{`$post->title}}`"`" for accessibility" -Severity "warning"
            }
            
            # Unsafe direct echo without escaping
            if ($line -match "\{\{\{\s*\$[^}]+\}\}\}") {
                Add-ContinueError -Type "Security" -File $file.FullName -Line $lineNum -Message "Unescaped output detected" -Suggestion "Use {{}} instead of {{{}} unless HTML output is intentional" -Severity "warning"
            }
        }
    }
}

function Test-ViteConfiguration {
    Write-Host "`n🔍 Checking Vite configuration..." -ForegroundColor Cyan
    
    # Check vite.config.js exists and has required inputs
    if (Test-Path "vite.config.js") {
        $viteConfig = Get-Content "vite.config.js" -Raw
        $global:ContinueStats.filesChecked++
        
        if ($viteConfig -notmatch "resources/css/app\.css") {
            Add-ContinueError -Type "ViteConfig" -File "vite.config.js" -Message "Missing app.css in Vite inputs" -Suggestion "Add `'resources/css/app.css`' to input array" -Severity "critical"
        }
        
        if ($viteConfig -notmatch "resources/js/app\.js") {
            Add-ContinueError -Type "ViteConfig" -File "vite.config.js" -Message "Missing app.js in Vite inputs" -Suggestion "Add `'resources/js/app.js`' to input array" -Severity "critical"
        }
    } else {
        Add-ContinueError -Type "ViteConfig" -File "vite.config.js" -Message "vite.config.js not found" -Suggestion "Create vite.config.js with laravel plugin configuration" -Severity "critical"
    }
    
    # Check if manifest exists after build
    if (Test-Path "public/build") {
        if (-not (Test-Path "public/build/manifest.json") -and -not (Test-Path "public/build/.vite/manifest.json")) {
            Add-ContinueError -Type "ViteBuild" -File "public/build" -Message "Vite manifest not found" -Suggestion "Run 'npm run build' and copy manifest.json to public/build/" -Severity "critical"
        }
    }
}

function Test-LaravelHealth {
    Write-Host "`n🔍 Testing Laravel application health..." -ForegroundColor Cyan
    
    # Check if server is running
    try {
        $healthResponse = Invoke-RestMethod "http://127.0.0.1:9999/healthz" -TimeoutSec 5
        if ($healthResponse -eq "ok") {
            Write-Host "✅ Laravel health check: OK" -ForegroundColor Green
        }
    } catch {
        Add-ContinueError -Type "LaravelHealth" -File "routes/web.php" -Message "Health check failed: $($_.Exception.Message)" -Suggestion "Start server with `'php -S 127.0.0.1:9999 -t public`' or `'php artisan serve --host=127.0.0.1 --port=9999`'" -Severity "critical"
        return
    }
    
    # Test homepage rendering
    try {
        $homeResponse = Invoke-WebRequest "http://127.0.0.1:9999/" -UseBasicParsing -TimeoutSec 10
        if ($homeResponse.StatusCode -eq 200) {
            $content = $homeResponse.Content
            
            # Check for required CSS classes
            if ($content -notmatch "news-scope") {
                Add-ContinueError -Type "Template" -File "resources/views/home.blade.php" -Message "Missing .news-scope wrapper" -Suggestion "Add section with class=`"news-scope`" wrapper in home template" -Severity "warning"
            }
            
            if ($content -notmatch "card-image") {
                Add-ContinueError -Type "Template" -File "resources/views/partials/news-card.blade.php" -Message "Missing .card-image classes" -Suggestion "Ensure news cards have div with class=`"card-image`" elements" -Severity "warning"
            }
            
            # Check for error indicators in response
            if ($content -match "Server Error|Exception|Fatal error") {
                Add-ContinueError -Type "LaravelError" -File "Unknown" -Message "Homepage contains error indicators" -Suggestion "Check Laravel logs for detailed error information" -Severity "critical"
            }
        }
    } catch {
        Add-ContinueError -Type "LaravelHealth" -File "routes/web.php" -Message "Homepage request failed: $($_.Exception.Message)" -Suggestion "Check routes and ensure server is running properly" -Severity "critical"
    }
}

function Test-SecurityBaseline {
    Write-Host "`n🔍 Running security baseline checks..." -ForegroundColor Cyan
    
    # Check .env security
    if (Test-Path ".env") {
        $envContent = Get-Content ".env" -Raw
        $global:ContinueStats.filesChecked++
        
        if ($envContent -match "APP_DEBUG=true" -and $Mode -eq "deploy") {
            Add-ContinueError -Type "Security" -File ".env" -Message "Debug mode enabled in production-ready build" -Suggestion "Set APP_DEBUG=false before deployment" -Severity "critical"
        }
        
        if ($envContent -match "APP_KEY=\s*$") {
            Add-ContinueError -Type "Security" -File ".env" -Message "Missing application key" -Suggestion "Run `'php artisan key:generate`'" -Severity "critical"
        }
    }
    
    # Check for common vulnerabilities in controllers
    $controllerFiles = Get-ChildItem "app/Http/Controllers" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
    foreach ($controller in $controllerFiles) {
        $global:ContinueStats.filesChecked++
        $content = Get-Content $controller.FullName -Raw
        
        # Check for SQL injection risks
        if ($content -match "DB::raw\(" -and $content -notmatch "DB::raw\(['\""]") {
            Add-ContinueError -Type "Security" -File $controller.FullName -Message "Potential SQL injection in DB::raw()" -Suggestion "Use parameterized queries instead of string concatenation" -Severity "critical"
        }
        
        # Check for XSS risks in direct output
        if ($content -match "echo\s+\$" -or $content -match "print\s+\$") {
            Add-ContinueError -Type "Security" -File $controller.FullName -Message "Direct variable output without escaping" -Suggestion "Use Blade templates with {{}} escaping instead" -Severity "warning"
        }
    }
}

function Get-ContinueReport {
    $global:ContinueStats.endTime = Get-Date
    $duration = $global:ContinueStats.endTime - $global:ContinueStats.startTime
    
    $report = @{
        summary = @{
            mode = $Mode
            duration = $duration.TotalSeconds
            filesChecked = $global:ContinueStats.filesChecked
            errorsFound = $global:ContinueStats.errorsFound
            warningsFound = $global:ContinueStats.warningsFound
        }
        errors = @($global:ContinueErrors | ForEach-Object { $_.ToJson() })
    }
    
    if ($JsonOutput) {
        return $report | ConvertTo-Json -Depth 10
    } else {
        Write-Host "`n📊 QA Report Summary" -ForegroundColor Cyan
        Write-Host "Mode: $Mode" -ForegroundColor Gray
        Write-Host "Duration: $([math]::Round($duration.TotalSeconds, 2))s" -ForegroundColor Gray
        Write-Host "Files Checked: $($global:ContinueStats.filesChecked)" -ForegroundColor Gray
        Write-Host "Errors: $($global:ContinueStats.errorsFound)" -ForegroundColor $(if($global:ContinueStats.errorsFound -gt 0){"Red"}else{"Green"})
        Write-Host "Warnings: $($global:ContinueStats.warningsFound)" -ForegroundColor $(if($global:ContinueStats.warningsFound -gt 0){"Yellow"}else{"Green"})
        
        if ($global:ContinueStats.errorsFound -eq 0 -and $global:ContinueStats.warningsFound -eq 0) {
            Write-Host "`n🎉 All checks passed! Ready for development/deployment." -ForegroundColor Green
        } elseif ($global:ContinueStats.errorsFound -eq 0) {
            Write-Host "`n⚠️  No critical errors, but some warnings need attention." -ForegroundColor Yellow
        } else {
            Write-Host "`n❌ Critical errors found! Please fix before proceeding." -ForegroundColor Red
        }
    }
}

# Main execution
Write-Host "🔧 PANORAMA IAS - Continue QA Integration" -ForegroundColor Green
Write-Host "Mode: $Mode | Context: $ContinueContext" -ForegroundColor Gray

switch ($Mode) {
    "quick" {
        Test-BladeIntegrity
        Test-ViteConfiguration
    }
    "full" {
        Test-BladeIntegrity
        Test-ViteConfiguration
        Test-LaravelHealth
    }
    "deploy" {
        Test-BladeIntegrity
        Test-ViteConfiguration  
        Test-LaravelHealth
        Test-SecurityBaseline
    }
}

Get-ContinueReport