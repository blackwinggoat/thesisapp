#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
SSH_HOST="${THESISAPPS_SSH_HOST:-thesisapps-production}"
DEPLOY_BRANCH="${THESISAPPS_DEPLOY_BRANCH:-main}"
REMOTE_REPOSITORY="${THESISAPPS_REMOTE_REPOSITORY:-/home/thesisapp/repositories/thesisapp}"
REMOTE_SHARED="${THESISAPPS_REMOTE_SHARED:-/home/thesisapp/shared/thesisapps}"
MODE=release
DRY_RUN=0
TARGET_SHA=

usage() {
    cat <<'EOF'
Usage:
  scripts/deploy-production-ssh.sh [--dry-run]
  scripts/deploy-production-ssh.sh --rollback COMMIT_SHA [--dry-run]

The normal mode deploys the exact origin/main commit. Rollback mode accepts
only a commit that is an ancestor of origin/main. Both modes still use the
server-side approval, backup, lock, manifest, and rollback safeguards.
EOF
}

fail() {
    printf 'REMOTE DEPLOY BLOCKED: %s\n' "$1" >&2
    exit 1
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)
            DRY_RUN=1
            shift
            ;;
        --rollback)
            [[ $# -ge 2 ]] || fail '--rollback requires a full commit SHA.'
            MODE=rollback
            TARGET_SHA=$2
            shift 2
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

[[ "$DEPLOY_BRANCH" =~ ^[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe deployment branch.'
[[ "$REMOTE_REPOSITORY" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote repository path.'
[[ "$REMOTE_SHARED" =~ ^/[A-Za-z0-9._/-]+$ ]] || fail 'Unsafe remote shared path.'

cd "$PROJECT_ROOT"
[[ -z "$(git status --porcelain)" ]] || fail 'The local worktree is not clean.'

REMOTE_TRACKING_REF="refs/remotes/origin/${DEPLOY_BRANCH}"
git fetch origin "refs/heads/${DEPLOY_BRANCH}:${REMOTE_TRACKING_REF}"
RELEASE_SHA=$(git rev-parse "$REMOTE_TRACKING_REF")

if [[ "$MODE" == release ]]; then
    TARGET_SHA=$RELEASE_SHA
else
    [[ "$TARGET_SHA" =~ ^[0-9a-f]{40}$ ]] || fail 'Rollback requires a full 40-character commit SHA.'
    git cat-file -e "${TARGET_SHA}^{commit}" 2>/dev/null || fail 'Rollback commit is unavailable locally.'
    git merge-base --is-ancestor "$TARGET_SHA" "$RELEASE_SHA" \
        || fail 'Rollback commit is not an ancestor of the release branch.'
fi

printf 'SSH host: %s\n' "$SSH_HOST"
printf 'Mode: %s\n' "$MODE"
printf 'Approved commit: %s\n' "$TARGET_SHA"

if [[ "$DRY_RUN" -eq 1 ]]; then
    printf 'Dry run complete; no server command was executed.\n'
    exit 0
fi

ssh "$SSH_HOST" \
    "bash -s -- '${TARGET_SHA}' '${DEPLOY_BRANCH}' '${MODE}' '${REMOTE_REPOSITORY}' '${REMOTE_SHARED}'" <<'REMOTE_SCRIPT'
set -Eeuo pipefail
umask 077

TARGET_SHA=$1
DEPLOY_BRANCH=$2
MODE=$3
REMOTE_REPOSITORY=$4
REMOTE_SHARED=$5
APPROVAL_FILE="${REMOTE_SHARED}/deploy-approved-commit"
REMOTE_TRACKING_REF="refs/remotes/origin/${DEPLOY_BRANCH}"

[[ ! -e "${REMOTE_SHARED}/deploy.lock" ]] \
    || { printf 'REMOTE DEPLOY BLOCKED: deployment lock exists.\n' >&2; exit 1; }

git -C "$REMOTE_REPOSITORY" fetch --depth=100 origin \
    "refs/heads/${DEPLOY_BRANCH}:${REMOTE_TRACKING_REF}"
RELEASE_SHA=$(git -C "$REMOTE_REPOSITORY" rev-parse "$REMOTE_TRACKING_REF")

if [[ "$MODE" == release ]]; then
    [[ "$TARGET_SHA" == "$RELEASE_SHA" ]] \
        || { printf 'REMOTE DEPLOY BLOCKED: origin branch changed.\n' >&2; exit 1; }
    git -C "$REMOTE_REPOSITORY" checkout -B "$DEPLOY_BRANCH" "$REMOTE_TRACKING_REF"
else
    git -C "$REMOTE_REPOSITORY" cat-file -e "${TARGET_SHA}^{commit}"
    git -C "$REMOTE_REPOSITORY" merge-base --is-ancestor "$TARGET_SHA" "$RELEASE_SHA"
    git -C "$REMOTE_REPOSITORY" checkout --detach "$TARGET_SHA"
fi

[[ "$(git -C "$REMOTE_REPOSITORY" rev-parse HEAD)" == "$TARGET_SHA" ]]
[[ -z "$(git -C "$REMOTE_REPOSITORY" status --porcelain --untracked-files=no)" ]] \
    || { printf 'REMOTE DEPLOY BLOCKED: tracked checkout changes detected.\n' >&2; exit 1; }

printf '%s\n' "$TARGET_SHA" > "$APPROVAL_FILE"
chmod 600 "$APPROVAL_FILE"
bash "${REMOTE_REPOSITORY}/scripts/deploy-cpanel.sh"
REMOTE_SCRIPT
