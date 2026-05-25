#!/usr/bin/env bash
# Mirror production WordPress to the local Docker stack (files + database + URLs).
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

REMOTE_HOST="${REMOTE_HOST:-sg-calvinw15}"
REMOTE_WP_PATH="${REMOTE_WP_PATH:-/home/customer/www/rhcarpentry.uk/public_html}"
REMOTE_WP_CONTENT="${REMOTE_WP_PATH}/wp-content"
LOCAL_WP_CONTENT="${ROOT_DIR}/wp-content"
SSH_BATCH_MODE="${SSH_BATCH_MODE:-1}"

RSYNC_SSH=(ssh)
if [[ "${SSH_BATCH_MODE}" == "1" ]]; then
	RSYNC_SSH+=(-o BatchMode=yes)
fi

rsync_remote() {
	local label="$1"
	local remote_path="$2"
	local local_path="$3"
	shift 3
	echo ""
	echo "Syncing ${label}..."
	mkdir -p "${local_path}"
	rsync -avz --delete -e "${RSYNC_SSH[*]}" "$@" \
		"${REMOTE_HOST}:${remote_path}/" \
		"${local_path}/"
}

echo "=== RH Carpentry: sync production → local ==="
echo "Remote: ${REMOTE_HOST}:${REMOTE_WP_PATH}"
echo "Local:  ${ROOT_DIR} (Docker → http://localhost:8088)"
echo ""

echo "Starting local stack..."
docker compose up -d db wordpress phpmyadmin

rsync_remote "uploads (media)" \
	"${REMOTE_WP_CONTENT}/uploads" \
	"${LOCAL_WP_CONTENT}/uploads"

rsync_remote "plugins" \
	"${REMOTE_WP_CONTENT}/plugins" \
	"${LOCAL_WP_CONTENT}/plugins"

rsync_remote "themes" \
	"${REMOTE_WP_CONTENT}/themes" \
	"${LOCAL_WP_CONTENT}/themes"

echo ""
echo "Importing database and rewriting URLs..."
"${ROOT_DIR}/scripts/pull-remote-db.sh"

echo ""
echo "Flushing permalinks and cache..."
docker compose run --rm wpcli rewrite flush --path=/var/www/html --hard >/dev/null
docker compose run --rm wpcli cache flush --path=/var/www/html >/dev/null 2>&1 || true

echo ""
echo "=== Sync complete ==="
echo "  Site:       http://localhost:8088"
echo "  Admin:      http://localhost:8088/wp-admin"
echo "  phpMyAdmin: http://localhost:8082"
echo ""
echo "Log in with your production WordPress user (same password as live)."
echo "Re-run anytime: ./scripts/sync-from-production.sh"
