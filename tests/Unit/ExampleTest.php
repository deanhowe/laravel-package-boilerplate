<?php

declare(strict_types=1);

namespace Tests\Unit;

test('example', function (): void {
    expect(true)->toBeTrue();

    $serviceProvider = new \Package\Providers\ServiceProvider(app());
    expect($serviceProvider)
        ->toBeInstanceOf(\Illuminate\Support\ServiceProvider::class)
        ->and($serviceProvider->register())->toBeNull()
        ->and($serviceProvider->boot())->toBeNull();

});
