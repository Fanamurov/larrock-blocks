<?php

namespace Larrock\ComponentBlocks;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;
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
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
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
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('renderBlock', function ($expression) {
            if($block = LarrockBlocks::getModel()->whereUrl($expression)->first()){
                $html = view('larrock::front.plugins.renderBlock.default-not-editable', ['data' => $block])->render();
                return "<?php echo '$html' ?>";
            }
        });

        $this->app['router']->aliasMiddleware('AddBlocksTemplate', AddBlocksTemplate::class);

        $this->app->singleton('larrockblocks', function() {
            $class = config('larrock.components.blocks', BlocksComponent::class);
            return new $class;
        });
    }
}