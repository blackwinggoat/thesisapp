#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
SSH_HOST="${THESISAPPS_SSH_HOST:-thesisapps-production}"
REMOTE_REPOSITORY="${THESISAPPS_REMOTE_REPOSITORY:-/home/thesisapp/repositories/thesisapp}"
REMOTE_SHARED="${THESISAPPS_REMOTE_SHARED:-/home/thesisapp/shared/thesisapps}"
REMOTE_DB_BACKUPS="${THESISAPPS_REMOTE_DB_BACKUPS:-${REMOTE_SHARED}/database-sync}"
BACKUP_DESTINATION="${THESISAPPS_EXTERNAL_BACKUP_DESTINATION:-${PROJECT_ROOT}/../.codex-audit/thesisapps-external-backups}"
PASSPHRASE="${THESISAPPS_EXTERNAL_BACKUP_PASSPHRASE:-}"
MODE=dry-run

usage() {
    cat <<'EOF'
Usage:
  scripts/backup-production-source-db.sh --dry-run
  THESISAPPS_EXTERNAL_BACKUP_PASSPHRASE='...' scripts/backup-production-source-db.sh --apply

Creates an encrypted recovery archive outside the hosting account. It contains
the deployed Git source, the newest production database snapshot, and official
assets. Runtime uploads, storage, and .env are intentionally excluded.
EOF
}

fail() {
    printf 'EXTERNAL BACKUP BLOCKED: %s\n' "$1" >&2
    exit 1
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)
            MODE=dry-run
            shift
            ;;
        --apply)
            MODE=apply
            shift
            ;;
        --help|-h)
            usage
            exit 0
            ;;
        *)
            fail "Unknown option: $1"
            ;;
    esac
done

for command_name in git ssh scp tar gzip openssl; do
    command -v "$command_name" >/dev/null 2>&1 || fail "Required command is unavailable: ${command_name}"
done

SHA256_BIN=$(command -v shasum || command -v sha256sum || true)
[[ -n "$SHA256_BIN" ]] || fail 'A SHA-256 command is unavailable.'
[[ "$REMOTE_REPOSITORY" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote repository path.'
[[ "$REMOTE_SHARED" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote shared path.'
[[ "$REMOTE_DB_BACKUPS" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote database backup path.'
[[ "$BACKUP_DESTINATION" = /* ]] || fail 'Backup destination must be an absolute path.'

sha256_file() {
    if [[ "$(basename "$SHA256_BIN")" == 'shasum' ]]; then
        "$SHA256_BIN" -a 256 "$1" | awk '{print $1}'
    else
        "$SHA256_BIN" "$1" | awk '{print $1}'
    fi
}

REMOTE_COMMIT=$(ssh "$SSH_HOST" "git -C '$REMOTE_REPOSITORY' rev-parse HEAD")
[[ "$REMOTE_COMMIT" =~ ^[0-9a-f]{40}$ ]] || fail 'Production commit is invalid.'

REMOTE_DB_DUMP=$(ssh "$SSH_HOST" "find '$REMOTE_DB_BACKUPS' -maxdepth 1 -type f -name 'production-*.sql.gz' -printf '%T@ %p\\n' | sort -nr | head -n 1 | cut -d' ' -f2-")
[[ "$REMOTE_DB_DUMP" =~ ^${REMOTE_DB_BACKUPS}/production-[0-9]{8}T[0-9]{6}Z\.sql\.gz$ ]] \
    || fail 'No valid production database snapshot is available.'

printf 'Production commit: %s\n' "$REMOTE_COMMIT"
printf 'Production database snapshot: %s\n' "$(basename "$REMOTE_DB_DUMP")"
printf 'External destination: %s\n' "$BACKUP_DESTINATION"
printf 'Scope: Git source, production database snapshot, and official assets only.\n'
printf 'Excluded: user uploads, runtime storage, and .env.\n'

if [[ "$MODE" == dry-run ]]; then
    printf 'Dry run complete; no external archive was created.\n'
    exit 0
fi

[[ -n "$PASSPHRASE" ]] || fail 'THESISAPPS_EXTERNAL_BACKUP_PASSPHRASE is required for encrypted backup.'

git -C "$PROJECT_ROOT" fetch origin main
git -C "$PROJECT_ROOT" cat-file -e "${REMOTE_COMMIT}^{commit}" \
    || fail 'Production commit is not available in the local Git repository.'

TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
BACKUP_NAME="thesisapps-${TIMESTAMP}-${REMOTE_COMMIT:0:12}"
BACKUP_PATH="${BACKUP_DESTINATION}/${BACKUP_NAME}"
STAGE_PATH=$(mktemp -d)
VERIFY_PATH=$(mktemp -d)

cleanup() {
    rm -rf "$STAGE_PATH" "$VERIFY_PATH"
}
trap cleanup EXIT

mkdir -p "$BACKUP_PATH"
chmod 700 "$BACKUP_DESTINATION" "$BACKUP_PATH"

git -C "$PROJECT_ROOT" archive --format=tar "$REMOTE_COMMIT" | gzip -9 > "${STAGE_PATH}/source-code.tar.gz"
scp "${SSH_HOST}:${REMOTE_DB_DUMP}" "${STAGE_PATH}/production.sql.gz"
ssh "$SSH_HOST" "tar -C '${REMOTE_SHARED}/official-assets' -czf - ." > "${STAGE_PATH}/official-assets.tar.gz"

gzip -t "${STAGE_PATH}/source-code.tar.gz"
gzip -t "${STAGE_PATH}/production.sql.gz"
gzip -t "${STAGE_PATH}/official-assets.tar.gz"

SOURCE_HASH=$(sha256_file "${STAGE_PATH}/source-code.tar.gz")
DATABASE_HASH=$(sha256_file "${STAGE_PATH}/production.sql.gz")
ASSET_HASH=$(sha256_file "${STAGE_PATH}/official-assets.tar.gz")

cat > "${STAGE_PATH}/manifest.txt" <<EOF
Backup created: ${TIMESTAMP}
Production commit: ${REMOTE_COMMIT}
Production database snapshot: $(basename "$REMOTE_DB_DUMP")
Scope: Git source, production database snapshot, official assets
Excluded: user uploads, runtime storage, .env
source-code.tar.gz SHA-256: ${SOURCE_HASH}
production.sql.gz SHA-256: ${DATABASE_HASH}
official-assets.tar.gz SHA-256: ${ASSET_HASH}
EOF

tar -C "$STAGE_PATH" -czf "${STAGE_PATH}/payload.tar.gz" \
    source-code.tar.gz production.sql.gz official-assets.tar.gz manifest.txt

openssl enc -aes-256-cbc -pbkdf2 -iter 250000 -md sha256 -salt \
    -in "${STAGE_PATH}/payload.tar.gz" \
    -out "${BACKUP_PATH}/payload.tar.gz.enc" \
    -pass env:THESISAPPS_EXTERNAL_BACKUP_PASSPHRASE

ENCRYPTED_HASH=$(sha256_file "${BACKUP_PATH}/payload.tar.gz.enc")
printf '%s  %s\n' "$ENCRYPTED_HASH" 'payload.tar.gz.enc' > "${BACKUP_PATH}/payload.tar.gz.enc.sha256"

openssl enc -d -aes-256-cbc -pbkdf2 -iter 250000 -md sha256 \
    -in "${BACKUP_PATH}/payload.tar.gz.enc" \
    -out "${VERIFY_PATH}/payload.tar.gz" \
    -pass env:THESISAPPS_EXTERNAL_BACKUP_PASSPHRASE
tar -xzf "${VERIFY_PATH}/payload.tar.gz" -C "$VERIFY_PATH"

[[ "$(sha256_file "${VERIFY_PATH}/source-code.tar.gz")" == "$SOURCE_HASH" ]] \
    || fail 'Verified source archive checksum does not match.'
[[ "$(sha256_file "${VERIFY_PATH}/production.sql.gz")" == "$DATABASE_HASH" ]] \
    || fail 'Verified database archive checksum does not match.'
[[ "$(sha256_file "${VERIFY_PATH}/official-assets.tar.gz")" == "$ASSET_HASH" ]] \
    || fail 'Verified official asset archive checksum does not match.'

chmod 600 "${BACKUP_PATH}/payload.tar.gz.enc" "${BACKUP_PATH}/payload.tar.gz.enc.sha256"
printf 'EXTERNAL BACKUP SUCCESS: %s\n' "$BACKUP_PATH"
printf 'Verified encrypted archive and all scoped component checksums.\n'
