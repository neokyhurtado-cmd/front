$files = Get-ChildItem -Recurse resources/views -Filter *.blade.php
$bad = @()
$partialsBad = @()

foreach ($f in $files) {
  $text = Get-Content -Raw $f.FullName
  $sec = ([regex]::Matches($text, '@section\(')).Count
  $end = ([regex]::Matches($text, '@endsection')).Count
  if ($f.FullName -match '\\partials\\' -and ($text -match '@extends|@section|@endsection')) {
    $partialsBad += $f.FullName
  }
  if ($sec -ne $end) {
    $bad += [pscustomobject]@{ File=$f.FullName; Sections=$sec; Endsections=$end }
  }
}

if ($partialsBad.Count -gt 0) {
  Write-Host "❌ Directivas Blade en partials:" -ForegroundColor Red
  $partialsBad | ForEach-Object { Write-Host " - $_" }
}

if ($bad.Count -gt 0) {
  Write-Host "❌ Desbalances Blade:" -ForegroundColor Red
  $bad | Format-Table -AutoSize
  exit 1
}

Write-Host "✅ Blade OK" -ForegroundColor Green