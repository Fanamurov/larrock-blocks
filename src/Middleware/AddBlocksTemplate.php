<?php

namespace Larrock\ComponentBlocks\Middleware;

use Cache;
use Closure;
use Larrock\ComponentBlocks\Models\Blocks;
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
            return Blocks::whereActive(1)->get();
        });
        foreach ($blocks as $block){
            $block_name = str_replace('-', '_', $block->url);
            $can_edit_class = 'larrock-block larrock-block-'. $block->url;
            if($user = \Auth::user()){
                if ($user->level() > 2) {
                    $can_edit_class .= ' larrock-admin-block';
                }
            }
            $block->description = '<div class="'. $can_edit_class .'">'. $block->description
                .'<a class="admin_edit" target="_blank" href="/admin/blocks/' . $block->id . '/edit">Редактировать</a></div>';
            View::share($block_name, $block);
        }

        return $next($request);
    }
}
