<?php

namespace Larrock\ComponentBlocks;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentBlocks\Middleware\AddBlocksTemplate;

class LarrockComponentBlocksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/larrock')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['router']->aliasMiddleware('AddBlocksTemplate', AddBlocksTemplate::class);

        $this->app->singleton('larrockblocks', function() {
            $class = config('larrock.components.blocks', BlocksComponent::class);
            return new $class;
        });

        if ( !class_exists('CreateBlocksTable')){
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/database/migrations/0000_00_00_000000_create_blocks_table.php' => database_path('migrations/'.$timestamp.'_create_blocks_table.php')
            ], 'migrations');
        }
    }
}
