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

## Deploy themes to live server

See **[docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)** for SSH/rsync steps, environment variables, and optional WP-CLI activation.

Quick deploy:

```bash
./scripts/deploy-themes.sh
```

## Licence

WordPress themes follow their respective `readme.txt` / `style.css` licence headers (typically GPL-2.0-or-later).
