#!/usr/bin/env bash
# Download production DB via SSH + WP-CLI, import into local Docker MariaDB, fix URLs.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

REMOTE_HOST="${REMOTE_HOST:-sg-calvinw15}"
REMOTE_WP_PATH="${REMOTE_WP_PATH:-www/calvinw15.sg-host.com/public_html}"
LOCAL_URL="${LOCAL_URL:-http://localhost:8088}"
DUMP_DIR="${ROOT_DIR}/data"
STAMP="$(date +%Y%m%d-%H%M%S)"
DUMP_FILE="${DUMP_DIR}/remote-db-${STAMP}.sql"

mkdir -p "${DUMP_DIR}"

echo "Fetching site URL from ${REMOTE_HOST}..."
REMOTE_URL="$(ssh -o BatchMode=yes "${REMOTE_HOST}" "cd ${REMOTE_WP_PATH} && wp option get siteurl 2>/dev/null" | tr -d '\r')"
if [[ -z "${REMOTE_URL}" ]]; then
	echo "Could not read remote siteurl. Check REMOTE_HOST and REMOTE_WP_PATH." >&2
	exit 1
fi
echo "Remote site URL: ${REMOTE_URL}"

echo "Exporting database to ${DUMP_FILE}..."
ssh -o BatchMode=yes "${REMOTE_HOST}" "cd ${REMOTE_WP_PATH} && wp db export - --add-drop-table" > "${DUMP_FILE}"
echo "Dump size: $(wc -c < "${DUMP_FILE}") bytes"

echo "Starting local stack (if needed)..."
docker compose up -d db wordpress

echo "Importing into local MariaDB..."
docker compose exec -T db mariadb -uwordpress -pwordpress wordpress < "${DUMP_FILE}"

echo "Replacing URLs ( → ${LOCAL_URL})..."
docker compose run --rm wpcli search-replace "${REMOTE_URL}" "${LOCAL_URL}" \
	--path=/var/www/html --all-tables --skip-columns=guid

# Also swap scheme if content references the alternate (avoid mangling unrelated strings)
if [[ "${REMOTE_URL}" == https://* ]]; then
	ALT_URL="http://${REMOTE_URL#https://}"
elif [[ "${REMOTE_URL}" == http://* ]]; then
	ALT_URL="https://${REMOTE_URL#http://}"
else
	ALT_URL=""
fi
if [[ -n "${ALT_URL}" && "${ALT_URL}" != "${REMOTE_URL}" ]]; then
	docker compose run --rm wpcli search-replace "${ALT_URL}" "${LOCAL_URL}" \
		--path=/var/www/html --all-tables --skip-columns=guid || true
fi

echo ""
echo "Done. Latest dump: ${DUMP_FILE}"
echo "Local site: ${LOCAL_URL}"
