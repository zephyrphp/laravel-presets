<?php

declare(strict_types=1);

namespace Zephyr\Presets\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

final class InstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'presets:install
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Install Zephyr presets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Zephyr presets...');

        // Install dependencies...
        if ($this->hasComposerPackage('phpunit/phpunit') && ! $this->removeComposerPackages(['phpunit/phpunit'], true)) {
            $this->error('Failed to remove PHPUnit package.');

            return self::FAILURE;
        }

        if (! $this->requireComposerPackages([
            'larastan/larastan',
            'laravel/pint',
            'pestphp/pest',
            'pestphp/pest-plugin-laravel',
            'rector/rector',
        ], true)) {
            $this->error('Failed to install required packages.');

            return self::FAILURE;
        }

        // Scripts...
        if (! $this->addScriptsToComposerJsonFile()) {
            $this->error('Failed to add scripts to Composer file.');

            return self::FAILURE;
        }

        // Publish...
        $this->call('vendor:publish', ['--tag' => 'zephyr-presets', '--force' => true]);

        $this->info('Zephyr presets installed successfully.');

        return self::SUCCESS;
    }

    /**
     * Adds the necessary scripts to the application's Composer file.
     */
    private function addScriptsToComposerJsonFile(): bool
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['scripts'] ??= [];

        $composer['scripts']['code:analyse'] = 'phpstan analyse --memory-limit=2G';
        $composer['scripts']['code:coverage'] = 'pest --coverage';
        $composer['scripts']['code:lint'] = 'pint';
        $composer['scripts']['code:refactor'] = 'rector';
        $composer['scripts']['code:test'] = 'pest';

        $composer['scripts']['test'] = [
            'rector --dry-run',
            'pint --test',
            'phpstan analyse --memory-limit=2G',
            'pest --coverage --min=100',
        ];

        return file_put_contents(
            base_path('composer.json'), json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ) !== false;
    }

    /**
     * Determine if the given Composer package is installed.
     */
    private function hasComposerPackage(string $package): bool
    {
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        return array_key_exists($package, $packages['require'] ?? [])
            || array_key_exists($package, $packages['require-dev'] ?? []);
    }

    /**
     * Removes the given Composer Packages from the application.
     *
     * @param  array<string>  $packages
     */
    private function removeComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'remove'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output): void {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  array<string>  $packages
     */
    private function requireComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output): void {
                $this->output->write($output);
            }) === 0;
    }
}
