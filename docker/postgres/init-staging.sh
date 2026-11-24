#!/bin/sh
# Idempotently creates or updates the least-privilege `forum_staging` role and
# `forum_staging` database inside the shared `forum-postgres` container, and
# revokes CONNECT on `forum_production` from `PUBLIC` and `forum_staging` so
# staging credentials cannot read or modify production data.
set -eu

STAGING_DB="${STAGING_DB_DATABASE:-forum_staging}"
STAGING_USER="${STAGING_DB_USERNAME:-forum_staging}"
STAGING_PASSWORD="${STAGING_DB_PASSWORD:-}"
PROD_DB="${POSTGRES_DB:-forum_production}"

if [ -z "$(printf '%s' "$STAGING_PASSWORD" | tr -d '[:space:]')" ]; then
    echo "Error: STAGING_DB_PASSWORD must be set and non-blank." >&2
    exit 1
fi

psql -v ON_ERROR_STOP=1 \
    --username "${POSTGRES_USER:-forum_production}" \
    --dbname "$PROD_DB" \
    --set=staging_user="$STAGING_USER" \
    --set=staging_db="$STAGING_DB" \
    --set=staging_password="$STAGING_PASSWORD" \
    --set=prod_db="$PROD_DB" <<'EOSQL'
SELECT format('CREATE ROLE %I LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE', :'staging_user', :'staging_password')
WHERE NOT EXISTS (SELECT 1 FROM pg_catalog.pg_roles WHERE rolname = :'staging_user') \gexec

SELECT format('ALTER ROLE %I WITH LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE', :'staging_user', :'staging_password') \gexec

SELECT format('CREATE DATABASE %I OWNER %I', :'staging_db', :'staging_user')
WHERE NOT EXISTS (SELECT 1 FROM pg_catalog.pg_database WHERE datname = :'staging_db') \gexec

SELECT format('ALTER DATABASE %I OWNER TO %I', :'staging_db', :'staging_user') \gexec

SELECT format('REVOKE ALL ON DATABASE %I FROM PUBLIC, %I', :'prod_db', :'staging_user') \gexec
SELECT format('REVOKE ALL ON DATABASE %I FROM PUBLIC', :'staging_db') \gexec
SELECT format('GRANT CONNECT, TEMPORARY ON DATABASE %I TO %I', :'staging_db', :'staging_user') \gexec
EOSQL
