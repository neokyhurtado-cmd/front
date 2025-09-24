$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI5MzkxZmMwYi03Y2YxLTQzNzktOWFhOS0wMDhiODBkOTQ1NGQiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwiaWF0IjoxNzU4NDEzNjA0fQ.bai7y7zAB0eCX2PVF9vYIXZMNq7NEyoHcIoa9bFoSXc'
$payload = @{ 
    title = 'Prueba desde test link'
    slug = 'prueba-test-link'
    url = 'https://example.test/prueba-test'
    plain_text = 'contenido test link'
    lead = 'lead test'
} | ConvertTo-Json

$payload | Set-Content -Path .\tmp_n8n_payload.json -Encoding utf8

try {
    $resp = Invoke-RestMethod -Uri 'http://localhost:5678/webhook-test/n8n/upsert-news' -Method Post -Headers @{ 'X-Webhook-Token' = $token } -InFile .\tmp_n8n_payload.json -ContentType 'application/json' -ErrorAction Stop
    Write-Output "=== WEBHOOK RESPONSE ==="
    $resp | ConvertTo-Json -Depth 5 | Write-Output
} catch {
    Write-Output "=== WEBHOOK ERROR ==="
    Write-Output $_.Exception.Message
}

Write-Output "`n=== LATEST FILES IN storage/app/public/news ==="
if (Test-Path .\laravel-backend\storage\app\public\news) {
    Get-ChildItem -Path .\laravel-backend\storage\app\public\news -File -Name | Select-Object -Last 10 | ForEach-Object { Write-Output $_ }
} else {
    Write-Output 'news directory not found'
}

Write-Output "`n=== LARAVEL LOG (last 50 lines) ==="
if (Test-Path .\laravel-backend\storage\logs\laravel.log) {
    Get-Content .\laravel-backend\storage\logs\laravel.log -Tail 50 | ForEach-Object { Write-Output $_ }
} else {
    Write-Output 'no laravel.log'
}

Remove-Item .\tmp_n8n_payload.json -ErrorAction SilentlyContinue
