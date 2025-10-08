<?php
namespace Dgtlinf\Passwordless;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PasswordlessServiceProvider extends PackageServiceProvider {
    public function configurePackage(Package $package): void
    {
        $package
            ->name('passwordless')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigration('create_passwordless_tokens_table')
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToStarRepoOnGitHub('dgtlinf/passwordless');
            });
    }

    public function packageRegistered(): void
    {
        // Here we can register custom logic or bindings after the package is booted
        $this->registerBindings();
    }

    protected function registerBindings(): void
    {
        // register the manager as a singleton
        $this->app->singleton(\Dgtlinf\Passwordless\PasswordlessManager::class);
        // alias it for facade access
        $this->app->alias(\Dgtlinf\Passwordless\PasswordlessManager::class, 'passwordless');
    }
}
