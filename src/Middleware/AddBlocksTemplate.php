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
            $block_name = str_replace('-', '_', $block->url);
            $can_edit_class = 'larrock-block larrock-block-'. $block->url;
            $html_edit_block = '';
            if($user = \Auth::user()){
                if ($user->level() > 2) {
                    $can_edit_class .= ' larrock-admin-block';
                    $html_edit_block = '<a class="admin_edit" target="_blank" href="/admin/blocks/' . $block->id . '/edit">Редактировать</a>';
                }
            }
            $block = Cache::remember('blocksPlugins'. $block->id, 1440, function() use ($block){
                $RenderGallery = new RenderGallery();
                $block = $RenderGallery->renderGallery($block);
                $block = $RenderGallery->renderFilesGallery($block);
                return $block;
            });
            $block->description = '<div class="'. $can_edit_class .'">'. $block->description
                .$html_edit_block .'</div>';
            View::share($block_name, $block);
        }

        return $next($request);
    }
}
