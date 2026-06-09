# Install Docker on Windows from the command line (PowerShell).
#
# Docker on Windows needs two things:
#   1. WSL2  - a lightweight Linux engine built into Windows (runs the containers)
#   2. Docker Desktop - provides the `docker` / `docker compose` commands + the engine
#
# This installs both using winget (the package manager built into Windows 10/11).
#
# HOW TO RUN:
#   1. Open PowerShell as Administrator (right-click PowerShell -> Run as administrator)
#   2. Allow this script for the session:
#         Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
#   3. Run it:
#         .\install-docker-windows.ps1
#
$ErrorActionPreference = "Stop"
Write-Host "==> Career Mind: Docker installer for Windows" -ForegroundColor Cyan

# --- 0. winget must be present (ships with Windows 10/11; via 'App Installer') ---
if (-not (Get-Command winget -ErrorAction SilentlyContinue)) {
    throw "winget not found. Install 'App Installer' from the Microsoft Store, then re-run."
}

# --- 1. Enable WSL2 (the Linux engine Docker uses) ----------------------------
Write-Host "==> Installing/Enabling WSL2..." -ForegroundColor Cyan
# Installs WSL + the default Ubuntu distro. Safe to run if already installed.
wsl --install
# If WSL was just enabled for the first time, Windows will ask for a REBOOT.
# After rebooting, re-run this script to finish the Docker Desktop install.

# --- 2. Install Docker Desktop via winget ------------------------------------
Write-Host "==> Installing Docker Desktop..." -ForegroundColor Cyan
winget install -e --id Docker.DockerDesktop `
    --accept-source-agreements --accept-package-agreements

Write-Host ""
Write-Host "============================================================" -ForegroundColor Green
Write-Host " Docker is installed." -ForegroundColor Green
Write-Host ""
Write-Host " NEXT STEPS:"
Write-Host "   1. If Windows asked to reboot for WSL2, reboot now."
Write-Host "   2. Launch 'Docker Desktop' from the Start menu and wait until"
Write-Host "      the whale icon says 'Docker Desktop is running'."
Write-Host "   3. Open a NEW PowerShell window and verify:"
Write-Host "         docker --version"
Write-Host "         docker compose version"
Write-Host "   4. Run the project:"
Write-Host "         cd career-mind"
Write-Host "         docker compose up --build"
Write-Host "      Then open http://localhost:8080"
Write-Host "============================================================" -ForegroundColor Green
