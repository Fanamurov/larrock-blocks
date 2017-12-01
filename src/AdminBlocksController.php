<?php

namespace Larrock\ComponentBlocks;

use Illuminate\Routing\Controller;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;
use Larrock\Core\Traits\AdminMethods;

class AdminBlocksController extends Controller
{
    use AdminMethods;

	public function __construct()
	{
        $this->middleware(LarrockBlocks::combineAdminMiddlewares());
	    $this->config = LarrockBlocks::shareConfig();
		\Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
	}
}