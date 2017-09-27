<?php

namespace Larrock\ComponentBlocks;

use Larrock\ComponentBlocks\Facades\LarrockBlocks;
use Larrock\ComponentBlocks\Models\Blocks;
use Larrock\Core\Component;
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
        $this->rows['title'] = $row->setValid('max:255|required')->setTypo();

        $row = new FormTextarea('description', 'Текст блока');
        $this->rows['description'] = $row->setTypo();

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = \Cache::remember('count-data-admin-'. LarrockBlocks::getName(), 1440, function(){
            return LarrockBlocks::getModel()->count(['id']);
        });
        if($count > 0){
            $dropdown = LarrockBlocks::getModel()->whereActive(1)->orderBy('position', 'desc')->get(['id', 'title', 'url']);
            return view('larrock::admin.sectionmenu.types.dropdown', ['count' => $count, 'app' => LarrockBlocks::getConfig(), 'url' => '/admin/'. LarrockBlocks::getName(), 'dropdown' => $dropdown]);
        }
        return view('larrock::admin.sectionmenu.types.default', ['app' => LarrockBlocks::getConfig(), 'url' => '/admin/'. LarrockBlocks::getName()]);
    }

    public function toDashboard()
    {
        $data = \Cache::remember('LarrockBlocksItems', 1440, function(){
            return LarrockBlocks::getModel()->whereActive(1)->get();
        });
        return view('larrock::admin.dashboard.blocks', ['component' => LarrockBlocks::getConfig(), 'data' => $data]);
    }
}