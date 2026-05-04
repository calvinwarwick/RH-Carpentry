# Deploy themes to the live server

This project ships two themes:

- **Parent:** `wp-content/themes/rh-base/`
- **Child:** `wp-content/themes/rh-base-child/` (customisations live here)

Production deploy is done with **rsync over SSH** (see [`scripts/deploy-themes.sh`](../scripts/deploy-themes.sh)).

## Prerequisites

1. **SSH access** to the host (SiteGround or any server with `rsync` + OpenSSH).
2. Your **SSH config** entry, e.g. in `~/.ssh/config`:

   ```sshconfig
   Host sg-calvinw15
       HostName gukm1054.siteground.biz
       User u2404-hqhjr6jnwqpg
       Port 18765
       IdentityFile ~/.ssh/rh_siteground_2026
   ```

   Use the host **alias** you choose (`Host` line) as `REMOTE_HOST`.

3. **Correct remote paths** under your account’s `public_html`.
   This project currently uses:
   - `PUBLIC_HTML=/home/customer/www/rhcarpentry.uk/public_html`
   - `REMOTE_BASE=/home/customer/www/rhcarpentry.uk/public_html/wp-content/themes`

## One-command deploy (recommended)

From the repo root:

```bash
chmod +x scripts/deploy-themes.sh
./scripts/deploy-themes.sh
```

Defaults are set inside the script. Override without editing files:

```bash
export REMOTE_HOST=sg-your-host-alias
export REMOTE_BASE=/home/customer/www/rhcarpentry.uk/public_html/wp-content/themes
./scripts/deploy-themes.sh
```

See [`.env.example`](../.env.example) for variable names.

### What the script does

- **rh-base:** `rsync -avz --delete` (removes remote files missing locally—keeps parent in sync).
- **rh-base-child:** `rsync -avz` (no `--delete` by default—safer for child; adjust script if you need strict mirror).

## Manual rsync (same as script)

Parent:

```bash
rsync -avz --delete \
  --exclude 'node_modules' \
  --exclude '.git' \
  -e ssh \
  ./wp-content/themes/rh-base/ \
  "${REMOTE_HOST}:${REMOTE_BASE}/rh-base/"
```

Child:

```bash
rsync -avz --exclude '.git' -e ssh \
  ./wp-content/themes/rh-base-child/ \
  "${REMOTE_HOST}:${REMOTE_BASE}/rh-base-child/"
```

## After deploy: activate child theme

In **WP Admin → Appearance → Themes**, activate **RH Base Child** (or your child theme name).

If you have **WP-CLI** on the server:

```bash
ssh "${REMOTE_HOST}" "cd ${PUBLIC_HTML} && wp theme activate rh-base-child"
```

Use the same `PUBLIC_HTML` path you use in SSH (repo root on server under `public_html`).

## Parent theme build (optional)

The parent theme may need assets built before deploy:

```bash
cd wp-content/themes/rh-base
npm ci
npm run build
cd ../../..
./scripts/deploy-themes.sh
```

## GitHub: create `RH-Carpentry` and push

1. On GitHub: **New repository** → name **`RH-Carpentry`** → create (no README if you already have commits locally).
2. Locally:

   ```bash
   cd /path/to/RH-Carpentry
   git remote add origin git@github.com:YOUR_USER/RH-Carpentry.git
   git branch -M main
   git push -u origin main
   ```

Replace `YOUR_USER` and use HTTPS if you prefer: `https://github.com/YOUR_USER/RH-Carpentry.git`.

## Security notes

- Do **not** commit production passwords, SFTP secrets, or `.env` with real credentials.
- Keep `/data/` (local DB dumps) out of git (already in `.gitignore`).
