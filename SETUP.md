# Career Mind — Setup from Zero

A clone of this repo to a **running app**, assuming the machine has *nothing* installed.

Two services run together:
- **`career-mind-web`** — PHP web app → http://127.0.0.1:8000
- **`career-mind-ai`** — Python/Flask AI service → http://127.0.0.1:5001
- **MySQL** database `career_mind` (schema + all seeded data ships in `database/career_mind.sql`)

The trained model (`career-mind-ai/models/career_model.pkl`) and the seeded database
are committed, so you do **not** need to train anything or hand-enter data.

---

## 1. Install prerequisites

You need **PHP 8.1+**, **Python 3.11 or 3.12**, **MySQL 8+**, and **Git**.

### macOS (Homebrew)
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"  # if Homebrew missing
brew install php python@3.12 mysql git
brew services start mysql
```

### Ubuntu / Debian
```bash
sudo apt update
sudo apt install -y php php-cli php-mysql python3 python3-venv python3-pip mysql-server git
sudo service mysql start
```

### Windows
Install each from the official sites (or `winget`):
```powershell
winget install PHP.PHP Python.Python.3.12 Oracle.MySQL Git.Git
```
Make sure `php`, `python`, and `mysql` are on your PATH. Start the MySQL service from "Services".

Verify:
```bash
php -v        # 8.1+
python3 --version   # 3.11 or 3.12   (use `python` on Windows)
mysql --version     # 8+
```

---

## 2. Clone the repo
```bash
git clone <REPO_URL> career-mind
cd career-mind
```

---

## 3. Create and load the database

The dump includes the schema **and** all seeded data (141 careers, 141 job roles,
the skills catalog, and the admin account).

```bash
# 'CREATE DATABASE' is inside the dump, so just pipe it in:
mysql -u root career_mind < database/career_mind.sql      2>/dev/null \
  || mysql -u root < database/career_mind.sql              # first-time (db not created yet)
```
If your MySQL `root` has a password, add `-p` (it will prompt):
```bash
mysql -u root -p < database/career_mind.sql
```

Confirm it loaded:
```bash
mysql -u root -e "USE career_mind; SELECT COUNT(*) AS careers FROM careers; SELECT COUNT(*) AS skills FROM skills;"
```
Expect ~141 careers and ~150+ skills.

---

## 4. Set database credentials (two places)

**a) PHP web app** — edit `career-mind-web/config/config.php`:
```php
'db' => [
    'host' => '127.0.0.1',
    'port' => '3306',
    'name' => 'career_mind',
    'user' => 'root',
    'pass' => '',        // <-- set your MySQL root password here if you have one
    'charset' => 'utf8mb4',
],
```
Leave the `ai.base_url` as `http://127.0.0.1:5001`.

**b) Python AI service** reads DB settings from environment variables (with defaults).
You pass them when you start it (see step 6) — or rely on the defaults
(`127.0.0.1`, `root`, db `career_mind`).

---

## 5. Python virtual environment + dependencies

From the project root:
```bash
python3 -m venv .venv                 # Windows: python -m venv .venv
source .venv/bin/activate             # Windows: .venv\Scripts\activate
pip install -r career-mind-ai/requirements.txt
```
This installs Flask, scikit-learn, pdfplumber, python-docx, mysql-connector, joblib.

> The model is already committed. Only if it's missing or you change the dataset:
> ```bash
> cd career-mind-ai && python train.py && cd ..
> ```

---

## 6. Run both services

### Option A — macOS one-command launcher
```bash
./start.sh        # starts MySQL, frees ports, launches both services
# stop with:
./stop.sh
```

### Option B — manual (any OS, two terminals)

**Terminal 1 — AI service (port 5001):**
```bash
source .venv/bin/activate
cd career-mind-ai
# pass DB creds if your root has a password; otherwise the defaults work:
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=career_mind DB_USER=root DB_PASS="" python app.py
```
Windows (PowerShell):
```powershell
.venv\Scripts\activate
cd career-mind-ai
$env:DB_HOST="127.0.0.1"; $env:DB_PORT="3306"; $env:DB_NAME="career_mind"; $env:DB_USER="root"; $env:DB_PASS=""
python app.py
```

**Terminal 2 — PHP web app (port 8000):**
```bash
cd career-mind-web
php -S 127.0.0.1:8000 -t public
```

---

## 7. Open the app

- Web app: **http://127.0.0.1:8000**
- AI health check: **http://127.0.0.1:5001/health** → should return `{"status":"ok","model_loaded":true}`

### Logins
- **Admin:** email `careermind@gmail.com`, password `careerminds1234`, role **Administrator**
- **Students:** register a new account (signup always creates a student; admin is not self-assignable).

---

## 8. Troubleshooting

| Symptom | Fix |
|---|---|
| "ML Service Unavailable" on dashboard | The AI service (port 5001) isn't running, or `ai.base_url` isn't `http://127.0.0.1:5001`. Start Terminal 1 and check `/health`. |
| DB connection error | Wrong credentials — fix `config.php` (PHP) **and** the `DB_*` env vars (Python). Ensure MySQL is running. |
| `php: command not found` / `mysql: not found` | PHP/MySQL not on PATH — reinstall or add to PATH. |
| Port already in use | Use a different port, e.g. `php -S 127.0.0.1:8001 -t public`. |
| Python "module not found" | Activate the venv, then `pip install -r career-mind-ai/requirements.txt`. |
| Predictions look off after changing data | Retrain: `cd career-mind-ai && python train.py`, then restart the AI service. |

### Optional: start from a clean slate (no demo accounts/CVs)
The shipped dump includes some demo users and CV history. To keep only the
careers/jobs/skills catalog + admin and clear the rest:
```sql
DELETE FROM cv_analyses; DELETE FROM cv_files;
DELETE FROM career_recommendations; DELETE FROM job_recommendations;
DELETE FROM career_prediction_cache;
DELETE FROM user_skills; DELETE FROM user_interests; DELETE FROM profiles;
DELETE FROM users WHERE email <> 'careermind@gmail.com';
```

---

## Summary (the whole thing, macOS)
```bash
brew install php python@3.12 mysql git && brew services start mysql
git clone <REPO_URL> career-mind && cd career-mind
mysql -u root < database/career_mind.sql
python3 -m venv .venv && source .venv/bin/activate
pip install -r career-mind-ai/requirements.txt
./start.sh
# open http://127.0.0.1:8000  (admin: careermind@gmail.com / careerminds1234)
```
