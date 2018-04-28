<?php

namespace Larrock\ComponentBlocks;

use Cache;
use LarrockBlocks;
use Larrock\Core\Component;
use Larrock\ComponentBlocks\Models\Blocks;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;

class BlocksComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'blocks';
        $this->title = 'Блоки';
        $this->description = 'Блоки используемые для вставки в шаблон. Блок доступен в шаблоне по переменной с имененем url ("-" заменяется на "_")';
        $this->model = \config('larrock.models.blocks', Blocks::class);
        $this->addRows()->addPositionAndActive()->isSearchable()->addPlugins();
    }

    protected function addPlugins()
    {
        $this->addPluginImages()->addPluginFiles()->addPluginSeo();

        return $this;
    }

    protected function addRows()
    {
        $row = new FormInput('title', 'Название блока');
        $this->setRow($row->setValid('max:255|required')->setTypo()->setFillable());

        $row = new FormTextarea('description', 'Текст блока');
        $this->setRow($row->setTypo()->setFillable());

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = Cache::rememberForever('count-data-admin-'.LarrockBlocks::getName(), function () {
            return LarrockBlocks::getModel()->count(['id']);
        });

        return view('larrock::admin.sectionmenu.types.default',
            ['app' => LarrockBlocks::getConfig(), 'url' => '/admin/'.LarrockBlocks::getName(), 'count' => $count]);
    }

    public function toDashboard()
    {
        $data = Cache::rememberForever('LarrockBlocksItemsDashboard', function () {
            return LarrockBlocks::getModel()->whereActive(1)->orderByDesc('updated_at')->get();
        });

        return view('larrock::admin.dashboard.blocks', ['component' => LarrockBlocks::getConfig(), 'data' => $data]);
    }
}
