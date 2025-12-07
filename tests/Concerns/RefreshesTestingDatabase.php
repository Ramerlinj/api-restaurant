<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait RefreshesTestingDatabase
{
    use RefreshDatabase {
        migrateFreshUsing as baseMigrateFreshUsing;
    }

    /**
     * Restrict migrations to the lightweight testing set.
     */
    protected function migrateFreshUsing(): array
    {
        return array_merge(
            $this->baseMigrateFreshUsing(),
            ['--path' => 'database/migrations/testing'],
        );
    }
}
