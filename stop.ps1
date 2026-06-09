# Career Mind — stop the AI service and PHP web app on Windows. Mirrors stop.sh.
foreach ($port in 5001, 8000) {
    $conns = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue
    if ($conns) {
        foreach ($c in $conns) {
            Stop-Process -Id $c.OwningProcess -Force -ErrorAction SilentlyContinue
            Write-Host "Stopped service on port $port (PID $($c.OwningProcess))"
        }
    } else {
        Write-Host "Nothing running on port $port"
    }
}
Write-Host "Done. (MySQL service left running — stop it with: Stop-Service MySQL80)"
