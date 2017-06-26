<?php

use Larrock\ComponentBlocks\AdminBlocksController;

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu']], function(){
    Route::resource('blocks', AdminBlocksController::class);
});