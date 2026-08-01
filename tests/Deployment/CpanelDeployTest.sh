#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
FIXTURE_ROOT=$(mktemp -d)
trap 'rm -rf "$FIXTURE_ROOT"' EXIT

APP_PATH="${FIXTURE_ROOT}/repository"
DEPLOY_PATH="${FIXTURE_ROOT}/public_html"
SHARED_PATH="${FIXTURE_ROOT}/shared"
BIN_PATH="${FIXTURE_ROOT}/bin"

mkdir -p \
    "${APP_PATH}/app" \
    "${APP_PATH}/bootstrap" \
    "${APP_PATH}/scripts" \
    "${APP_PATH}/public" \
    "${DEPLOY_PATH}/app" \
    "${DEPLOY_PATH}/bootstrap/cache" \
    "${DEPLOY_PATH}/storage/app/public" \
    "${DEPLOY_PATH}/storage/framework" \
    "${DEPLOY_PATH}/public/gambar" \
    "${DEPLOY_PATH}/public/dokumen" \
    "${DEPLOY_PATH}/public/public/dokumen" \
    "${DEPLOY_PATH}/public/mobile/controller/simta/uploadedFiles" \
    "${SHARED_PATH}/official-assets" \
    "$BIN_PATH"

cp "${PROJECT_ROOT}/scripts/deploy-cpanel.sh" "${APP_PATH}/scripts/deploy-cpanel.sh"
cp "${PROJECT_ROOT}/scripts/deploy-excludes.txt" "${APP_PATH}/scripts/deploy-excludes.txt"
cp "${PROJECT_ROOT}/scripts/sync-release.php" "${APP_PATH}/scripts/sync-release.php"
cp "${PROJECT_ROOT}/scripts/normalize-composer-installed.php" "${APP_PATH}/scripts/normalize-composer-installed.php"

printf 'new source\n' > "${APP_PATH}/app/version.php"
printf '<?php return [];\n' > "${APP_PATH}/bootstrap/app.php"
printf '%s\n' \
    '#!/usr/bin/env php' \
    '<?php' \
    '$command = $argv[1] ?? "";' \
    'if ($command === "down") {' \
    '    @mkdir("storage/framework", 0777, true);' \
    '    touch("storage/framework/down");' \
    '} elseif ($command === "up") {' \
    '    @unlink("storage/framework/down");' \
    '} elseif ($command === "package:discover" && getenv("FAIL_DISCOVER") === "1") {' \
    '    exit(42);' \
    '}' \
    > "${APP_PATH}/artisan"
cp "${APP_PATH}/artisan" "${DEPLOY_PATH}/artisan"
printf '{}\n' > "${APP_PATH}/composer.lock"
printf 'old source\n' > "${DEPLOY_PATH}/app/version.php"
printf 'remove me\n' > "${DEPLOY_PATH}/app/obsolete.php"
printf 'APP_ENV=production\nOFFICIAL_ASSET_PATH=%s/official-assets\n' "$SHARED_PATH" > "${DEPLOY_PATH}/.env"
printf 'user upload\n' > "${DEPLOY_PATH}/public/gambar/user-upload.png"
printf 'user document\n' > "${DEPLOY_PATH}/public/dokumen/user-document.pdf"
printf 'nested user document\n' > "${DEPLOY_PATH}/public/public/dokumen/nested-user-document.pdf"
printf 'mobile user upload\n' > "${DEPLOY_PATH}/public/mobile/controller/simta/uploadedFiles/mobile-user-upload.pdf"
printf 'runtime state\n' > "${DEPLOY_PATH}/storage/runtime.txt"
printf 'server archive\n' > "${DEPLOY_PATH}/public/public_html.zip"

for index in 1 2 3 4 5 6 7; do
    printf 'official %s\n' "$index" > "${SHARED_PATH}/official-assets/official-${index}.png"
done

printf '%s\n' \
    '#!/usr/bin/env bash' \
    'set -e' \
    'mkdir -p vendor/composer' \
    "printf 'fixture vendor\\n' > vendor/autoload.php" \
    "printf '%s\\n' '{\"packages\":[{\"name\":\"fixture/package\",\"extra\":{\"laravel\":[]}}],\"dev\":false}' > vendor/composer/installed.json" \
    > "${BIN_PATH}/composer"

chmod +x "${BIN_PATH}/composer" "${APP_PATH}/artisan"

git -C "$APP_PATH" init -q
git -C "$APP_PATH" add .
git -C "$APP_PATH" -c user.name='Deploy Test' -c user.email='deploy-test@example.invalid' commit -qm fixture
COMMIT_SHA=$(git -C "$APP_PATH" rev-parse HEAD)
printf 'not-the-approved-commit\n' > "${SHARED_PATH}/deploy-approved-commit"

set +e
APP_PATH="$APP_PATH" \
DEPLOY_PATH="$DEPLOY_PATH" \
SHARED_PATH="$SHARED_PATH" \
OFFICIAL_PATH="${SHARED_PATH}/official-assets" \
PHP_BIN="$(command -v php)" \
COMPOSER_BIN="${BIN_PATH}/composer" \
bash "${PROJECT_ROOT}/scripts/deploy-cpanel.sh"
UNAPPROVED_STATUS=$?
set -e

[[ "$UNAPPROVED_STATUS" -ne 0 ]]
assert_unapproved_source=$(sed -n '1p' "${DEPLOY_PATH}/app/version.php")
[[ "$assert_unapproved_source" == 'old source' ]]

printf '%s\n' "$COMMIT_SHA" > "${SHARED_PATH}/deploy-approved-commit"

APP_PATH="$APP_PATH" \
DEPLOY_PATH="$DEPLOY_PATH" \
SHARED_PATH="$SHARED_PATH" \
OFFICIAL_PATH="${SHARED_PATH}/official-assets" \
PHP_BIN="$(command -v php)" \
COMPOSER_BIN="${BIN_PATH}/composer" \
bash "${PROJECT_ROOT}/scripts/deploy-cpanel.sh"

assert_line() {
    local expected=$1
    local file=$2

    if ! grep -Fqx "$expected" "$file"; then
        printf 'Expected "%s" in %s; actual content:\n' "$expected" "$file" >&2
        sed -n '1,20p' "$file" >&2
        exit 1
    fi
}

assert_line 'new source' "${DEPLOY_PATH}/app/version.php"
[[ ! -e "${DEPLOY_PATH}/app/obsolete.php" ]]
assert_line 'user upload' "${DEPLOY_PATH}/public/gambar/user-upload.png"
assert_line 'user document' "${DEPLOY_PATH}/public/dokumen/user-document.pdf"
assert_line 'nested user document' "${DEPLOY_PATH}/public/public/dokumen/nested-user-document.pdf"
assert_line 'mobile user upload' "${DEPLOY_PATH}/public/mobile/controller/simta/uploadedFiles/mobile-user-upload.pdf"
assert_line 'runtime state' "${DEPLOY_PATH}/storage/runtime.txt"
assert_line 'server archive' "${DEPLOY_PATH}/public/public_html.zip"
assert_line "OFFICIAL_ASSET_PATH=${SHARED_PATH}/official-assets" "${DEPLOY_PATH}/.env"
[[ -L "${DEPLOY_PATH}/public/storage" ]]
[[ ! -e "${DEPLOY_PATH}/storage/framework/down" ]]
[[ ! -e "${SHARED_PATH}/deploy-approved-commit" ]]
php -r '$installed = json_decode(file_get_contents($argv[1]), true); exit(isset($installed[0]["name"]) && $installed[0]["name"] === "fixture/package" ? 0 : 1);' \
    "${APP_PATH}/vendor/composer/installed.json"
assert_line 'old source' "${SHARED_PATH}"/deploy-backups/*/app/version.php
[[ -z "$(find "${SHARED_PATH}/deploy-backups" -type f -name '*.zip' -print -quit)" ]]

printf 'broken source\n' > "${APP_PATH}/app/version.php"
git -C "$APP_PATH" add app/version.php
git -C "$APP_PATH" -c user.name='Deploy Test' -c user.email='deploy-test@example.invalid' commit -qm broken-fixture
FAILED_COMMIT_SHA=$(git -C "$APP_PATH" rev-parse HEAD)
printf '%s\n' "$FAILED_COMMIT_SHA" > "${SHARED_PATH}/deploy-approved-commit"

set +e
FAIL_DISCOVER=1 \
APP_PATH="$APP_PATH" \
DEPLOY_PATH="$DEPLOY_PATH" \
SHARED_PATH="$SHARED_PATH" \
OFFICIAL_PATH="${SHARED_PATH}/official-assets" \
PHP_BIN="$(command -v php)" \
COMPOSER_BIN="${BIN_PATH}/composer" \
bash "${PROJECT_ROOT}/scripts/deploy-cpanel.sh"
FAILED_DEPLOY_STATUS=$?
set -e

[[ "$FAILED_DEPLOY_STATUS" -eq 42 ]]
assert_line 'new source' "${DEPLOY_PATH}/app/version.php"
assert_line 'user upload' "${DEPLOY_PATH}/public/gambar/user-upload.png"
assert_line 'user document' "${DEPLOY_PATH}/public/dokumen/user-document.pdf"
assert_line 'nested user document' "${DEPLOY_PATH}/public/public/dokumen/nested-user-document.pdf"
assert_line 'mobile user upload' "${DEPLOY_PATH}/public/mobile/controller/simta/uploadedFiles/mobile-user-upload.pdf"
assert_line 'runtime state' "${DEPLOY_PATH}/storage/runtime.txt"
assert_line 'server archive' "${DEPLOY_PATH}/public/public_html.zip"
[[ ! -e "${DEPLOY_PATH}/storage/framework/down" ]]
assert_line "$FAILED_COMMIT_SHA" "${SHARED_PATH}/deploy-approved-commit"

MALICIOUS_SOURCE="${FIXTURE_ROOT}/malicious-source"
MALICIOUS_TARGET="${FIXTURE_ROOT}/malicious-target"
mkdir -p "$MALICIOUS_SOURCE" "$MALICIOUS_TARGET"
ln -s ../../outside "$MALICIOUS_SOURCE/escape"

set +e
php "${PROJECT_ROOT}/scripts/sync-release.php" \
    "$MALICIOUS_SOURCE" \
    "$MALICIOUS_TARGET" \
    "${PROJECT_ROOT}/scripts/deploy-excludes.txt" \
    /dev/null \
    "${FIXTURE_ROOT}/malicious-manifest.json"
MALICIOUS_LINK_STATUS=$?
set -e

[[ "$MALICIOUS_LINK_STATUS" -ne 0 ]]
[[ ! -e "${MALICIOUS_TARGET}/escape" ]]

printf 'CPanel deployment safety test passed.\n'
