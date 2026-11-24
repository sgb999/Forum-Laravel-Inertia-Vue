#!/bin/sh
# Idempotently creates or updates the least-privilege `forum_production` and
# `forum_staging` non-superuser roles and databases inside the shared
# `forum-postgres` container (connecting as the `POSTGRES_USER` cluster
# superuser, default `forum_admin`), and revokes cross-environment `CONNECT`
# privileges so neither runtime application role has superuser privileges or
# access to the other environment's database.
set -eu

ADMIN_USER="${POSTGRES_USER:-forum_admin}"
ADMIN_DB="${POSTGRES_DB:-postgres}"

PROD_DB="${PRODUCTION_DB_DATABASE:-forum_production}"
PROD_USER="${PRODUCTION_DB_USERNAME:-forum_production}"
PROD_PASSWORD="${PRODUCTION_DB_PASSWORD:-}"

STAGING_DB="${STAGING_DB_DATABASE:-forum_staging}"
STAGING_USER="${STAGING_DB_USERNAME:-forum_staging}"
STAGING_PASSWORD="${STAGING_DB_PASSWORD:-}"

if [ -z "$(printf '%s' "$PROD_PASSWORD" | tr -d '[:space:]')" ]; then
    echo "Error: PRODUCTION_DB_PASSWORD must be set and non-blank." >&2
    exit 1
fi

if [ -z "$(printf '%s' "$STAGING_PASSWORD" | tr -d '[:space:]')" ]; then
    echo "Error: STAGING_DB_PASSWORD must be set and non-blank." >&2
    exit 1
fi

if [ "$PROD_USER" = "$ADMIN_USER" ] || [ "$STAGING_USER" = "$ADMIN_USER" ]; then
    echo "Error: application roles ($PROD_USER, $STAGING_USER) must be distinct from POSTGRES_USER ($ADMIN_USER)." >&2
    exit 1
fi

if [ "$PROD_USER" = "$STAGING_USER" ]; then
    echo "Error: PRODUCTION_DB_USERNAME and STAGING_DB_USERNAME must be distinct." >&2
    exit 1
fi

until pg_isready -U "$ADMIN_USER" -d "$ADMIN_DB" >/dev/null 2>&1; do
    sleep 1
done

psql -v ON_ERROR_STOP=1 \
    --username "$ADMIN_USER" \
    --dbname "$ADMIN_DB" \
    --set=prod_user="$PROD_USER" \
    --set=prod_db="$PROD_DB" \
    --set=prod_password="$PROD_PASSWORD" \
    --set=staging_user="$STAGING_USER" \
    --set=staging_db="$STAGING_DB" \
    --set=staging_password="$STAGING_PASSWORD" <<'EOSQL'
SELECT format('CREATE ROLE %I LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS', :'prod_user', :'prod_password')
WHERE NOT EXISTS (SELECT 1 FROM pg_catalog.pg_roles WHERE rolname = :'prod_user') \gexec

SELECT format('ALTER ROLE %I WITH LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS', :'prod_user', :'prod_password') \gexec

SELECT format('CREATE DATABASE %I OWNER %I', :'prod_db', :'prod_user')
WHERE NOT EXISTS (SELECT 1 FROM pg_catalog.pg_database WHERE datname = :'prod_db') \gexec

SELECT format('ALTER DATABASE %I OWNER TO %I', :'prod_db', :'prod_user') \gexec

SELECT format('CREATE ROLE %I LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS', :'staging_user', :'staging_password')
WHERE NOT EXISTS (SELECT 1 FROM pg_catalog.pg_roles WHERE rolname = :'staging_user') \gexec

SELECT format('ALTER ROLE %I WITH LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS', :'staging_user', :'staging_password') \gexec

SELECT format('CREATE DATABASE %I OWNER %I', :'staging_db', :'staging_user')
WHERE NOT EXISTS (SELECT 1 FROM pg_catalog.pg_database WHERE datname = :'staging_db') \gexec

SELECT format('ALTER DATABASE %I OWNER TO %I', :'staging_db', :'staging_user') \gexec

SELECT format('REVOKE ALL ON DATABASE %I FROM PUBLIC, %I', :'prod_db', :'staging_user') \gexec
SELECT format('GRANT CONNECT, TEMPORARY ON DATABASE %I TO %I', :'prod_db', :'prod_user') \gexec

SELECT format('REVOKE ALL ON DATABASE %I FROM PUBLIC, %I', :'staging_db', :'prod_user') \gexec
SELECT format('GRANT CONNECT, TEMPORARY ON DATABASE %I TO %I', :'staging_db', :'staging_user') \gexec
EOSQL
