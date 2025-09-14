$files = @(
  "resources\views\home.blade.php",
  "resources\views\blog\show.blade.php"
)
$files | % { if (Test-Path $_) { attrib +R $_ } }

$fw = New-Object IO.FileSystemWatcher "resources\views","*.blade.php"
$fw.IncludeSubdirectories = $true
$fw.EnableRaisingEvents = $true

Register-ObjectEvent $fw Changed -Action {
  foreach ($f in $files) { if (Test-Path $f) { attrib +R $f } }
} | Out-Null

Write-Host "🔒 Lock activo. Ctrl+C para salir."
Wait-Event