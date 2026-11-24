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
