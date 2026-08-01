#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
FIXTURE_ROOT=$(mktemp -d)
trap 'rm -rf "$FIXTURE_ROOT"' EXIT

ORIGIN_PATH="${FIXTURE_ROOT}/origin.git"
SOURCE_PATH="${FIXTURE_ROOT}/source"
CHECKOUT_PATH="${FIXTURE_ROOT}/checkout"

git init -q --bare "$ORIGIN_PATH"
git -C "$ORIGIN_PATH" symbolic-ref HEAD refs/heads/main
git init -q -b main "$SOURCE_PATH"
mkdir -p "${SOURCE_PATH}/scripts"
cp "${PROJECT_ROOT}/scripts/deploy-production-ssh.sh" "${SOURCE_PATH}/scripts/"
printf 'fixture\n' > "${SOURCE_PATH}/README.md"
git -C "$SOURCE_PATH" add .
git -C "$SOURCE_PATH" -c user.name='Deploy Test' -c user.email='deploy-test@example.invalid' \
    commit -qm fixture
git -C "$SOURCE_PATH" remote add origin "$ORIGIN_PATH"
git -C "$SOURCE_PATH" push -q -u origin main

git clone -q "$ORIGIN_PATH" "$CHECKOUT_PATH"
git -C "$CHECKOUT_PATH" update-ref -d refs/remotes/origin/main
git -C "$CHECKOUT_PATH" config --unset-all remote.origin.fetch
git -C "$CHECKOUT_PATH" config --add remote.origin.fetch \
    '+refs/heads/chore/unused:refs/remotes/origin/chore/unused'

OUTPUT=$(cd "$CHECKOUT_PATH" && bash scripts/deploy-production-ssh.sh --dry-run)
EXPECTED_SHA=$(git -C "$SOURCE_PATH" rev-parse HEAD)
ACTUAL_SHA=$(git -C "$CHECKOUT_PATH" rev-parse refs/remotes/origin/main)

[[ "$ACTUAL_SHA" == "$EXPECTED_SHA" ]]
grep -Fqx "Approved commit: ${EXPECTED_SHA}" <<< "$OUTPUT"
grep -Fqx 'Dry run complete; no server command was executed.' <<< "$OUTPUT"

printf 'Production SSH deployment fetch test passed.\n'
