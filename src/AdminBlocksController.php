<?php

namespace Larrock\ComponentBlocks;

use Breadcrumbs;
use Illuminate\Routing\Controller;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;
use Larrock\Core\Traits\AdminMethods;

class AdminBlocksController extends Controller
{
    use AdminMethods;

	public function __construct()
	{
	    $this->config = LarrockBlocks::shareConfig();

		\Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. LarrockBlocks::getName() .'.index', function($breadcrumbs){
            $breadcrumbs->push(LarrockBlocks::getTitle(), '/admin/'. LarrockBlocks::getName());
        });
	}
}