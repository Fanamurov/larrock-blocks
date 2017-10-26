<?php

namespace Larrock\ComponentBlocks\Middleware;

use Cache;
use Closure;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;
use Larrock\Core\Helpers\Plugins\RenderGallery;
use View;

class AddBlocksTemplate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $blocks = Cache::remember('blocks', 1440, function(){
            return LarrockBlocks::getModel()->whereActive(1)->get();
        });
        foreach ($blocks as $block){
            View::share($block->url, view('larrock::front.plugins.renderBlock.default', ['data' => $block])->render());
        }

        return $next($request);
    }
}