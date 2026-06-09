# Career Mind - Student Setup Guide

> **Cloning this on a fresh machine?** Follow **[SETUP.md](SETUP.md)** — a complete
> zero-to-running guide (installs, database import with all seeded data, run commands).
> The notes below are the original Windows-focused quick reference.

This project has two services:

1. PHP Web App (`career-mind-web`)
2. Flask AI Service (`career-mind-ai`)

Both services must run at the same time.

## 1. Prerequisites (Windows)

Install the following tools:

1. Python 3.11 or 3.12
2. PHP 8.1+
3. MySQL 8+
4. Git (optional)

## 2. Project Structure

Keep this folder structure:

- `career-mind/`
	- `career-mind-ai/`
	- `career-mind-web/`
	- `database/`
	- `docs/`

## 3. Database Setup

1. Open MySQL and create database:

```sql
CREATE DATABASE career_mind;
```

2. Import schema from:

- `database/schema.sql`

## 4. Configure PHP App

Open:

- `career-mind-web/config/config.php`

Set your DB credentials in the `db` section:

- host
- port
- name
- user
- pass

Keep AI URL as:

- `http://localhost:5001`

## 5. Create Python Virtual Environment

From project root (`career-mind`):

```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
```

Install AI dependencies:

```powershell
cd .\career-mind-ai
pip install -r requirements.txt
```

## 6. Train Model (if needed)

If model file is missing in `career-mind-ai/models`, run:

```powershell
python train.py
```

## 7. Run the Project (2 Terminals)

### Terminal 1 - AI Service

```powershell
cd C:\path\to\career-mind\career-mind-ai
C:\path\to\career-mind\.venv\Scripts\python.exe app.py
```

### Terminal 2 - PHP Web App

```powershell
cd C:\path\to\career-mind\career-mind-web
php -S 127.0.0.1:8000 -t public
```

## 8. Open in Browser

- Web App: `http://127.0.0.1:8000`
- AI Health Check: `http://127.0.0.1:5001/health`

If health returns status `ok`, AI service is running correctly.

## 9. Stop Servers

Press `Ctrl + C` in each terminal.

## 10. Quick Troubleshooting

1. Port already in use:

```powershell
php -S 127.0.0.1:8001 -t public
```

2. AI not reachable from web app:

- Check `career-mind-web/config/config.php`
- Ensure `ai.base_url` is `http://localhost:5001`

3. DB connection error:

- Recheck MySQL username/password and database name (`career_mind`)

4. Python module missing:

```powershell
pip install -r career-mind-ai/requirements.txt
```

For normal use, always start both services (AI + PHP) before opening the web app.
# career-mind
