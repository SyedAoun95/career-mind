# Running Career Mind with Docker

This is the easiest way to run the whole project. You do **not** need to install
PHP, Python, or MySQL — only Docker.

## Prerequisites

You need the Docker engine + CLI. Pick **one**:

- **Docker Desktop** (GUI) — <https://www.docker.com/products/docker-desktop/>. Install, then launch it once.
- **CLI only, no GUI (macOS)** — run the included installer (uses Homebrew + Colima, fully free):
  ```bash
  ./install-docker-mac.sh
  ```
  After a reboot, start the engine again with `colima start`.

> Getting `zsh: command not found: docker`? That means the Docker **CLI** isn't on
> your PATH yet — run `./install-docker-mac.sh` (macOS) or install Docker Desktop.

## Run it

```bash
git clone git@github.com:SyedAoun95/career-mind.git
cd career-mind
docker compose up --build
```

The first run takes a few minutes (it downloads images and installs dependencies).
When it finishes, open:

- **Web app:** http://localhost:8080
- **AI health check:** http://localhost:5001 is internal-only; the web app reaches it automatically.

The database schema and data load automatically from `database/career_mind.sql`
on the first start.

## Stop it

Press `Ctrl+C`, then to remove the containers:

```bash
docker compose down
```

To also wipe the database and start fresh next time:

```bash
docker compose down -v
```

## What's running

| Service | What it is              | Port            |
|---------|-------------------------|-----------------|
| `web`   | PHP + Apache web app    | 8080 (browser)  |
| `ai`    | Flask AI service        | internal only   |
| `db`    | MySQL 8 database        | internal only   |

All three run on a private Docker network and talk to each other by service name.
