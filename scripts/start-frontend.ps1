# Open a new PowerShell window and run the Vite dev server for the frontend
$frontendPath = "$(Split-Path -Parent $MyInvocation.MyCommand.Definition)\..\frontend"
Start-Process -FilePath pwsh -ArgumentList "-NoExit","-Command","cd '$frontendPath'; npm run dev -- --host 127.0.0.1 --port 5174" -WorkingDirectory $frontendPath
Write-Output "Started frontend dev server in a new PowerShell window on http://127.0.0.1:5174/"
