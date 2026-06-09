# INSTALL — exact commands (copy & paste in order)

You have a clean machine with nothing installed. Pick your OS section and run the
commands **in order, top to bottom**. Don't skip any.

Final result: app at **http://127.0.0.1:8000**, admin login
`careermind@gmail.com` / `careerminds1234`.

---

# ========== macOS ==========

### 1. Install Homebrew (skip if `brew --version` already works)
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2. Install PHP, Python, MySQL, Git
```bash
brew install php python@3.12 mysql git
```

### 3. Start MySQL
```bash
brew services start mysql
```

### 4. Clone the project
```bash
cd ~/Downloads
git clone <REPO_URL> career-mind
cd career-mind
```

### 5. Load the database (schema + all data)
```bash
mysql -u root < database/career_mind.sql
```

### 6. Create the Python environment and install dependencies
```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r career-mind-ai/requirements.txt
```

### 7. Start everything
```bash
./start.sh
```

### 8. Open the app
Open **http://127.0.0.1:8000** in your browser.
To stop later: `./stop.sh`

---

# ========== Windows (using VS Code) ==========

You'll do everything inside **VS Code's integrated terminal** (which is PowerShell by default).

### 1. Install the tools first
Install VS Code, PHP, Python, MySQL, and Git. Open the normal **PowerShell as Administrator**
(Start menu → type "PowerShell" → right-click → Run as administrator) and run:
```powershell
winget install --id Microsoft.VisualStudioCode -e
winget install --id PHP.PHP.8.3 -e
winget install --id Python.Python.3.12 -e
winget install --id Oracle.MySQL -e
winget install --id Git.Git -e
```
Then **restart your PC** (so PHP/Python/MySQL are added to PATH and MySQL service registers).

### 2. Start MySQL
Open PowerShell as Administrator again and run:
```powershell
net start MySQL
```
(If that exact name fails: Start menu → "Services" → find the MySQL service → click **Start**.)

### 3. Clone the project
In any PowerShell window:
```powershell
cd $HOME\Downloads
git clone <REPO_URL> career-mind
```

### 4. Open the project in VS Code
```powershell
code $HOME\Downloads\career-mind
```
(Or open VS Code → **File → Open Folder** → pick the `career-mind` folder.)

### 5. Open a terminal in VS Code
In VS Code: top menu **Terminal → New Terminal** (or press **Ctrl + `**).
A PowerShell terminal opens at the project folder. Run everything below in it.

> If a window pops up about running scripts, run this once in the VS Code terminal:
> ```powershell
> Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
> ```

### 6. Load the database
```powershell
Get-Content database\career_mind.sql | mysql -u root
```

### 7. Create the Python environment and install dependencies
```powershell
python -m venv .venv
.venv\Scripts\Activate.ps1
pip install -r career-mind-ai\requirements.txt
```

### 8. Start the AI service in this terminal (leave it running)
```powershell
cd career-mind-ai
$env:DB_HOST="127.0.0.1"; $env:DB_PORT="3306"; $env:DB_NAME="career_mind"; $env:DB_USER="root"; $env:DB_PASS=""
python app.py
```
Leave this terminal alone — it's the AI service. Don't close it.

### 9. Open a SECOND terminal in VS Code for the web app
Click the **split terminal** icon (the ⊞ in the terminal panel) or **Terminal → New Terminal**
again. A new terminal opens. In it run:
```powershell
cd career-mind-web
php -S 127.0.0.1:8000 -t public
```

### 10. Open the app
**Ctrl + click** the `http://127.0.0.1:8000` link in the terminal, or open it in your browser.

---

# ========== Ubuntu / Debian Linux ==========

### 1. Install PHP, Python, MySQL, Git
```bash
sudo apt update
sudo apt install -y php php-cli php-mysql python3 python3-venv python3-pip mysql-server git
```

### 2. Start MySQL
```bash
sudo service mysql start
```

### 3. Clone the project
```bash
cd ~
git clone <REPO_URL> career-mind
cd career-mind
```

### 4. Load the database
```bash
sudo mysql < database/career_mind.sql
```

### 5. Create the Python environment and install dependencies
```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r career-mind-ai/requirements.txt
```

### 6. Start the AI service (this terminal — leave it running)
```bash
cd career-mind-ai
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=career_mind DB_USER=root DB_PASS="" python3 app.py
```

### 7. Start the web app (open a SECOND terminal)
```bash
cd ~/career-mind/career-mind-web
php -S 127.0.0.1:8000 -t public
```

### 8. Open the app
Open **http://127.0.0.1:8000** in your browser.

---

# Check it's working
Open **http://127.0.0.1:5001/health** — it must show:
```json
{"status":"ok","model_loaded":true}
```
Then log in at http://127.0.0.1:8000 with `careermind@gmail.com` / `careerminds1234`.

---

# If your MySQL root has a PASSWORD

The commands above assume MySQL `root` has **no password** (the default after a fresh
`brew install` / `apt install`). If yours has one, do BOTH of these:

1. When loading the database, add `-p` (it will prompt for the password):
   ```bash
   mysql -u root -p < database/career_mind.sql
   ```
2. Edit `career-mind-web/config/config.php` and put the password in the `pass` line:
   ```php
   'pass' => 'YOUR_PASSWORD_HERE',
   ```
3. When starting the AI service, set `DB_PASS` to it:
   ```bash
   DB_PASS="YOUR_PASSWORD_HERE" python app.py
   ```

---

# Common errors

| Error | Fix |
|---|---|
| `command not found: php / python / mysql` | The install step didn't finish, or PATH not refreshed. Close and reopen the terminal; re-run the install step. |
| `Access denied for user 'root'` | Your MySQL root has a password — see the section above. |
| Web page loads but says **"ML Service Unavailable"** | The AI service isn't running. Make sure the AI terminal (port 5001) is still open and `http://127.0.0.1:5001/health` works. |
| `Address already in use` on 8000 or 5001 | Something else uses the port. Stop it, or change the web port: `php -S 127.0.0.1:8001 -t public`. |
