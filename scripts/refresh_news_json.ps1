$list = Invoke-RestMethod 'http://127.0.0.1:8095/api/mobility/news' -ErrorAction Stop
foreach($item in $list.data){
  $id = $item.id
  $out = "c:\laragon\www\INICIO\crm-vite-react\news_$id.json"
  try{
    $detail = Invoke-RestMethod "http://127.0.0.1:8095/api/news/$id" -ErrorAction Stop
    $detail | ConvertTo-Json -Depth 8 | Out-File $out -Encoding utf8
    Write-Output "Saved $out"
  } catch {
    $err = $_.Exception.Message
    Write-Error ("Failed {0}: {1}" -f $id, $err)
  }
}
