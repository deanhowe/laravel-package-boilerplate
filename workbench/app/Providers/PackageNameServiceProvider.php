<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Workbench\App\Console\Commands\PackageNameCommand;

/**
 * Class TestServiceProvider
 *
 */
final class PackageNameServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = false;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array<array-key, mixed>
     */
    protected array $commands = [
        PackageNameCommand::class,

    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->commands($this->commands);
    }
}
