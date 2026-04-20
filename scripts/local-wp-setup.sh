#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SITE_URL="${SITE_URL:-http://localhost:8088}"
SITE_TITLE="${SITE_TITLE:-RH Carpentry Local}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin123!}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.local}"

cd "${ROOT_DIR}"

echo "Starting local WordPress stack..."
docker compose up -d

echo "Waiting for WordPress container..."
until docker compose exec -T wordpress sh -lc "php -v >/dev/null 2>&1"; do
  sleep 2
done

echo "Installing WordPress core (if needed)..."
if docker compose run --rm wpcli core is-installed --path=/var/www/html >/dev/null 2>&1; then
  echo "WordPress is already installed."
else
  docker compose run --rm wpcli core install \
    --path=/var/www/html \
    --url="${SITE_URL}" \
    --title="${SITE_TITLE}" \
    --admin_user="${ADMIN_USER}" \
    --admin_password="${ADMIN_PASSWORD}" \
    --admin_email="${ADMIN_EMAIL}" \
    --skip-email
fi

echo "Activating RH Base Child theme..."
docker compose run --rm wpcli theme activate rh-base-child --path=/var/www/html

echo ""
echo "Local site ready:"
echo "  Site:  ${SITE_URL}"
echo "  Admin: ${SITE_URL}/wp-admin"
echo "  User:  ${ADMIN_USER}"
echo "  Pass:  ${ADMIN_PASSWORD}"
echo "  phpMyAdmin: http://localhost:8082"
