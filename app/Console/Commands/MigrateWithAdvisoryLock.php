<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Runs `migrate --force` while holding a Postgres session-level advisory
 * lock, so that an overlapping container (e.g. two web containers briefly
 * alive during a rolling Kamal deploy) cannot run migrations at the same
 * time. Unlike `migrate --isolated`, this needs no supporting table, so it
 * also works on a brand new, completely empty database.
 */
class MigrateWithAdvisoryLock extends Command
{
    protected $signature = 'migrate:owned';

    protected $description = 'Run the database migrations, holding a Postgres advisory lock for the duration';

    /**
     * Arbitrary fixed key identifying this lock; any bigint works as long as
     * every caller (i.e. every web container) uses the same one.
     */
    private const LOCK_KEY = 8743201;

    public function handle(): int
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'pgsql') {
            // Only Postgres has advisory locks; elsewhere just migrate plainly.
            return $this->call('migrate', ['--force' => true]);
        }

        $acquired = $connection->selectOne('select pg_try_advisory_lock(?) as locked', [self::LOCK_KEY])->locked;

        if (! $acquired) {
            $this->components->warn('Another process already holds the migration lock; skipping.');

            return self::SUCCESS;
        }

        try {
            return $this->call('migrate', ['--force' => true]);
        } finally {
            $connection->selectOne('select pg_advisory_unlock(?)', [self::LOCK_KEY]);
        }
    }
}
