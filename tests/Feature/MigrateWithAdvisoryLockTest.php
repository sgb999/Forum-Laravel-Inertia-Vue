<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Postgres-only: advisory locks don't exist on the sqlite connection the rest
// of the suite runs on (see phpunit.xml), so this needs DB_CONNECTION=pgsql
// pointed at a real Postgres (e.g. `docker compose up postgres` locally).
beforeEach(function () {
    if (DB::connection()->getDriverName() !== 'pgsql') {
        $this->markTestSkipped('migrate:owned locking is Postgres-specific; the active connection is not pgsql.');
    }
});

test('migrate:owned waits for another session holding the lock, then fails after the configured timeout', function () {
    // Must match MigrateWithAdvisoryLock::LOCK_KEY.
    $lockKey = 8743201;

    $defaultConnection = config('database.default');
    $holderName = "{$defaultConnection}_lock_holder_test";
    config(["database.connections.{$holderName}" => config("database.connections.{$defaultConnection}")]);

    // A second, independent Postgres session/backend holding the lock, so it
    // behaves like a different web container - not re-entrant within one session.
    $holder = DB::connection($holderName);
    $holder->selectOne('select pg_advisory_lock(?)', [$lockKey]);

    putenv('MIGRATION_LOCK_TIMEOUT=2');

    try {
        $start = microtime(true);
        $exitCode = Artisan::call('migrate:owned');
        $elapsed = microtime(true) - $start;

        expect($exitCode)->not->toBe(0);
        expect($elapsed)->toBeGreaterThanOrEqual(2.0);
        expect(Artisan::output())->toContain('Timed out');
    } finally {
        putenv('MIGRATION_LOCK_TIMEOUT');
        $holder->selectOne('select pg_advisory_unlock(?)', [$lockKey]);
        DB::disconnect($holderName);
    }
});
