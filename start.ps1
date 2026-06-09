# Career Mind launcher for Windows (PowerShell) — mirrors start.sh.
# Starts the Flask AI service (:5001) and the PHP web app (:8000). Logs -> .\logs\
# Usage:  .\start.ps1      (run .\stop.ps1 to stop)
$ErrorActionPreference = "Stop"

$Root   = $PSScriptRoot
$LogDir = Join-Path $Root "logs"
New-Item -ItemType Directory -Force -Path $LogDir | Out-Null

# --- resolve binaries (compatible with Windows PowerShell 5.1, no ?. operator) ---
$phpCmd    = Get-Command php    -ErrorAction SilentlyContinue
$pythonCmd = Get-Command python -ErrorAction SilentlyContinue
if (-not $phpCmd)    { throw "php not found on PATH. Install PHP 8.2+ and reopen PowerShell." }
if (-not $pythonCmd) { throw "python not found on PATH. Install Python 3.11+ and reopen PowerShell." }
$php    = $phpCmd.Source
$python = $pythonCmd.Source

# --- check MySQL is reachable (best effort) ---
$mysqlCmd   = Get-Command mysqladmin -ErrorAction SilentlyContinue
$mysqladmin = if ($mysqlCmd) { $mysqlCmd.Source } else { $null }
if ($mysqladmin) {
    & $mysqladmin -u root ping 2>$null | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "MySQL not responding — trying to start the service..."
        Start-Service -Name "MySQL80","MySQL" -ErrorAction SilentlyContinue
    }
}

# --- free ports if something is already listening ---
foreach ($port in 5001, 8000) {
    $conns = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue
    foreach ($c in $conns) {
        Write-Host "Port $port busy — stopping old process (PID $($c.OwningProcess))"
        Stop-Process -Id $c.OwningProcess -Force -ErrorAction SilentlyContinue
    }
}

# --- start Flask AI service (:5001) ---
# Child processes inherit these env vars; adjust DB_PASS if your root has a password.
$env:DB_HOST = "127.0.0.1"; $env:DB_PORT = "3306"
$env:DB_NAME = "career_mind"; $env:DB_USER = "root"
if (-not $env:DB_PASS) { $env:DB_PASS = "" }

$ai = Start-Process -FilePath $python -ArgumentList "app.py" `
    -WorkingDirectory (Join-Path $Root "career-mind-ai") `
    -RedirectStandardOutput (Join-Path $LogDir "ai.log") `
    -RedirectStandardError  (Join-Path $LogDir "ai.err.log") `
    -PassThru -WindowStyle Hidden
Write-Host "AI service starting (PID $($ai.Id)) — log: logs\ai.log"

# --- start PHP web app (:8000) ---
$web = Start-Process -FilePath $php `
    -ArgumentList "-S","127.0.0.1:8000","-t","public" `
    -WorkingDirectory (Join-Path $Root "career-mind-web") `
    -RedirectStandardOutput (Join-Path $LogDir "web.log") `
    -RedirectStandardError  (Join-Path $LogDir "web.err.log") `
    -PassThru -WindowStyle Hidden
Write-Host "Web app starting (PID $($web.Id)) — log: logs\web.log"

# --- wait until both answer ---
Write-Host -NoNewline "Waiting for services"
do {
    Start-Sleep -Seconds 1
    Write-Host -NoNewline "."
    $aiOk  = try { (Invoke-WebRequest "http://127.0.0.1:5001/health" -UseBasicParsing -TimeoutSec 2).StatusCode -eq 200 } catch { $false }
    $webOk = try { (Invoke-WebRequest "http://127.0.0.1:8000/"       -UseBasicParsing -TimeoutSec 2).StatusCode -ge 200 } catch { $false }
} until ($aiOk -and $webOk)
Write-Host ""

Write-Host ""
Write-Host "Career Mind is running:" -ForegroundColor Green
Write-Host "   Web app   : http://localhost:8000"
Write-Host "   AI health : http://localhost:5001/health"
Write-Host ""
Write-Host "Stop everything with: .\stop.ps1"
