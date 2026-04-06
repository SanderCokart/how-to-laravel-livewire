#!/usr/bin/env sh
set -e

# Create a `testing` database for Pest/PHPUnit (phpunit.xml sets DB_DATABASE=testing).
if [ "$(psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" -tAc "SELECT 1 FROM pg_database WHERE datname='testing'")" != "1" ]; then
  psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" -c 'CREATE DATABASE testing;'
fi

