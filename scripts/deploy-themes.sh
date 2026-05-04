#!/usr/bin/env bash
# Deploy RH Base parent + child over SSH/rsync (defaults: SiteGround layout).
# Override: REMOTE_HOST, REMOTE_BASE (path to wp-content/themes on server), PUBLIC_HTML (for WP-CLI hint).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REMOTE_HOST="${REMOTE_HOST:-sg-calvinw15}"
REMOTE_BASE="${REMOTE_BASE:-/home/customer/www/rhcarpentry.uk/public_html/wp-content/themes}"
PUBLIC_HTML="${PUBLIC_HTML:-/home/customer/www/rhcarpentry.uk/public_html}"

echo "Syncing rh-base → ${REMOTE_HOST}:${REMOTE_BASE}/rh-base/"
rsync -avz --delete \
	--exclude 'node_modules' \
	--exclude '.git' \
	-e ssh \
	"${ROOT}/wp-content/themes/rh-base/" \
	"${REMOTE_HOST}:${REMOTE_BASE}/rh-base/"

echo "Syncing rh-base-child → ${REMOTE_HOST}:${REMOTE_BASE}/rh-base-child/"
rsync -avz \
	--exclude '.git' \
	-e ssh \
	"${ROOT}/wp-content/themes/rh-base-child/" \
	"${REMOTE_HOST}:${REMOTE_BASE}/rh-base-child/"

echo "Done. Activate the child in WP Admin or run:"
echo "  ssh ${REMOTE_HOST} \"cd ${PUBLIC_HTML} && wp theme activate rh-base-child\""
