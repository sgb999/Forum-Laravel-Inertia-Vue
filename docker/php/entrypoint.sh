#!/usr/bin/env bash
set -e

# Only needed in dev where the code is bind-mounted from the host and
# storage/ + bootstrap/cache/ may not exist yet or have host-owned perms.
if [ "$APP_ENV" != "production" ]; then
    mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

    if [ ! -f .env ] && [ -f .env.example ]; then
        cp .env.example .env
    fi

    # Keep the `vendor` named volume in sync with composer.lock automatically -
    # covers both the very first `up` (volume is empty) and any later `up`
    # after pulling code with a changed composer.lock, so nobody has to
    # remember to run `composer install` by hand.
    #
    # app/reverb/queue/scheduler all share this same volume and can start at
    # nearly the same moment, so use a directory as an atomic lock: only the
    # container that successfully creates it runs the install, the rest wait.
    if [ -f composer.json ] && [ -f composer.lock ]; then
        mkdir -p vendor
        HASH_FILE="vendor/.composer-lock.hash"
        LOCK_DIR="vendor/.composer-install.lock"
        CURRENT_HASH=$(md5sum composer.lock | awk '{print $1}')
        STORED_HASH=$(cat "$HASH_FILE" 2>/dev/null || echo "")

        if [ "$CURRENT_HASH" != "$STORED_HASH" ]; then
            if mkdir "$LOCK_DIR" 2>/dev/null; then
                echo "[entrypoint] composer.lock changed - running composer install..."
                # Composer resolving/installing a real dependency tree can need
                # more than our web-serving php.ini memory_limit (512M) - give
                # it its own unlimited budget rather than raising the limit
                # PHP-FPM itself runs with.
                if COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist; then
                    echo "$CURRENT_HASH" > "$HASH_FILE"
                else
                    # Don't let `set -e` kill the whole container over this -
                    # a crashed container just retries the same failing install
                    # forever on every restart with no way to intervene. Let
                    # php-fpm start anyway so Laravel surfaces a clear error
                    # you can actually see and debug.
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
