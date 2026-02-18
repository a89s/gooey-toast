<?php

use Illuminate\Support\Facades\Blade;

it('registers the config', function () {
    $config = config('gooey-toast');

    expect($config)->toBeArray()
        ->and($config['position'])->toBe('bottom-center')
        ->and($config['duration'])->toBe(5000)
        ->and($config['max_toasts'])->toBe(5)
        ->and($config['theme'])->toBe('dark');
});

it('allows config values to be overridden', function () {
    config(['gooey-toast.position' => 'top-right']);
    config(['gooey-toast.duration' => 3000]);
    config(['gooey-toast.theme' => 'light']);

    expect(config('gooey-toast.position'))->toBe('top-right')
        ->and(config('gooey-toast.duration'))->toBe(3000)
        ->and(config('gooey-toast.theme'))->toBe('light');
});

it('registers the blade component alias', function () {
    $aliases = Blade::getClassComponentAliases();

    expect($aliases)->toHaveKey('gooey-toast');
});

it('loads the package views', function () {
    $finder = view()->getFinder();
    $hints = $finder->getHints();

    expect($hints)->toHaveKey('gooey-toast');
});

it('has publishable config', function () {
    $paths = \Illuminate\Support\ServiceProvider::pathsToPublish(
        \Gooey\Toast\GooeyToastServiceProvider::class,
        'gooey-toast-config'
    );

    expect($paths)->not->toBeEmpty();
});

it('has publishable views', function () {
    $paths = \Illuminate\Support\ServiceProvider::pathsToPublish(
        \Gooey\Toast\GooeyToastServiceProvider::class,
        'gooey-toast-views'
    );

    expect($paths)->not->toBeEmpty();
});

it('registers the gooeyToastStyles blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('gooeyToastStyles');
});

it('registers the gooeyToastScripts blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('gooeyToastScripts');
});
