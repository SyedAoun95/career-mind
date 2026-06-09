# Career Mind — AI service (Flask + scikit-learn)
FROM python:3.11-slim

# libgomp1: required at runtime by scikit-learn (OpenMP). curl: used by the healthcheck.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libgomp1 curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Install dependencies first so this layer is cached unless requirements change.
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the rest of the service (model, data, code).
COPY . .

# Bind to all interfaces inside the container so the web service can reach it.
ENV FLASK_RUN_HOST=0.0.0.0 \
    FLASK_RUN_PORT=5001

EXPOSE 5001

CMD ["python", "app.py"]
