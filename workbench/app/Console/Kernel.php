<?php

declare(strict_types=1);

namespace Workbench\App\Console;

use Orchestra\Testbench\Foundation\Console\Kernel as ConsoleKernel;
use Throwable;

final class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<array-key, mixed>
     */
    protected $commands = [];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        if (file_exists($console = base_path('routes/console.php'))) {
            require $console;
        }
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  Throwable  $e
     * @return void
     *
     * @throws Throwable
     */
    protected function reportException(Throwable $e): void
    {
        throw $e;
    }
}
