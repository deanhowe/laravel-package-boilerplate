<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

uses(TestCase::class);


test('example', function (): void {

    ray($this->artisan('about'));
    ray()->confetti();
    //    expect(__DIR__ . '/../../src/Providers/ServiceProvider.php')->toBeFile()
    //        ->and(true)->toBeTrue();
});
