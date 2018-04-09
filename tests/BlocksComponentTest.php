<?php

namespace Larrock\ComponentBlocks\Tests;

use Larrock\ComponentBlocks\BlocksComponent;
use Larrock\ComponentBlocks\LarrockComponentBlocksServiceProvider;
use Larrock\ComponentBlocks\Tests\DatabaseTest\CreateBlocksDatabase;
use Larrock\Core\LarrockCoreServiceProvider;
use Orchestra\Testbench\TestCase;

class BlocksComponentTest extends TestCase
{
    /** @var BlocksComponent */
    protected $component;

    protected function setUp()
    {
        parent::setUp();

        $this->component = new BlocksComponent();

        $seed = new CreateBlocksDatabase();
        $seed->setUpBlocksDatabase();
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->component);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('larrock.middlewares.front', ['TestAddFront']);
        $app['config']->set('larrock.middlewares.admin', ['TestAddAdmin']);
        $app['config']->set('larrock.feed.anonsCategory', 1);
    }

    protected function getPackageProviders($app)
    {
        return [
            LarrockCoreServiceProvider::class,
            //JsValidationServiceProvider::class,
            //LarrockComponentAdminSeoServiceProvider::class,
            //BreadcrumbsServiceProvider::class,
            LarrockComponentBlocksServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'JsValidator' => 'Proengsoft\JsValidation\Facades\JsValidatorFacade',
            'LarrockAdminSeo' => 'Larrock\ComponentAdminSeo\Facades\LarrockSeo',
            //'LarrockFeed' => 'Larrock\ComponentFeed\Facades\LarrockFeed',
            'Breadcrumbs' => 'DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs',
            'LarrockBlocks' => 'Larrock\ComponentBlocks\Facades\LarrockBlocks'
        ];
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf(BlocksComponent::class, $this->component->getConfig());
        $this->assertEquals('blocks', $this->component->name);
        $this->assertCount(8, $this->component->rows);
        $rows = ['title', 'description', 'position', 'active', 'seo_title', 'seo_description', 'seo_keywords', 'url'];
        foreach ($this->component->rows as $row){
            $this->assertEquals($rows, array_keys($this->component->rows));
        }

        $this->assertCount(3, $this->component->plugins_backend);
        $plugins = ['images', 'files', 'seo'];
        foreach ($this->component->plugins_backend as $row){
            $this->assertEquals($plugins, array_keys($this->component->plugins_backend));
        }
    }

    public function testSearch()
    {
        $test = $this->component->search();
        $this->assertCount(1, $test);
        $this->assertEquals('test', $test[1]['title']);
    }
}