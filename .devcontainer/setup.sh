#!/usr/bin/env bash
set -euo pipefail

mkdir -p logs tmp tmp/cache/models tmp/cache/persistent tmp/cache/views tmp/sessions
chmod -R 775 logs tmp || true

if [ -f composer.json ]; then
  composer install || true
fi

for i in $(seq 1 40); do
  if mysqladmin ping -h"${DB_HOST:-db}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-cake}" -p"${DB_PASSWORD:-cake}" --silent; then
    break
  fi
  sleep 2
done

mysql -h"${DB_HOST:-db}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-cake}" -p"${DB_PASSWORD:-cake}" \
  -e "CREATE DATABASE IF NOT EXISTS \`${DB_TEST_DATABASE:-cakephp_test}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || true
