<?php

declare(strict_types=1);

namespace Gooey\Toast;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class GooeyToastServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/gooey-toast.php', 'gooey-toast');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gooey-toast');

        Blade::component('gooey-toast::components.container', 'gooey-toast');

        Blade::directive('gooeyToastStyles', function () {
            return "<?php echo view('gooey-toast::partials.styles')->render(); ?>";
        });

        Blade::directive('gooeyToastScripts', function () {
            return "<?php echo view('gooey-toast::partials.scripts')->render(); ?>";
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/gooey-toast.php' => config_path('gooey-toast.php'),
            ], 'gooey-toast-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/gooey-toast'),
            ], 'gooey-toast-views');
        }
    }
}
