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
    public function boot(){}

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app['router']->aliasMiddleware('AddBlocksTemplate', AddBlocksTemplate::class);
        $this->app->make(BlocksComponent::class);

        if ( !class_exists('CreateLarrockBlocksTable')){
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/database/migrations/0000_00_00_000000_create_blocks_table.php' => database_path('migrations/'.$timestamp.'_create_blocks_table.php')
            ], 'migrations');
        }
    }
}
