#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
FIXTURE_ROOT=$(mktemp -d)
trap 'rm -rf "$FIXTURE_ROOT"' EXIT

WORK_PATH="${FIXTURE_ROOT}/work"
REMOTE_PATH="${FIXTURE_ROOT}/remote.git"
BIN_PATH="${FIXTURE_ROOT}/bin"
CURL_LOG="${FIXTURE_ROOT}/curl.log"

mkdir -p "${WORK_PATH}/scripts" "$BIN_PATH"
cp "${PROJECT_ROOT}/scripts/deploy-production.sh" "${WORK_PATH}/scripts/deploy-production.sh"
chmod +x "${WORK_PATH}/scripts/deploy-production.sh"

git init -q --bare "$REMOTE_PATH"
git -C "$WORK_PATH" init -q
git -C "$WORK_PATH" config user.name 'Deploy Test'
git -C "$WORK_PATH" config user.email 'deploy-test@example.invalid'
git -C "$WORK_PATH" add scripts/deploy-production.sh
git -C "$WORK_PATH" commit -qm fixture
git -C "$WORK_PATH" branch -M main
git -C "$WORK_PATH" remote add origin "$REMOTE_PATH"
git -C "$WORK_PATH" push -qu -u origin main
TEST_SHA=$(git -C "$WORK_PATH" rev-parse HEAD)

cat > "${BIN_PATH}/curl" <<'EOF'
#!/usr/bin/env bash
set -Eeuo pipefail

AUTH_CONFIG=$(cat)
[[ "$AUTH_CONFIG" == *"dummy_cpanel_token_1234567890"* ]]
[[ "$*" != *"dummy_cpanel_token_1234567890"* ]]
printf '%s\n' "$*" >> "$TEST_CURL_LOG"

case "$*" in
    *'/execute/VersionControl/update'*)
        printf '{"status":1,"data":{},"errors":null}\n'
        ;;
    *'/execute/Fileman/save_file_content'*)
        printf '{"status":1,"data":{"path":"approved"},"errors":null}\n'
        ;;
    *'/execute/VersionControlDeployment/create'*)
        printf '{"status":1,"data":{"deploy_id":12},"errors":null}\n'
        ;;
    *'/execute/VersionControlDeployment/retrieve'*)
        printf '{"status":1,"data":[{"deploy_id":12,"repository_state":{"identifier":"%s"},"timestamps":{"succeeded":1}}],"errors":null}\n' "$TEST_SHA"
        ;;
    *'/execute/VersionControl/retrieve'*)
        printf '{"status":1,"data":[{"repository_root":"/home/thesisapp/repositories/thesisapp","last_update":{"identifier":"%s"},"last_deployment":{"repository_state":{"identifier":"%s"},"timestamps":{"succeeded":1}}}],"errors":null}\n' "$TEST_SHA" "$TEST_SHA"
        ;;
    *)
        exit 31
        ;;
esac
EOF
chmod +x "${BIN_PATH}/curl"

export TEST_CURL_LOG="$CURL_LOG"
export TEST_SHA
export THESISAPPS_CPANEL_API_TOKEN=dummy_cpanel_token_1234567890
export PATH="${BIN_PATH}:${PATH}"

(cd "$WORK_PATH" && bash scripts/deploy-production.sh --dry-run)
grep -Fq '/execute/VersionControl/retrieve' "$CURL_LOG"
! grep -Fq '/execute/VersionControl/update' "$CURL_LOG"

: > "$CURL_LOG"
NOOP_OUTPUT=$(cd "$WORK_PATH" && bash scripts/deploy-production.sh)
grep -Fq 'DEPLOY NOT NEEDED' <<< "$NOOP_OUTPUT"
! grep -Fq '/execute/VersionControl/update' "$CURL_LOG"

: > "$CURL_LOG"
(cd "$WORK_PATH" && bash scripts/deploy-production.sh --force)
grep -Fq '/execute/VersionControl/update' "$CURL_LOG"
grep -Fq '/execute/Fileman/save_file_content' "$CURL_LOG"
grep -Fq '/execute/VersionControlDeployment/create' "$CURL_LOG"
grep -Fq '/execute/VersionControlDeployment/retrieve' "$CURL_LOG"
! grep -Fq 'dummy_cpanel_token_1234567890' "$CURL_LOG"

printf 'dirty\n' > "${WORK_PATH}/dirty.txt"
set +e
(cd "$WORK_PATH" && bash scripts/deploy-production.sh --dry-run >/dev/null 2>&1)
DIRTY_STATUS=$?
set -e
[[ "$DIRTY_STATUS" -ne 0 ]]

printf 'cPanel API deployment test passed.\n'
