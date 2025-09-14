# 🚀 Safe Commit Script con QA Automático
# Uso: .\tools\safe-commit.ps1 -Message "feat: nueva funcionalidad"

param(
    [Parameter(Mandatory=$true)]
    [string]$Message,
    
    [Parameter()]
    [switch]$Force,
    
    [Parameter()]
    [ValidateSet("quick", "full", "deploy")]
    [string]$QAMode = "quick"
)

$ErrorActionPreference = "Stop"

Write-Host "🚀 PANORAMA IAS - Safe Commit Workflow" -ForegroundColor Green
Write-Host "Message: $Message" -ForegroundColor Gray
Write-Host "QA Mode: $QAMode" -ForegroundColor Gray

# Stage all changes if not already staged
$stagedFiles = git diff --cached --name-only
if (-not $stagedFiles) {
    Write-Host "▶ Staging all changes..." -ForegroundColor Blue
    git add .
}

# Run QA validation
Write-Host "▶ Running QA validation..." -ForegroundColor Blue
try {
    $qaOutput = & "./tools/continue-qa.ps1" -Mode $QAMode -JsonOutput 2>&1
    $qaExitCode = $LASTEXITCODE
} catch {
    Write-Host "❌ QA Script execution failed:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Yellow
    
    if ($Force) {
        Write-Host "⚠️  FORCE FLAG enabled, proceeding with commit anyway..." -ForegroundColor Yellow
    } else {
        Write-Host "💡 Use -Force to bypass QA or fix the errors above" -ForegroundColor Cyan
        exit 1
    }
}

if ($qaExitCode -ne 0 -and -not $Force) {
    Write-Host "❌ QA validation failed" -ForegroundColor Red
    Write-Host $qaOutput -ForegroundColor Yellow
    Write-Host "💡 Use -Force to bypass QA or fix the errors above" -ForegroundColor Cyan
    exit 1
}

# Parse QA results
$hasErrors = $false
if ($qaOutput) {
    try {
        $jsonStart = $qaOutput.IndexOf('{')
        if ($jsonStart -ge 0) {
            $jsonStr = $qaOutput.Substring($jsonStart)
            $qaResult = ConvertFrom-Json $jsonStr
            
            $criticalErrors = @($qaResult.errors | Where-Object { $_.severity -eq 'critical' })
            $warnings = @($qaResult.errors | Where-Object { $_.severity -eq 'warning' })
            
            if ($criticalErrors.Count -gt 0) {
                $hasErrors = $true
                Write-Host ""
                Write-Host "❌ CRITICAL ERRORS FOUND: $($criticalErrors.Count)" -ForegroundColor Red
                Write-Host ""
                
                foreach ($error in $criticalErrors) {
                    Write-Host "🚫 [$($error.severity.ToUpper())] $($error.type)" -ForegroundColor Red
                    Write-Host "   📁 File: $($error.file):$($error.line)" -ForegroundColor Gray
                    Write-Host "   ⚠️  Issue: $($error.message)" -ForegroundColor Yellow
                    Write-Host "   💡 Fix: $($error.suggestion)" -ForegroundColor Cyan
                    Write-Host ""
                }
                
                if (-not $Force) {
                    Write-Host "🔧 TO PROCEED:" -ForegroundColor Cyan
                    Write-Host "   1. Fix critical errors shown above" -ForegroundColor White
                    Write-Host "   2. Re-run: .\tools\safe-commit.ps1 -Message '$Message'" -ForegroundColor White
                    Write-Host "   3. Or use Continue AI to auto-fix: select error file → 'Continue QA Integration'" -ForegroundColor White
                    Write-Host ""
                    Write-Host "🚨 EMERGENCY BYPASS:" -ForegroundColor Yellow
                    Write-Host "   .\tools\safe-commit.ps1 -Message '$Message' -Force" -ForegroundColor White
                    exit 1
                }
            }
            
            if ($warnings.Count -gt 0) {
                Write-Host "⚠️  $($warnings.Count) warning(s) found:" -ForegroundColor Yellow
                foreach ($warning in $warnings[0..2]) {  # Show first 3 warnings
                    Write-Host "   • $($warning.message) in $($warning.file)" -ForegroundColor Gray
                }
                if ($warnings.Count -gt 3) {
                    Write-Host "   • ... and $($warnings.Count - 3) more warnings" -ForegroundColor Gray
                }
                Write-Host "   Run: .\tools\continue-qa.ps1 -Mode $QAMode for full details" -ForegroundColor Gray
            }
            
            Write-Host "✅ QA Summary: $($qaResult.summary.filesChecked) files checked, $($qaResult.summary.errorsFound) errors, $($qaResult.summary.warningsFound) warnings" -ForegroundColor Green
        }
    } catch {
        Write-Host "⚠️  Could not parse QA output, proceeding with commit" -ForegroundColor Yellow
    }
}

# Proceed with commit
if ($hasErrors -and -not $Force) {
    Write-Host "❌ Cannot commit with critical errors. Use -Force to bypass." -ForegroundColor Red
    exit 1
}

Write-Host "▶ Committing changes..." -ForegroundColor Blue
try {
    git commit -m "$Message"
    Write-Host ""
    Write-Host "🎉 COMMIT SUCCESSFUL!" -ForegroundColor Green
    Write-Host "Message: $Message" -ForegroundColor Gray
    
    # Show commit hash
    $commitHash = git rev-parse --short HEAD
    Write-Host "Hash: $commitHash" -ForegroundColor Gray
    
    if ($hasErrors -and $Force) {
        Write-Host ""
        Write-Host "⚠️  WARNING: Committed with -Force flag despite critical errors" -ForegroundColor Yellow
        Write-Host "   Consider fixing the issues in the next commit" -ForegroundColor Gray
    }
    
} catch {
    Write-Host "❌ Git commit failed:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "✨ Ready for next development iteration!" -ForegroundColor Cyan