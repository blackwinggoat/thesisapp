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
    'command_name=$1' \
    'field=${2:-}' \
    'environment=${3:-}' \
    '[[ "$command_name" == metadata ]]' \
    '[[ "$environment" == local ]]' \
    'case "$field" in' \
    '  database) echo thesisapps_fixture_local ;;' \
    '  host) echo 127.0.0.1 ;;' \
    '  *) exit 9 ;;' \
    'esac' \
    > "${BIN_PATH}/helper"

printf '%s\n' \
    '#!/usr/bin/env bash' \
    'printf "%s\n" thesisapps_fixture_production' \
    > "${BIN_PATH}/ssh"

printf '%s\n' \
    '#!/usr/bin/env bash' \
    'printf "Unexpected executable call: %s\n" "$0" >&2' \
    'exit 97' \
    > "${BIN_PATH}/unexpected"

chmod +x "${BIN_PATH}/helper" "${BIN_PATH}/ssh" "${BIN_PATH}/unexpected"

OUTPUT=$(
    PHP_BIN="$(command -v bash)" \
    MYSQL_BIN="${BIN_PATH}/unexpected" \
    MYSQLDUMP_BIN="${BIN_PATH}/unexpected" \
    GZIP_BIN="${BIN_PATH}/unexpected" \
    SHA256_BIN="${BIN_PATH}/unexpected" \
    SSH_BIN="${BIN_PATH}/ssh" \
    SCP_BIN="${BIN_PATH}/unexpected" \
    DATABASE_SYNC_HELPER="${BIN_PATH}/helper" \
    bash "${PROJECT_ROOT}/scripts/sync-production-db-to-local.sh" --dry-run
)

grep -Fqx 'Local target: thesisapps_fixture_local@127.0.0.1' <<< "$OUTPUT"
grep -Fqx 'Production source: thesisapps_fixture_production via thesisapps-production' <<< "$OUTPUT"
grep -Fqx 'Dry run complete; no dump was created and no database was changed.' <<< "$OUTPUT"

set +e
bash "${PROJECT_ROOT}/scripts/sync-production-db-to-local.sh" >/dev/null 2>&1
MISSING_MODE_STATUS=$?
set -e
[[ "$MISSING_MODE_STATUS" -ne 0 ]]

printf 'Production database sync safety test passed.\n'
