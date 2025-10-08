<?php

namespace Dgtlinf\Passwordless\Tests;

use Dgtlinf\Passwordless\PasswordlessServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            PasswordlessServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // ✅ Register dummy route so URL::temporarySignedRoute() works
        Route::get('/magic/{token}/{email}', function () {
            return 'ok';
        })->name('passwordless.magic');
    }
}
