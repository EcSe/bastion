#!/usr/bin/env bash

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/bastion}"
CONSOLE_DIR="$APP_DIR/console"
CORE_DIR="$APP_DIR/core"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"

if [[ ! -d "$APP_DIR/.git" ]]; then
    echo "No se encontró repositorio git en $APP_DIR" >&2
    exit 1
fi

cd "$APP_DIR"
git fetch origin main
git checkout main
git pull --ff-only origin main

cd "$CORE_DIR"
"$NPM_BIN" ci
"$NPM_BIN" run build

cd "$CONSOLE_DIR"
"$COMPOSER_BIN" install --no-interaction --no-dev --prefer-dist --optimize-autoloader
"$PHP_BIN" artisan migrate --force --no-interaction
"$NPM_BIN" ci
"$NPM_BIN" run build
"$PHP_BIN" artisan optimize:clear
