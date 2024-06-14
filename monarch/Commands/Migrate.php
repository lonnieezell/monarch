<?php

use Monarch\Console\Command;
use Monarch\Database\Migrations;

class Migrate extends Command
{
    protected string $signature = 'migrate {migration?}';
    protected string $description = 'Migrates the database';
    protected string $group = 'Database';
    protected array $options = [
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

        // The first argument is the migration name/folder
        if ($migration = $this->argument('migration')) {
            if (is_dir($migration)) {
                $this->migrations->inDirectory($migration);
            } elseif (is_file($migration)) {
                $this->migrations->only($migration);
            }
        }

        if ($this->option('fresh')) {
            $this->migrations->fresh();
        } else {
            $this->migrations->latest(fn ($migration) => $this->info("Migrating: {$migration}"));
        }

        $this->success('Done.');
    }
}
