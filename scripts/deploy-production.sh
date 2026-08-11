#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
CPANEL_HOST="${THESISAPPS_CPANEL_HOST:-https://cpanel.thesis.fikom.app}"
CPANEL_USER="${THESISAPPS_CPANEL_USER:-thesisapp}"
CPANEL_TOKEN_SERVICE="${THESISAPPS_CPANEL_TOKEN_SERVICE:-thesisapps-cpanel-api}"
DEPLOY_BRANCH="${THESISAPPS_DEPLOY_BRANCH:-main}"
REMOTE_REPOSITORY="${THESISAPPS_REMOTE_REPOSITORY:-/home/thesisapp/repositories/thesisapp}"
REMOTE_SHARED="${THESISAPPS_REMOTE_SHARED:-/home/thesisapp/shared/thesisapps}"
DEPLOY_TIMEOUT="${THESISAPPS_DEPLOY_TIMEOUT:-300}"
POLL_INTERVAL="${THESISAPPS_DEPLOY_POLL_INTERVAL:-4}"
DRY_RUN=0
FORCE=0
API_RESPONSE=
API_ERROR=

usage() {
    cat <<'EOF'
Usage:
  scripts/deploy-production.sh [--dry-run] [--force]

Deploys the exact local origin/main commit through the cPanel UAPI without
opening cPanel in a browser. The cPanel API token is read from the macOS
Keychain service "thesisapps-cpanel-api", or from the temporary environment
variable THESISAPPS_CPANEL_API_TOKEN.

Options:
  --dry-run  Verify Git state, cPanel authentication, and remote deployment state.
  --force    Redeploy even when the exact commit is already live.
EOF
}

fail() {
    printf 'PRODUCTION DEPLOY BLOCKED: %s\n' "$1" >&2
    exit 1
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)
            DRY_RUN=1
            shift
            ;;
        --force)
            FORCE=1
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

for command_name in curl git jq; do
    command -v "$command_name" >/dev/null 2>&1 \
        || fail "Required command is unavailable: ${command_name}"
done

[[ "$CPANEL_HOST" =~ ^https://[A-Za-z0-9.-]+(:[0-9]+)?$ ]] \
    || fail 'Unsafe cPanel host URL.'
[[ "$CPANEL_USER" =~ ^[A-Za-z0-9._-]+$ ]] || fail 'Unsafe cPanel username.'
[[ "$DEPLOY_BRANCH" =~ ^[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe deployment branch.'
[[ "$REMOTE_REPOSITORY" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote repository path.'
[[ "$REMOTE_SHARED" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote shared path.'
[[ "$DEPLOY_TIMEOUT" =~ ^[1-9][0-9]*$ ]] || fail 'Unsafe deployment timeout.'
[[ "$POLL_INTERVAL" =~ ^[1-9][0-9]*$ ]] || fail 'Unsafe deployment poll interval.'

if [[ -n "${THESISAPPS_CPANEL_API_TOKEN:-}" ]]; then
    CPANEL_TOKEN=$THESISAPPS_CPANEL_API_TOKEN
else
    command -v security >/dev/null 2>&1 \
        || fail 'macOS Keychain is unavailable and THESISAPPS_CPANEL_API_TOKEN is not set.'
    CPANEL_TOKEN=$(security find-generic-password \
        -a "$CPANEL_USER" \
        -s "$CPANEL_TOKEN_SERVICE" \
        -w 2>/dev/null) \
        || fail "cPanel API token is missing from Keychain service ${CPANEL_TOKEN_SERVICE}."
fi

[[ ${#CPANEL_TOKEN} -ge 16 && ${#CPANEL_TOKEN} -le 512 ]] \
    || fail 'The cPanel API token has an unexpected length.'
case "$CPANEL_TOKEN" in
    *[!A-Za-z0-9_-]*) fail 'The cPanel API token has an unexpected format.' ;;
esac

try_api_call() {
    local endpoint=$1
    shift
    API_ERROR=

    if ! API_RESPONSE=$(
        printf 'header = "Authorization: cpanel %s:%s"\n' "$CPANEL_USER" "$CPANEL_TOKEN" \
            | curl \
                -4 \
                --config - \
                --get \
                --retry 6 \
                --retry-all-errors \
                --retry-delay 2 \
                --retry-max-time 75 \
                --connect-timeout 7 \
                --max-time 20 \
                --fail-with-body \
                --silent \
                --show-error \
                "${CPANEL_HOST}/execute/${endpoint}" \
                "$@"
    ); then
        API_ERROR="cPanel API request failed: ${endpoint}. Check the network connection and token validity."
        return 1
    fi

    printf '%s' "$API_RESPONSE" | jq -e . >/dev/null 2>&1 \
        || { API_ERROR="cPanel returned an invalid response for ${endpoint}."; return 1; }

    local status
    local errors
    status=$(printf '%s' "$API_RESPONSE" | jq -r '(.result? // .).status // 0')
    if [[ "$status" != 1 ]]; then
        errors=$(printf '%s' "$API_RESPONSE" \
            | jq -r '((.result? // .).errors // ["unknown cPanel error"]) | if type == "array" then join("; ") else tostring end')
        API_ERROR="cPanel rejected ${endpoint}: ${errors}"
        return 2
    fi
}

api_call() {
    if try_api_call "$@"; then
        return
    fi

    fail "$API_ERROR"
}

repository_state() {
    api_call VersionControl/retrieve

    SERVER_SHA=$(printf '%s' "$API_RESPONSE" | jq -r --arg repository "$REMOTE_REPOSITORY" '
        ((.result? // .).data // [])[]
        | select(.repository_root == $repository)
        | .last_update.identifier // empty
    ')
    LAST_DEPLOY_SHA=$(printf '%s' "$API_RESPONSE" | jq -r --arg repository "$REMOTE_REPOSITORY" '
        ((.result? // .).data // [])[]
        | select(.repository_root == $repository)
        | .last_deployment.repository_state.identifier // empty
    ')
    LAST_DEPLOY_SUCCEEDED=$(printf '%s' "$API_RESPONSE" | jq -r --arg repository "$REMOTE_REPOSITORY" '
        ((.result? // .).data // [])[]
        | select(.repository_root == $repository)
        | .last_deployment.timestamps.succeeded // empty
    ')

    [[ -n "$SERVER_SHA" ]] \
        || fail "cPanel repository was not found: ${REMOTE_REPOSITORY}"
}

cd "$PROJECT_ROOT"
[[ -z "$(git status --porcelain)" ]] || fail 'The local worktree is not clean.'
[[ "$(git branch --show-current)" == "$DEPLOY_BRANCH" ]] \
    || fail "The checked-out branch must be ${DEPLOY_BRANCH}."

REMOTE_TRACKING_REF="refs/remotes/origin/${DEPLOY_BRANCH}"
git fetch origin "refs/heads/${DEPLOY_BRANCH}:${REMOTE_TRACKING_REF}"
TARGET_SHA=$(git rev-parse HEAD)
ORIGIN_SHA=$(git rev-parse "$REMOTE_TRACKING_REF")
[[ "$TARGET_SHA" == "$ORIGIN_SHA" ]] \
    || fail "Local HEAD ${TARGET_SHA} does not match origin/${DEPLOY_BRANCH} ${ORIGIN_SHA}. Commit and push first."

printf 'Target commit: %s\n' "$TARGET_SHA"
repository_state
printf 'cPanel repository commit: %s\n' "$SERVER_SHA"
printf 'Last successful deployment: %s\n' "${LAST_DEPLOY_SHA:-<none>}"

if [[ "$DRY_RUN" -eq 1 ]]; then
    printf 'DRY RUN PASS: Git, Keychain token, cPanel API, and repository state are valid.\n'
    exit 0
fi

if [[ "$FORCE" -eq 0 && "$LAST_DEPLOY_SHA" == "$TARGET_SHA" && -n "$LAST_DEPLOY_SUCCEEDED" ]]; then
    printf 'DEPLOY NOT NEEDED: production already runs %s.\n' "$TARGET_SHA"
    exit 0
fi

printf 'Updating the cPanel repository from origin/%s.\n' "$DEPLOY_BRANCH"
api_call VersionControl/update \
    --data-urlencode "repository_root=${REMOTE_REPOSITORY}" \
    --data-urlencode "branch=${DEPLOY_BRANCH}"

repository_state
[[ "$SERVER_SHA" == "$TARGET_SHA" ]] \
    || fail "cPanel repository is at ${SERVER_SHA}, expected ${TARGET_SHA}."

printf 'Approving the exact deployment commit.\n'
api_call Fileman/save_file_content \
    --data-urlencode "dir=${REMOTE_SHARED}" \
    --data-urlencode 'file=deploy-approved-commit' \
    --data-urlencode "content=${TARGET_SHA}" \
    --data-urlencode 'from_charset=UTF-8' \
    --data-urlencode 'to_charset=UTF-8' \
    --data-urlencode 'fallback=0'

printf 'Starting guarded production deployment.\n'
api_call VersionControlDeployment/create \
    --data-urlencode "repository_root=${REMOTE_REPOSITORY}"

DEPLOY_ID=$(printf '%s' "$API_RESPONSE" | jq -r '
    (.result? // .) as $response
    | $response.data.deploy_id // $response.data[0].deploy_id // empty
')
[[ "$DEPLOY_ID" =~ ^[0-9]+$ ]] || fail 'cPanel did not return a valid deployment id.'

STARTED_AT=$(date +%s)
while true; do
    if try_api_call VersionControlDeployment/retrieve; then
        :
    else
        API_CALL_STATUS=$?
        [[ "$API_CALL_STATUS" -ne 2 ]] || fail "$API_ERROR"
        NOW=$(date +%s)
        (( NOW - STARTED_AT < DEPLOY_TIMEOUT )) \
            || fail "Timed out waiting for cPanel deployment ${DEPLOY_ID}: ${API_ERROR}"
        printf 'Deployment is still pending; cPanel status is temporarily unavailable. Retrying.\n'
        sleep "$POLL_INTERVAL"
        continue
    fi

    DEPLOY_ITEM=$(printf '%s' "$API_RESPONSE" | jq -c --arg deploy_id "$DEPLOY_ID" '
        [((.result? // .).data // [])[] | select((.deploy_id | tostring) == $deploy_id)][0] // {}
    ')
    SUCCEEDED_AT=$(printf '%s' "$DEPLOY_ITEM" | jq -r '.timestamps.succeeded // empty')
    FAILED_AT=$(printf '%s' "$DEPLOY_ITEM" | jq -r '.timestamps.failed // empty')
    DEPLOYED_SHA=$(printf '%s' "$DEPLOY_ITEM" | jq -r '.repository_state.identifier // empty')

    if [[ -n "$SUCCEEDED_AT" ]]; then
        [[ "$DEPLOYED_SHA" == "$TARGET_SHA" ]] \
            || fail "Deployment succeeded for ${DEPLOYED_SHA}, expected ${TARGET_SHA}."
        printf 'DEPLOY SUCCESS: production is running %s.\n' "$TARGET_SHA"
        break
    fi

    [[ -z "$FAILED_AT" ]] || fail "cPanel deployment ${DEPLOY_ID} failed."

    NOW=$(date +%s)
    (( NOW - STARTED_AT < DEPLOY_TIMEOUT )) \
        || fail "Timed out waiting for cPanel deployment ${DEPLOY_ID}."

    sleep "$POLL_INTERVAL"
done

CPANEL_TOKEN=
unset CPANEL_TOKEN THESISAPPS_CPANEL_API_TOKEN
