#!/usr/bin/env bash
set -euo pipefail

echo "==> Starting devcontainer setup"

echo "==> Preparing writable directories"
mkdir -p logs tmp tmp/cache/models tmp/cache/persistent tmp/cache/views tmp/sessions
chmod -R 775 logs tmp || true

if [ -f composer.json ]; then
  echo "==> Installing Composer dependencies"
  composer install --no-interaction
fi

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USERNAME="${DB_USERNAME:-cake}"
DB_PASSWORD="${DB_PASSWORD:-cake}"
DB_DATABASE="${DB_DATABASE:-cakephp_app}"
DB_TEST_DATABASE="${DB_TEST_DATABASE:-cakephp_test}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-root}"

SCHEMA_FILE="config/schema/novel_factory_cakephp5_schema.sql"

mysql_cmd() {
  mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "$@"
}

root_mysql_cmd() {
  mysql -h"${DB_HOST}" -P"${DB_PORT}" -uroot -p"${DB_ROOT_PASSWORD}" "$@"
}

import_schema() {
  local database_name="$1"

  # 'characters' only comes from the SQL schema, so use it as the import marker.
  local marker_count
  marker_count=$(mysql_cmd -sse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${database_name}' AND table_name='characters';" 2>/dev/null || echo "0")

  if [ "${marker_count}" != "0" ]; then
    echo "==> ${database_name} already has the novel schema, skipping import"
    return 0
  fi

  if [ ! -f "${SCHEMA_FILE}" ]; then
    echo "==> Schema file ${SCHEMA_FILE} not found, skipping import"
    return 0
  fi

  echo "==> Importing ${SCHEMA_FILE} into ${database_name}"
  # Strip CREATE DATABASE (multi-line) and USE so the import targets the requested
  # database, and use IF NOT EXISTS so migration-managed tables are left untouched.
  sed -E '/^[[:space:]]*CREATE DATABASE/I,/;[[:space:]]*$/d; /^[[:space:]]*USE /Id; s/^CREATE TABLE /CREATE TABLE IF NOT EXISTS /' "${SCHEMA_FILE}" | mysql_cmd "${database_name}"
  echo "==> Schema imported into ${database_name}"
}

echo "==> Waiting for MySQL at ${DB_HOST}:${DB_PORT}"
for i in $(seq 1 40); do
  if mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; then
    break
  fi
  sleep 2
done

echo "==> Ensuring test database exists and is accessible"
root_mysql_cmd -e "CREATE DATABASE IF NOT EXISTS \`${DB_TEST_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${DB_TEST_DATABASE}\`.* TO '${DB_USERNAME}'@'%';
FLUSH PRIVILEGES;"

echo "==> Running migrations (users and core tables)"
bin/cake migrations migrate --no-lock
bin/cake migrations migrate --no-lock --connection test

import_schema "${DB_DATABASE}"
import_schema "${DB_TEST_DATABASE}"

echo "==> Setup complete"
echo "Run:"
echo "  bin/cake server -H 0.0.0.0 -p 8765   # or the 'Cake: serve' task"
echo "  vendor/bin/phpunit                    # or the 'Cake: phpunit' task"
