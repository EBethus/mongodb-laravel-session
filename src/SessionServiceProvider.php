<?php
namespace ForFit\Session;

use ForFit\Session\Console\Commands\MongodbSessionDropIndex;
use ForFit\Session\Console\Commands\MongodbSessionIndex;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider as ParentServiceProvider;

class SessionServiceProvider extends ParentServiceProvider
{

  /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving('session', function($session)
        {
            $session->extend('mongodb', function ($app) {
                $configs = $app['config']->get('session');
                $connection = $app['db']->connection($configs['connection'] ?? null);
                return new MongoDbSessionHandler($connection, $configs['table'], $configs['lifetime']);
            });
        });
    }
 

    /**
     * Register any application services.
     *
     * @throws \Exception
     * @return void
     *
     */
    public function boot()
    {
        // register the collection indexing commands and migrations if running in cli
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
            $this->commands([
                MongodbSessionDropIndex::class,
                MongodbSessionIndex::class,
            ]);
        }
    }
}
