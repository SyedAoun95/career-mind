#!/usr/bin/env bash
# Install the Docker CLI on macOS WITHOUT Docker Desktop (GUI-free, fully free).
#
# It installs, via Homebrew:
#   - docker          : the `docker` command-line tool
#   - docker-compose   : the `docker compose` subcommand
#   - colima           : a lightweight Linux VM that actually runs the containers
#                        (Docker needs a Linux engine; on Mac, Colima provides it)
#
# Usage:
#   chmod +x install-docker-mac.sh
#   ./install-docker-mac.sh
#
set -euo pipefail

echo "==> Career Mind: Docker CLI installer for macOS"

# --- 1. Ensure Homebrew is installed -----------------------------------------
if ! command -v brew >/dev/null 2>&1; then
  echo "==> Homebrew not found. Installing it..."
  /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
fi

# Make sure brew is on PATH for this session (Apple Silicon vs Intel locations).
if [ -x /opt/homebrew/bin/brew ]; then
  eval "$(/opt/homebrew/bin/brew shellenv)"
elif [ -x /usr/local/bin/brew ]; then
  eval "$(/usr/local/bin/brew shellenv)"
fi
echo "==> Homebrew: $(brew --version | head -1)"

# --- 2. Install docker CLI, compose, and colima ------------------------------
echo "==> Installing docker, docker-compose, colima..."
brew install docker docker-compose colima

# --- 3. Wire up `docker compose` (v2 subcommand) -----------------------------
# Homebrew installs the compose binary; link it as a Docker CLI plugin so that
# `docker compose ...` works (not just the older `docker-compose ...`).
mkdir -p "$HOME/.docker/cli-plugins"
ln -sfn "$(brew --prefix)/opt/docker-compose/bin/docker-compose" \
        "$HOME/.docker/cli-plugins/docker-compose"

# --- 4. Start the Colima VM (the container engine) ---------------------------
echo "==> Starting Colima (this creates a small Linux VM the first time)..."
if colima status >/dev/null 2>&1; then
  echo "==> Colima already running."
else
  colima start
fi

# --- 5. Verify ---------------------------------------------------------------
echo
echo "==> Versions:"
docker --version
docker compose version
echo
echo "==> Smoke test (runs a tiny container):"
docker run --rm hello-world | head -3

echo
echo "============================================================"
echo " Docker CLI is ready."
echo
echo " Run the project now with:"
echo "     cd \"$(pwd)\""
echo "     docker compose up --build"
echo " Then open http://localhost:8080"
echo
echo " Notes:"
echo "   - Start the engine after a reboot with:  colima start"
echo "   - Stop the engine with:                  colima stop"
echo "============================================================"
