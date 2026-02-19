<?php

namespace A89s\GooeyToast\Tests;

use A89s\GooeyToast\GooeyToastServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            GooeyToastServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('gooey-toast', [
            'position' => 'bottom-center',
            'duration' => 5000,
            'max_toasts' => 5,
            'theme' => 'dark',
        ]);
    }
}
