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
DB_TEST_DATABASE="${DB_TEST_DATABASE:-cakephp_test}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-root}"

mysql_cmd() {
  mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "$@"
}

root_mysql_cmd() {
  mysql -h"${DB_HOST}" -P"${DB_PORT}" -uroot -p"${DB_ROOT_PASSWORD}" "$@"
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

echo "==> Setup complete"
echo "Run:"
echo "  bin/cake server -H 0.0.0.0 -p 8765   # or the 'Cake: serve' task"
echo "  vendor/bin/phpunit                    # or the 'Cake: phpunit' task"
