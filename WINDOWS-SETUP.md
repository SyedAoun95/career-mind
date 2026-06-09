# Career Mind — Windows Setup Guide

This guide covers running Career Mind on **Windows 10 / 11**. There are two paths:

- **Path A — Docker (recommended):** install Docker once, run one command. Nothing
  else to install or configure.
- **Path B — Manual:** install PHP, Python, and MySQL yourself and run the app
  directly. Use this only if you can't or don't want to use Docker.

---

## Path A — Docker (recommended)

### 1. Install Git for Windows
Download and install from <https://git-scm.com/download/win> (accept the defaults).
This gives you `git` and **Git Bash**.

### 2. Install Docker Desktop
Docker on Windows runs on the **WSL2** engine (a lightweight Linux layer built into
Windows). Docker Desktop sets this up for you.

**Option 1 — command line (recommended).** Open **PowerShell as Administrator** and
run the included installer (uses `winget` + WSL2):

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\install-docker-windows.ps1
```

Or do it as two plain commands without the script:

```powershell
wsl --install
winget install -e --id Docker.DockerDesktop
```

**Option 2 — manual download.**
1. Download **Docker Desktop for Windows** from
   <https://www.docker.com/products/docker-desktop/>.
2. Run the installer and **leave "Use WSL 2 instead of Hyper-V" checked**.
3. Restart the PC if it asks you to.

Either way, then:
- Reboot if Windows asked you to (needed the first time WSL2 is enabled).
- Launch **Docker Desktop** and wait until the whale icon in the system tray says
  **"Docker Desktop is running"**.
- In a **new** PowerShell window, verify: `docker --version`

### CLI-only, no Docker Desktop (advanced)
If you want Docker with no GUI at all (the Windows equivalent of Colima on Mac),
run the Docker engine inside WSL2:

```powershell
wsl --install          # installs Ubuntu; reboot if asked, then open "Ubuntu"
```
Then **inside the Ubuntu (WSL) shell**:
```bash
curl -fsSL https://get.docker.com | sh      # installs docker + compose
sudo service docker start                    # start the engine
```
Clone and run the project from inside that Ubuntu shell (not PowerShell). For most
people Docker Desktop is simpler — use this only if you specifically want no GUI.

> If Docker says WSL2 is missing, open **PowerShell as Administrator** and run:
> ```powershell
> wsl --install
> ```
> then reboot and start Docker Desktop again.

### 3. Get the code and run it
Open **PowerShell** (normal, not admin) and run:

```powershell
git clone https://github.com/SyedAoun95/career-mind.git
cd career-mind
docker compose up --build
```

The first run downloads images and installs dependencies — give it a few minutes.
When it settles, open your browser to:

```
http://localhost:8080
```

The database and all data load automatically from `database\career_mind.sql`.

### 4. Stop it
Press **Ctrl + C** in the PowerShell window, then:

```powershell
docker compose down
```

To also erase the database (fresh start next time): `docker compose down -v`.

### Docker troubleshooting (Windows)
| Problem | Fix |
|---|---|
| "Docker Desktop requires WSL2" | Run `wsl --install` in admin PowerShell, reboot. |
| "Virtualization not enabled" | Enable **Virtualization / SVM** in the PC's BIOS/UEFI. |
| Port 8080 already in use | Edit `docker-compose.yml` → change `"8080:80"` to e.g. `"9090:80"`, then open `http://localhost:9090`. |
| Containers start but page won't load | Wait ~30s on first run (MySQL is still importing data), then refresh. |

---

## Path B — Manual install (no Docker)

> Your `start.sh` is a Mac/Linux script and will **not** run on Windows PowerShell.
> Use the `start.ps1` PowerShell launcher included in this repo instead (see step 5).

### 1. Install the tools
Install all three (the [Chocolatey](https://chocolatey.org/install) package
manager makes this easy — run these in **admin PowerShell**):

```powershell
choco install php python mysql git -y
```

Or download manually:
- **PHP 8.2+** — <https://windows.php.net/download/> (Thread Safe x64). Add the
  extracted folder to your **PATH**, and in `php.ini` uncomment `extension=pdo_mysql`.
- **Python 3.11+** — <https://www.python.org/downloads/windows/> (check
  **"Add Python to PATH"** during install).
- **MySQL 8** — <https://dev.mysql.com/downloads/installer/> (or install **XAMPP**,
  which bundles MySQL).

Verify each is on your PATH:
```powershell
php --version
python --version
mysql --version
```

### 2. Start MySQL and import the database
Make sure the MySQL service is running (Services app, or `net start MySQL80`), then
import the dump. In PowerShell, use `Get-Content` (PowerShell has no `<` redirect):

```powershell
Get-Content database\career_mind.sql | mysql -u root
```

If your root user has a password, add `-p` and enter it when prompted.

### 3. Configure the database password (only if root has one)
The app defaults to user `root` with an **empty** password. If your MySQL `root`
has a password, set it before launching (PowerShell):

```powershell
$env:DB_PASS = "your_mysql_root_password"
```

### 4. Install the Python dependencies
```powershell
cd career-mind-ai
pip install -r requirements.txt
cd ..
```

### 5. Launch both services
Use the included PowerShell launcher (it starts the AI service and the web app,
mirroring `start.sh`):

```powershell
.\start.ps1
```

If PowerShell blocks the script, allow it for this session first:
```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\start.ps1
```

Then open:
- **Web app:** <http://localhost:8000>
- **AI health:** <http://localhost:5001/health>

To stop them, run:
```powershell
.\stop.ps1
```

#### Or start them by hand (two terminals)
**Terminal 1 — AI service:**
```powershell
cd career-mind-ai
$env:DB_HOST="127.0.0.1"; $env:DB_PORT="3306"; $env:DB_NAME="career_mind"; $env:DB_USER="root"
python app.py
```
**Terminal 2 — Web app:**
```powershell
cd career-mind-web
php -S 127.0.0.1:8000 -t public
```

### Manual troubleshooting (Windows)
| Problem | Fix |
|---|---|
| `php` / `python` / `mysql` "not recognized" | The tool isn't on your PATH. Reinstall with PATH option, or add its folder to PATH and reopen PowerShell. |
| `could not find driver` (PDO) | Enable `extension=pdo_mysql` in `php.ini`, then restart the web server. |
| AI calls fail / timeout | Make sure the AI service (Terminal 1) is actually running on port 5001. |
| `Access denied for user 'root'` | Set the right password: `$env:DB_PASS="..."` before launching. |

---

## Which path should you choose?

| | Path A — Docker | Path B — Manual |
|---|---|---|
| One-time install | Docker Desktop only | PHP + Python + MySQL |
| Setup commands | `docker compose up` | import SQL, pip install, launch 2 services |
| Database import | Automatic | Manual |
| Breaks on version/PATH issues | No | Sometimes |

**Recommendation:** use **Path A (Docker)** unless you have a specific reason not to.
