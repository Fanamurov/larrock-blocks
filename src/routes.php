<?php

Route::group(['prefix' => 'admin'], function(){
    Route::resource('blocks', 'Larrock\ComponentBlocks\AdminBlocksController');
});

Breadcrumbs::register('admin.'. LarrockBlocks::getName() .'.index', function($breadcrumbs){
    $breadcrumbs->push(LarrockBlocks::getTitle(), '/admin/'. LarrockBlocks::getName());
});