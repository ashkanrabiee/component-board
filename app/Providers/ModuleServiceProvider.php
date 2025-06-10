<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;



class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         $this->loadModuleRoutes();
    }

      private function loadModuleRoutes(): void
    {
        $modules = config('modules.modules', []);
        
        foreach ($modules as $module => $config) {
            $routeFile = $config['path'] . '/routes.php';
            if (file_exists($routeFile)) {
                require $routeFile;
            }
        }
    }
}
