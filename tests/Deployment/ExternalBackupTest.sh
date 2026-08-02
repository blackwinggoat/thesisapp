#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
FIXTURE_ROOT=$(mktemp -d)
trap 'rm -rf "$FIXTURE_ROOT"' EXIT

BIN_PATH="${FIXTURE_ROOT}/bin"
mkdir -p "$BIN_PATH"

printf '%s\n' \
    '#!/usr/bin/env bash' \
    'set -e' \
    'case "$*" in' \
    '  *"rev-parse HEAD"*) echo aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa ;;' \
    '  *"find "*) echo /srv/shared/database-sync/production-20260802T122453Z.sql.gz ;;' \
    '  *) exit 9 ;;' \
    'esac' \
    > "${BIN_PATH}/ssh"

chmod +x "${BIN_PATH}/ssh"

OUTPUT=$( \
    PATH="${BIN_PATH}:${PATH}" \
    THESISAPPS_REMOTE_REPOSITORY=/srv/repository \
    THESISAPPS_REMOTE_SHARED=/srv/shared \
    THESISAPPS_REMOTE_DB_BACKUPS=/srv/shared/database-sync \
    THESISAPPS_EXTERNAL_BACKUP_DESTINATION="${FIXTURE_ROOT}/external" \
    bash "${PROJECT_ROOT}/scripts/backup-production-source-db.sh" --dry-run
)

grep -Fqx 'Production commit: aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' <<< "$OUTPUT"
grep -Fqx 'Production database snapshot: production-20260802T122453Z.sql.gz' <<< "$OUTPUT"
grep -Fqx 'Scope: Git source, production database snapshot, and official assets only.' <<< "$OUTPUT"
grep -Fqx 'Excluded: user uploads, runtime storage, and .env.' <<< "$OUTPUT"
grep -Fqx 'Dry run complete; no external archive was created.' <<< "$OUTPUT"

printf 'External backup safety test passed.\n'
