#!/usr/bin/env bash
# Career Mind — stop the Flask AI service and PHP web app.
for PORT in 5001 8000; do
  PIDS="$(lsof -nP -iTCP:$PORT -sTCP:LISTEN -t 2>/dev/null || true)"
  if [ -n "$PIDS" ]; then
    kill $PIDS 2>/dev/null || true
    echo "Stopped service on port $PORT (PID $PIDS)"
  else
    echo "Nothing running on port $PORT"
  fi
done
echo "Done. (MySQL left running — stop it with: brew services stop mysql)"
