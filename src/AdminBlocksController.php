<?php

namespace Larrock\ComponentBlocks;

use Breadcrumbs;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;
use Larrock\Core\AdminController;

class AdminBlocksController extends AdminController
{
	public function __construct()
	{
	    LarrockBlocks::shareConfig();

        Breadcrumbs::setView('larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. LarrockBlocks::getName() .'.index', function($breadcrumbs){
            $breadcrumbs->push(LarrockBlocks::getTitle(), '/admin/'. LarrockBlocks::getName());
        });
	}
}
