#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

APP_PATH="${APP_PATH:-/home/thesisapp/repositories/thesisapp}"
DEPLOY_PATH="${DEPLOY_PATH:-/home/thesisapp/public_html}"
SHARED_PATH="${SHARED_PATH:-/home/thesisapp/shared/thesisapps}"
OFFICIAL_PATH="${OFFICIAL_PATH:-${SHARED_PATH}/official-assets}"
APPROVAL_FILE="${APPROVAL_FILE:-${SHARED_PATH}/deploy-approved-commit}"
EXCLUDE_FILE="${EXCLUDE_FILE:-${APP_PATH}/scripts/deploy-excludes.txt}"
SYNC_SCRIPT="${SYNC_SCRIPT:-${APP_PATH}/scripts/sync-release.php}"
BACKUP_ROOT="${BACKUP_ROOT:-${SHARED_PATH}/deploy-backups}"
LOCK_PATH="${LOCK_PATH:-${SHARED_PATH}/deploy.lock}"
MANAGED_MANIFEST="${MANAGED_MANIFEST:-${SHARED_PATH}/managed-files.json}"

fail() {
    printf 'DEPLOY BLOCKED: %s\n' "$1" >&2
    exit 1
}

for command_name in git; do
    command -v "$command_name" >/dev/null 2>&1 || fail "Required command is unavailable: ${command_name}"
done

PHP_BIN="${PHP_BIN:-$(command -v php || true)}"
COMPOSER_BIN="${COMPOSER_BIN:-$(command -v composer || true)}"

if [[ -z "$COMPOSER_BIN" && -x /opt/cpanel/composer/bin/composer ]]; then
    COMPOSER_BIN=/opt/cpanel/composer/bin/composer
fi

[[ -n "$PHP_BIN" ]] || fail 'PHP CLI is unavailable.'
[[ -n "$COMPOSER_BIN" ]] || fail 'Composer is unavailable.'
[[ -f "${APP_PATH}/artisan" ]] || fail 'Repository does not contain artisan.'
[[ -f "${APP_PATH}/composer.lock" ]] || fail 'Repository does not contain composer.lock.'
[[ -f "$EXCLUDE_FILE" ]] || fail 'Deployment exclusion file is missing.'
[[ -f "$SYNC_SCRIPT" ]] || fail 'Deployment synchronization script is missing.'
[[ -f "${DEPLOY_PATH}/.env" ]] || fail 'Production .env is missing.'
[[ -d "${DEPLOY_PATH}/storage" ]] || fail 'Production storage directory is missing.'
[[ -d "$OFFICIAL_PATH" ]] || fail 'Persistent official asset directory is missing.'
[[ $(find "$OFFICIAL_PATH" -maxdepth 1 -type f -name '*.png' | wc -l | tr -d ' ') -ge 7 ]] \
    || fail 'Persistent official assets are incomplete.'
grep -Fqx "OFFICIAL_ASSET_PATH=${OFFICIAL_PATH}" "${DEPLOY_PATH}/.env" \
    || fail 'Production .env does not point to persistent official assets.'

if [[ -e "${DEPLOY_PATH}/public/storage" && ! -L "${DEPLOY_PATH}/public/storage" ]]; then
    fail 'public/storage exists but is not a symbolic link.'
fi

CURRENT_COMMIT=$(git -C "$APP_PATH" rev-parse HEAD)
[[ -f "$APPROVAL_FILE" ]] || fail "Approval file is missing for commit ${CURRENT_COMMIT}."
APPROVED_COMMIT=$(tr -d '[:space:]' < "$APPROVAL_FILE")
[[ "$APPROVED_COMMIT" == "$CURRENT_COMMIT" ]] \
    || fail "Approved commit ${APPROVED_COMMIT:-<empty>} does not match ${CURRENT_COMMIT}."

mkdir -p "$SHARED_PATH" "$BACKUP_ROOT"
chmod 700 "$SHARED_PATH" "$BACKUP_ROOT" "$OFFICIAL_PATH"

if ! mkdir "$LOCK_PATH" 2>/dev/null; then
    fail "Another deployment may be active: ${LOCK_PATH}"
fi
trap 'rmdir "$LOCK_PATH" 2>/dev/null || true' EXIT

TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
BACKUP_PATH="${BACKUP_ROOT}/${TIMESTAMP}-${CURRENT_COMMIT}"
BACKUP_MANIFEST="${BACKUP_ROOT}/${TIMESTAMP}-${CURRENT_COMMIT}.json"
mkdir -p "$BACKUP_PATH"

printf 'Preparing dependencies for %s\n' "$CURRENT_COMMIT"
(
    cd "$APP_PATH"
    "$COMPOSER_BIN" install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --optimize-autoloader \
        --no-scripts
)

printf 'Backing up current production source to %s\n' "$BACKUP_PATH"
"$PHP_BIN" "$SYNC_SCRIPT" \
    "$DEPLOY_PATH" \
    "$BACKUP_PATH" \
    "$EXCLUDE_FILE" \
    /dev/null \
    "$BACKUP_MANIFEST"

PREVIOUS_MANIFEST="$MANAGED_MANIFEST"
if [[ ! -f "$PREVIOUS_MANIFEST" ]]; then
    PREVIOUS_MANIFEST="$BACKUP_MANIFEST"
fi

WAS_DOWN=0
if [[ -f "${DEPLOY_PATH}/storage/framework/down" ]]; then
    WAS_DOWN=1
fi

set +e
(
    set -Eeuo pipefail
    cd "$DEPLOY_PATH"
    "$PHP_BIN" artisan down

    "$PHP_BIN" "$SYNC_SCRIPT" \
        "$APP_PATH" \
        "$DEPLOY_PATH" \
        "$EXCLUDE_FILE" \
        "$PREVIOUS_MANIFEST" \
        "$MANAGED_MANIFEST"

    ln -sfn "${DEPLOY_PATH}/storage/app/public" "${DEPLOY_PATH}/public/storage"
    chmod -R u+rwX "${DEPLOY_PATH}/storage" "${DEPLOY_PATH}/bootstrap/cache"

    "$PHP_BIN" artisan package:discover --ansi
    "$PHP_BIN" artisan config:clear
    "$PHP_BIN" artisan route:clear
    "$PHP_BIN" artisan view:clear

    if [[ "$WAS_DOWN" -eq 0 ]]; then
        "$PHP_BIN" artisan up
    fi
)
DEPLOY_STATUS=$?
set -e

if [[ "$DEPLOY_STATUS" -ne 0 ]]; then
    printf 'Deployment failed; restoring source backup.\n' >&2
    "$PHP_BIN" "$SYNC_SCRIPT" \
        "$BACKUP_PATH" \
        "$DEPLOY_PATH" \
        "$EXCLUDE_FILE" \
        "$MANAGED_MANIFEST" \
        "$MANAGED_MANIFEST"

    if [[ "$WAS_DOWN" -eq 0 ]]; then
        (cd "$DEPLOY_PATH" && "$PHP_BIN" artisan up) || true
    fi

    exit "$DEPLOY_STATUS"
fi

rm -f "$APPROVAL_FILE"
printf 'DEPLOY SUCCESS: %s\n' "$CURRENT_COMMIT"
