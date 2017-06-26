<?php

namespace Larrock\ComponentBlocks;

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
        $this->model = Blocks::class;
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
        $count = \Cache::remember('count-data-admin-'. $this->name, 1440, function(){
            return Blocks::count(['id']);
        });
        $dropdown = Blocks::whereActive(1)->orderBy('position', 'desc')->get(['id', 'title', 'url']);
        return view('larrock::admin.sectionmenu.types.dropdown', ['count' => $count, 'app' => $this, 'url' => '/admin/'. $this->name, 'dropdown' => $dropdown]);
    }
}