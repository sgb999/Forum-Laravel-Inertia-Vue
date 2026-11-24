#!/usr/bin/env bash
set -e

# Setup storage and cache directories
mkdir -p \
    storage/app/public/default \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache

# Sync tracked default avatars/banners into the persistent storage volume
if [ -d /usr/local/share/forum-default ]; then
    cp -a /usr/local/share/forum-default/. storage/app/public/default/ 2>/dev/null || true
fi

# Ensure proper permissions (ignore errors if volume mounts forbid it on host)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Ensure public/storage symlink exists when storage is mounted as a persistent volume
if [ ! -e public/storage ] && [ ! -L public/storage ]; then
    ln -s ../storage/app/public public/storage 2>/dev/null || true
fi

# Production initialization: apply migrations exactly once before any role
# starts, instead of every role racing `migrate --force` against the same
# database. Kamal's web role runs the image's default CMD (supervisord);
# worker, scheduler and reverb override "$@" with their own `php artisan ...`
# command (see config/deploy.yml). Use that to make web the sole migration
# owner, and have every other role wait for it to finish first.
if [ "$APP_ENV" = "production" ]; then
    case "$1" in
        *supervisord*)
            # `migrate --isolated` was tried here first, but its mutex is backed
            # by the `cache_locks` table - which doesn't exist yet on a brand
            # new database, since it's itself created by the migration this is
            # supposed to guard. `migrate:owned` (app/Console/Commands) takes a
            # Postgres session advisory lock instead, which needs no table, so
            # it also works on a completely empty database. An overlapping web
            # container from a rolling deploy that can't get the lock just
            # skips, trusting the container holding it to finish the migration.
            php artisan migrate:owned
            ;;
        *)
            echo "[entrypoint] waiting for the web role to finish migrations..."
            WAIT_TIMEOUT="${MIGRATION_WAIT_TIMEOUT:-300}"
            WAITED=0
            while true; do
                # --pending=1 exits 0 once nothing is pending, 1 while
                # migrations are pending OR the migrations table doesn't
                # exist yet (first boot) - both mean "web isn't done yet".
                if php artisan migrate:status --pending=1 >/tmp/migrate-status.log 2>&1; then
                    echo "[entrypoint] migrations are up to date, starting."
                    break
                fi
                if [ "$WAITED" -ge "$WAIT_TIMEOUT" ]; then
                    # Fail the container instead of starting against a schema
                    # that isn't ready, so Kamal reports this role as unhealthy
                    # rather than silently running degraded.
                    echo "[entrypoint] gave up waiting for migrations after ${WAIT_TIMEOUT}s, refusing to start:" >&2
                    cat /tmp/migrate-status.log >&2
                    exit 1
                fi
                sleep 2
                WAITED=$((WAITED + 2))
            done
            ;;
    esac
fi

# Development specific initialization
if [ "$APP_ENV" != "production" ]; then
    if [ ! -f .env ] && [ -f .env.example ]; then
        echo "[entrypoint] Copying .env.example to .env..."
        cp .env.example .env
    fi

    # Install / sync Composer dependencies whenever composer.lock changes.
    # app, reverb, queue and scheduler all share the bind-mounted worktree, so
    # serialize with an atomic mkdir lock inside vendor/ to avoid four containers
    # racing `composer install` on `docker compose up`.
    if [ -f composer.json ] && [ -f composer.lock ]; then
        mkdir -p vendor
        HASH_FILE="vendor/.composer-lock.hash"
        LOCK_DIR="vendor/.composer-install.lock"
        CURRENT_HASH=$(md5sum composer.lock | awk '{print $1}')
        STORED_HASH=$(cat "$HASH_FILE" 2>/dev/null || echo "")

        if [ "$CURRENT_HASH" != "$STORED_HASH" ]; then
            if mkdir "$LOCK_DIR" 2>/dev/null; then
                echo "[entrypoint] composer.lock changed - running composer install..."
                if COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist; then
                    echo "$CURRENT_HASH" > "$HASH_FILE"
                else
                    echo "[entrypoint] composer install FAILED - starting anyway with the previous vendor/ state. Fix composer.lock/composer.json and re-run 'docker compose exec app composer install' manually." >&2
                fi
                rmdir "$LOCK_DIR"
            else
                echo "[entrypoint] another container is running composer install, waiting..."
                WAITED=0
                while [ -d "$LOCK_DIR" ] && [ "$WAITED" -lt 300 ]; do
                    sleep 1
                    WAITED=$((WAITED + 1))
                done
                if [ -d "$LOCK_DIR" ]; then
                    echo "[entrypoint] lock held for 5m, assuming it's stale and clearing it" >&2
                    rmdir "$LOCK_DIR" 2>/dev/null || true
                fi
            fi
        fi
    fi
fi

exec "$@"
