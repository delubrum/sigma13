<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $domainPath = app_path('Domain');

        if (! File::isDirectory($domainPath)) {
            return;
        }

        // Discovery of domain routes and views
        $modules = File::directories($domainPath);

        foreach ($modules as $module) {
            $routeFile = $module.'/routes.php';
            if (File::exists($routeFile)) {
                Route::middleware('web')->group($routeFile);
            }

            $viewsPath = $module.'/Web/Views';
            if (File::isDirectory($viewsPath)) {
                $domainName = strtolower(basename((string) $module));
                $this->loadViewsFrom($viewsPath, $domainName);
            }
        }
    }
}
