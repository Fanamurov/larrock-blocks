<?php

namespace Larrock\ComponentBlocks;

use Breadcrumbs;
use Larrock\Core\AdminController;

class AdminBlocksController extends AdminController
{
	public function __construct()
	{
        $component = new BlocksComponent();
        $this->config = $component->shareConfig();

        Breadcrumbs::setView('larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. $this->config->name .'.index', function($breadcrumbs){
            $breadcrumbs->push($this->config->title, '/admin/'. $this->config->name);
        });
	}
}
