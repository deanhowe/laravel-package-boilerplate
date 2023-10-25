<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Hash;

trait CreatesApplication
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'sqlite']);
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
        $this->withFactories(__DIR__.'/factories');
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = parent::createApplication();

        Hash::setRounds(4);

        return $app;
    }

    protected function getPackageProviders($app): array
    {
        return ['Package\Providers\ServiceProvider'];
    }

    protected function getPackageAliases($app): array
    {
        return [
            // 'Facade' => 'Package\Facade',
        ];
    }
}
