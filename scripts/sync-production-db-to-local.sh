#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
SSH_HOST="${THESISAPPS_SSH_HOST:-thesisapps-production}"
REMOTE_APP="${THESISAPPS_REMOTE_APP:-/home/thesisapp/public_html}"
REMOTE_BACKUP_ROOT="${THESISAPPS_REMOTE_DB_BACKUPS:-/home/thesisapp/shared/thesisapps/database-sync}"
AUDIT_ROOT="${THESISAPPS_LOCAL_DB_BACKUPS:-${PROJECT_ROOT}/../.codex-audit/thesisapps-db-sync}"
HELPER="${DATABASE_SYNC_HELPER:-${PROJECT_ROOT}/scripts/database-sync-helper.php}"
PHP_BIN="${PHP_BIN:-$(command -v php || true)}"
MYSQL_BIN="${MYSQL_BIN:-$(command -v mysql || true)}"
MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-$(command -v mysqldump || true)}"
GZIP_BIN="${GZIP_BIN:-$(command -v gzip || true)}"
SHA256_BIN="${SHA256_BIN:-$(command -v sha256sum || true)}"
SSH_BIN="${SSH_BIN:-$(command -v ssh || true)}"
SCP_BIN="${SCP_BIN:-$(command -v scp || true)}"
MODE=
LOCK_PATH=
LOCAL_CONFIG=
RESTORE_STARTED=0
RESTORE_COMPLETED=0

usage() {
    cat <<'EOF'
Usage:
  scripts/sync-production-db-to-local.sh --dry-run
  scripts/sync-production-db-to-local.sh --apply

--dry-run validates local and production targets without creating a dump.
--apply backs up the local database, downloads a new read-only production
snapshot, restores it locally, repairs view definers, and verifies the result.
EOF
}

fail() {
    printf 'DATABASE SYNC BLOCKED: %s\n' "$1" >&2
    exit 1
}

cleanup() {
    if [[ -n "$LOCAL_CONFIG" && -f "$LOCAL_CONFIG" ]]; then
        rm -f "$LOCAL_CONFIG"
    fi
    if [[ -n "$LOCK_PATH" && -d "$LOCK_PATH" ]]; then
        rmdir "$LOCK_PATH" 2>/dev/null || true
    fi
}

trap cleanup EXIT

[[ $# -eq 1 ]] || { usage; exit 1; }
case "$1" in
    --dry-run|--apply)
        MODE=$1
        ;;
    --help|-h)
        usage
        exit 0
        ;;
    *)
        usage
        fail "Unknown option: $1"
        ;;
esac

for executable in "$PHP_BIN" "$MYSQL_BIN" "$MYSQLDUMP_BIN" "$GZIP_BIN" \
    "$SHA256_BIN" "$SSH_BIN" "$SCP_BIN"; do
    [[ -n "$executable" && -x "$executable" ]] \
        || fail "Required executable is unavailable: ${executable:-<empty>}"
done

[[ -f "$HELPER" ]] || fail "Database sync helper is missing: $HELPER"
[[ "$SSH_HOST" =~ ^[A-Za-z0-9._-]+$ ]] || fail 'Unsafe SSH host alias.'
[[ "$REMOTE_APP" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote application path.'
[[ "$REMOTE_BACKUP_ROOT" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote backup path.'

LOCAL_DATABASE=$($PHP_BIN "$HELPER" metadata database local)
LOCAL_HOST=$($PHP_BIN "$HELPER" metadata host local)
REMOTE_DATABASE=$(
    "$SSH_BIN" "$SSH_HOST" \
        "cd '$REMOTE_APP' && php scripts/database-sync-helper.php metadata database production"
)

[[ "$LOCAL_DATABASE" != "$REMOTE_DATABASE" ]] \
    || fail 'Local and production database names are identical.'

printf 'Local target: %s@%s\n' "$LOCAL_DATABASE" "$LOCAL_HOST"
printf 'Production source: %s via %s\n' "$REMOTE_DATABASE" "$SSH_HOST"

if [[ "$MODE" == '--dry-run' ]]; then
    printf 'Dry run complete; no dump was created and no database was changed.\n'
    exit 0
fi

mkdir -p "$AUDIT_ROOT"
chmod 700 "$AUDIT_ROOT"
LOCK_PATH="${AUDIT_ROOT}/sync.lock"
if ! mkdir "$LOCK_PATH" 2>/dev/null; then
    fail "Another database sync may be active: $LOCK_PATH"
fi

TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
RUN_PATH="${AUDIT_ROOT}/${TIMESTAMP}"
mkdir "$RUN_PATH"
chmod 700 "$RUN_PATH"
LOCAL_CONFIG="${RUN_PATH}/.local-client.cnf"
LOCAL_BACKUP="${RUN_PATH}/local-before.sql.gz"
LOCAL_VIEW_BACKUP="${RUN_PATH}/local-before-views.sql.gz"
PRODUCTION_DUMP="${RUN_PATH}/production.sql.gz"
LOCALIZED_DUMP="${RUN_PATH}/production-localized.sql.gz"

$PHP_BIN "$HELPER" client-config "$LOCAL_CONFIG" local

VIEW_IGNORE_ARGUMENTS=()
while IFS= read -r view; do
    [[ -n "$view" ]] || continue
    VIEW_IGNORE_ARGUMENTS+=("--ignore-table=${LOCAL_DATABASE}.${view}")
done < <($PHP_BIN "$HELPER" view-names local)

printf 'Backing up local database to %s\n' "$LOCAL_BACKUP"
"$MYSQLDUMP_BIN" \
    "--defaults-extra-file=$LOCAL_CONFIG" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    --events \
    --hex-blob \
    --set-gtid-purged=OFF \
    --column-statistics=0 \
    --no-tablespaces \
    --default-character-set=utf8mb4 \
    "${VIEW_IGNORE_ARGUMENTS[@]}" \
    "$LOCAL_DATABASE" | "$GZIP_BIN" -9 > "$LOCAL_BACKUP"

$PHP_BIN "$HELPER" view-sql local | "$GZIP_BIN" -9 > "$LOCAL_VIEW_BACKUP"
chmod 600 "$LOCAL_BACKUP" "$LOCAL_VIEW_BACKUP"
"$GZIP_BIN" -t "$LOCAL_BACKUP"
"$GZIP_BIN" -t "$LOCAL_VIEW_BACKUP"
"$SHA256_BIN" "$LOCAL_BACKUP" "$LOCAL_VIEW_BACKUP" > "${RUN_PATH}/local-before.sha256"

printf 'Creating a read-only production snapshot.\n'
"$SSH_BIN" "$SSH_HOST" \
    "bash -s -- '$REMOTE_APP' '$REMOTE_BACKUP_ROOT' '$TIMESTAMP'" <<'REMOTE'
set -Eeuo pipefail
umask 077

REMOTE_APP=$1
REMOTE_BACKUP_ROOT=$2
TIMESTAMP=$3
HELPER="${REMOTE_APP}/scripts/database-sync-helper.php"
PHP_BIN=$(command -v php)
MYSQLDUMP_BIN=$(command -v mysqldump)
GZIP_BIN=$(command -v gzip)
SHA256_BIN=$(command -v sha256sum)
CLIENT_CONFIG="${REMOTE_BACKUP_ROOT}/.client-${TIMESTAMP}.cnf"
RAW_DUMP="${REMOTE_BACKUP_ROOT}/production-${TIMESTAMP}.sql"
COMPRESSED_DUMP="${RAW_DUMP}.gz"

cleanup_remote() {
    rm -f "$CLIENT_CONFIG" "$RAW_DUMP"
}
trap cleanup_remote EXIT

mkdir -p "$REMOTE_BACKUP_ROOT"
chmod 700 "$REMOTE_BACKUP_ROOT"
cd "$REMOTE_APP"
"$PHP_BIN" "$HELPER" client-config "$CLIENT_CONFIG" production
REMOTE_DATABASE=$("$PHP_BIN" "$HELPER" metadata database production)

"$MYSQLDUMP_BIN" \
    "--defaults-extra-file=$CLIENT_CONFIG" \
    --single-transaction \
    --quick \
    --triggers \
    --hex-blob \
    --no-tablespaces \
    --skip-routines \
    --skip-events \
    --default-character-set=utf8mb4 \
    "$REMOTE_DATABASE" > "$RAW_DUMP"

"$GZIP_BIN" -9 "$RAW_DUMP"
"$GZIP_BIN" -t "$COMPRESSED_DUMP"
"$SHA256_BIN" "$COMPRESSED_DUMP" > "${COMPRESSED_DUMP}.sha256"
chmod 600 "$COMPRESSED_DUMP" "${COMPRESSED_DUMP}.sha256"
REMOTE

REMOTE_DUMP="${REMOTE_BACKUP_ROOT}/production-${TIMESTAMP}.sql.gz"
"$SCP_BIN" "${SSH_HOST}:${REMOTE_DUMP}" "$PRODUCTION_DUMP"
"$SCP_BIN" "${SSH_HOST}:${REMOTE_DUMP}.sha256" "${PRODUCTION_DUMP}.remote.sha256"
chmod 600 "$PRODUCTION_DUMP" "${PRODUCTION_DUMP}.remote.sha256"
"$GZIP_BIN" -t "$PRODUCTION_DUMP"

EXPECTED_HASH=$(awk '{print $1}' "${PRODUCTION_DUMP}.remote.sha256")
ACTUAL_HASH=$("$SHA256_BIN" "$PRODUCTION_DUMP" | awk '{print $1}')
[[ "$EXPECTED_HASH" == "$ACTUAL_HASH" ]] || fail 'Production dump checksum mismatch.'

REPLACEMENTS=$($PHP_BIN "$HELPER" localize "$PRODUCTION_DUMP" "$LOCALIZED_DUMP")
[[ "$REPLACEMENTS" =~ ^[0-9]+$ ]] \
    || fail 'Production dump definer localization returned an invalid result.'
"$GZIP_BIN" -t "$LOCALIZED_DUMP"
"$SHA256_BIN" "$LOCALIZED_DUMP" > "${LOCALIZED_DUMP}.sha256"

restore_archive() {
    local archive=$1
    "$GZIP_BIN" -cd "$archive" | "$MYSQL_BIN" \
        "--defaults-extra-file=$LOCAL_CONFIG" \
        --default-character-set=utf8mb4 \
        "--database=$LOCAL_DATABASE"
}

rollback_local() {
    printf 'Restoring the pre-sync local database backup.\n' >&2
    restore_archive "$LOCAL_BACKUP"
    restore_archive "$LOCAL_VIEW_BACKUP"
}

printf 'Restoring the production snapshot into the local database.\n'
RESTORE_STARTED=1
set +e
restore_archive "$LOCALIZED_DUMP"
RESTORE_STATUS=$?
set -e

if [[ "$RESTORE_STATUS" -ne 0 ]]; then
    rollback_local || fail 'Restore failed and automatic local rollback also failed.'
    fail 'Production restore failed; the original local database was restored.'
fi

if ! $PHP_BIN "$HELPER" counts local > "${RUN_PATH}/local-counts.tsv"; then
    rollback_local || fail 'Verification failed and automatic local rollback also failed.'
    fail 'Local database verification failed; the original local database was restored.'
fi
RESTORE_COMPLETED=1

$PHP_BIN "$HELPER" digests local > "${RUN_PATH}/local-digests.tsv"
"$SSH_BIN" "$SSH_HOST" \
    "cd '$REMOTE_APP' && php scripts/database-sync-helper.php counts production" \
    > "${RUN_PATH}/production-counts.tsv"
"$SSH_BIN" "$SSH_HOST" \
    "cd '$REMOTE_APP' && php scripts/database-sync-helper.php digests production" \
    > "${RUN_PATH}/production-digests.tsv"

LC_ALL=C sort -o "${RUN_PATH}/local-counts.tsv" "${RUN_PATH}/local-counts.tsv"
LC_ALL=C sort -o "${RUN_PATH}/production-counts.tsv" "${RUN_PATH}/production-counts.tsv"

LIVE_MATCH=yes
if ! cmp -s "${RUN_PATH}/local-counts.tsv" "${RUN_PATH}/production-counts.tsv" \
    || ! cmp -s "${RUN_PATH}/local-digests.tsv" "${RUN_PATH}/production-digests.tsv"; then
    LIVE_MATCH=no
fi

(cd "$PROJECT_ROOT" && "$PHP_BIN" artisan config:clear >/dev/null)
(cd "$PROJECT_ROOT" && "$PHP_BIN" artisan route:list >/dev/null)

cat > "${RUN_PATH}/REPORT.txt" <<EOF
Database sync completed: ${TIMESTAMP}
Local database: ${LOCAL_DATABASE}
Production database: ${REMOTE_DATABASE}
Production dump SHA-256: ${ACTUAL_HASH}
Localized view definers: ${REPLACEMENTS}
Matches live production after restore: ${LIVE_MATCH}
Local backup: ${LOCAL_BACKUP}
Production snapshot: ${PRODUCTION_DUMP}
EOF
chmod 600 "${RUN_PATH}/REPORT.txt"

printf 'DATABASE SYNC SUCCESS: %s\n' "$TIMESTAMP"
printf 'Backup and audit: %s\n' "$RUN_PATH"
if [[ "$LIVE_MATCH" == yes ]]; then
    printf 'Local counts and critical-table digests match live production.\n'
else
    printf 'NOTICE: production changed after the snapshot; local contains the verified snapshot.\n'
fi
