<?php

namespace Larrock\ComponentBlocks\Middleware;

use Cache;
use Closure;
use LarrockBlocks;
use Larrock\Core\Helpers\Plugins\RenderPlugins;
use View;

class AddBlocksTemplate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Throwable
     */
    public function handle($request, Closure $next)
    {
        $blocks = Cache::rememberForever('blocks', function(){
            return LarrockBlocks::getModel()->whereActive(1)->get();
        });
        foreach ($blocks as $block){
            View::share($block->url, view('larrock::front.plugins.renderBlock.default', ['data' => $block])->render());
        }

        return $next($request);
    }
}