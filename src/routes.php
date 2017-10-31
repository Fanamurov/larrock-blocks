<?php

use Larrock\ComponentBlocks\AdminBlocksController;

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('blocks', AdminBlocksController::class);
});