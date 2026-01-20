# Start both frontend and backend servers on Windows

$PROJECT_ROOT = Split-Path -Parent $MyInvocation.MyCommand.Path

Write-Host "Starting jquerynativePHPapi servers..."
Write-Host "Project root: $PROJECT_ROOT"
Write-Host ""

# Start backend server
Write-Host "Starting backend on localhost:3001..."
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PROJECT_ROOT\backend'; php -S localhost:3001 -t public" -WindowStyle Normal

Start-Sleep -Seconds 1

# Start frontend server
Write-Host "Starting frontend on localhost:3000..."
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PROJECT_ROOT\frontend'; php -S localhost:3000" -WindowStyle Normal

Write-Host ""
Write-Host "✓ Backend running on http://localhost:3001"
Write-Host "✓ Frontend running on http://localhost:3000"
Write-Host ""
Write-Host "Close the command windows to stop the servers"
Write-Host ""
