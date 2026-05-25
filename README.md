# RH-Carpentry

WordPress site project for **RH Carpentry**: themes, local Docker environment, and deployment tooling.

## Contents

- **`wp-content/themes/rh-base-child/`** — main customisations (deploy this to production).
- **`wp-content/themes/rh-base/`** — parent theme (sync with care; run `npm run build` if you change SCSS/JS).
- **`docker-compose.yml`** — local WordPress + MariaDB + phpMyAdmin.

## Local development

```bash
docker compose up -d
```

- Site: `http://localhost:8088`
- phpMyAdmin: `http://localhost:8082`
- **Dev favicon:** green circle on local (`localhost` / `WP_ENVIRONMENT_TYPE=local`) so you can tell it apart from live

### Copy production to local (full site)

Requires SSH host `sg-calvinw15` (see `.env.example`). Pulls database, uploads, plugins, and themes from live, then rewrites URLs to `http://localhost:8088`:

```bash
chmod +x scripts/sync-from-production.sh scripts/pull-remote-db.sh
./scripts/sync-from-production.sh
```

Database dumps are saved under `data/` (gitignored). To refresh only the DB:

```bash
./scripts/pull-remote-db.sh
```

## Deploy themes to live server

See **[docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)** for SSH/rsync steps, environment variables, and optional WP-CLI activation.

Quick deploy:

```bash
./scripts/deploy-themes.sh
```

## Licence

WordPress themes follow their respective `readme.txt` / `style.css` licence headers (typically GPL-2.0-or-later).
