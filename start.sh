#!/usr/bin/env bash
# Career Mind launcher — starts the Flask AI service and the PHP web app.
# Both run detached (survive closing the terminal). Logs go to ./logs/.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PY="$ROOT/.venv/bin/python"
LOGDIR="$ROOT/logs"
mkdir -p "$LOGDIR"

# --- resolve binaries from PATH (works on Intel + Apple Silicon Homebrew) ---
PHP_BIN="$(command -v php || echo /usr/local/bin/php)"
MYSQLADMIN_BIN="$(command -v mysqladmin || echo /usr/local/bin/mysqladmin)"
BREW_BIN="$(command -v brew || true)"

# --- make sure MySQL is running ---
if ! "$MYSQLADMIN_BIN" -u root ping >/dev/null 2>&1; then
  echo "Starting MySQL..."
  [ -n "$BREW_BIN" ] && "$BREW_BIN" services start mysql >/dev/null 2>&1 || true
  until "$MYSQLADMIN_BIN" -u root ping >/dev/null 2>&1; do sleep 1; done
fi
echo "MySQL: up"

# --- free the ports if something is already listening ---
for PORT in 5001 8000; do
  PIDS="$(lsof -nP -iTCP:$PORT -sTCP:LISTEN -t 2>/dev/null || true)"
  if [ -n "$PIDS" ]; then
    echo "Port $PORT busy — stopping old process ($PIDS)"
    kill $PIDS 2>/dev/null || true
    sleep 1
  fi
done

# --- start Flask AI service (:5001) ---
cd "$ROOT/career-mind-ai"
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=career_mind DB_USER=root DB_PASS="" \
  nohup "$PY" app.py >"$LOGDIR/ai.log" 2>&1 &
echo "AI service starting (PID $!) — log: logs/ai.log"

# --- start PHP web app (:8000) ---
cd "$ROOT/career-mind-web"
nohup "$PHP_BIN" -S 127.0.0.1:8000 -t public >"$LOGDIR/web.log" 2>&1 &
echo "Web app starting (PID $!) — log: logs/web.log"

# --- wait until both answer ---
echo -n "Waiting for services"
until curl -s -o /dev/null http://127.0.0.1:5001/health 2>/dev/null \
   && curl -s -o /dev/null http://127.0.0.1:8000/ 2>/dev/null; do
  echo -n "."; sleep 1
done
echo

echo
echo "✅ Career Mind is running:"
echo "   Web app   : http://127.0.0.1:8000"
echo "   AI health : http://127.0.0.1:5001/health"
echo
echo "Stop everything with: ./stop.sh"
