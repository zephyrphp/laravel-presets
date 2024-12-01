<?php

declare(strict_types=1);

namespace Zephyr\Presets;

use Illuminate\Support\ServiceProvider;
use Zephyr\Presets\Commands\InstallCommand;

final class PresetsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureCommands();
    }

    private function configurePublishing(): void
    {
        $this->publishes([
            __DIR__.'/../stubs/app/Providers/AppServiceProvider.php' => base_path('app/Providers/AppServiceProvider.php'),
            __DIR__.'/../stubs/tests/Arch/PresetsTest.php' => base_path('tests/Arch/PresetsTest.php'),
            __DIR__.'/../stubs/tests/Feature/WelcomeTest.php' => base_path('tests/Feature/WelcomeTest.php'),
            __DIR__.'/../stubs/tests/Unit/Models/UserTest.php' => base_path('tests/Unit/Models/UserTest.php'),
            __DIR__.'/../stubs/tests/Pest.php' => base_path('tests/Pest.php'),
            __DIR__.'/../stubs/phpstan.neon' => base_path('phpstan.neon'),
            __DIR__.'/../stubs/phpunit.xml' => base_path('phpunit.xml'),
            __DIR__.'/../stubs/pint.json' => base_path('pint.json'),
            __DIR__.'/../stubs/rector.php' => base_path('rector.php'),
        ], 'zephyr-presets');
    }

    private function configureCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
