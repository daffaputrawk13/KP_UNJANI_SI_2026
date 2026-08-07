#!/usr/bin/env bash
set -e

# Tanpa Railway Volume, file ini ada di filesystem ephemeral — akan
# reset ke kosong setiap kali aplikasi di-redeploy. Cukup untuk demo,
# bukan untuk pemakaian data jangka panjang.
DB_PATH="${DB_DATABASE:-/app/database/database.sqlite}"
mkdir -p "$(dirname "$DB_PATH")"
touch "$DB_PATH"

php artisan config:clear
php artisan migrate --force
php artisan storage:link || true

php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
