<?php

use Monarch\Console\Command;
use Monarch\Database\Migrations;

class Migrate extends Command
{
    protected string $signature = 'migrate {migration?}';
    protected string $description = 'Migrates the database';
    protected string $group = 'Database';
    protected array $options = [
        '--R|rollback' => 'Rolls back the last batch of migrations',
        '--F|fresh' => 'Drops all tables and re-runs all migrations',
    ];

    private Migrations $migrations;

    public function __construct()
    {
        parent::__construct();

        $this->migrations = new Migrations();
    }

    public function run(): void
    {
        $this->info('Migrating database...');

        if ($this->option('rollback')) {
            $this->migrations->rollback(fn ($migration) => $this->info("Rolling back: {$migration}"));
        } elseif ($this->option('fresh')) {
            $this->migrations->fresh();
        } else {
            $this->migrations->latest(fn ($migration) => $this->info("Migrating: {$migration}"));
        }

        $this->success('Done.');
    }
}
