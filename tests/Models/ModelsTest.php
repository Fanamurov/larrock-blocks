<?php

namespace Larrock\ComponentBlocks\Tests\Models;

use Larrock\ComponentBlocks\BlocksComponent;
use Larrock\ComponentBlocks\LarrockComponentBlocksServiceProvider;
use Larrock\ComponentBlocks\Models\Blocks;
use Larrock\ComponentBlocks\Tests\DatabaseTest\CreateMediaDatabase;
use Larrock\Core\LarrockCoreServiceProvider;
use Larrock\ComponentBlocks\Tests\DatabaseTest\CreateBlocksDatabase;
use Orchestra\Testbench\TestCase;
use Spatie\MediaLibrary\Models\Media;

class ModelsTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $seed = new CreateBlocksDatabase();
        $seed->setUpBlocksDatabase();

        $seed = new CreateMediaDatabase();
        $seed->setUpMediaDatabase();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('medialibrary.media_model', \Spatie\MediaLibrary\Models\Media::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            LarrockCoreServiceProvider::class,
            LarrockComponentBlocksServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'LarrockBlocks' => 'Larrock\ComponentBlocks\Facades\LarrockBlocks'
        ];
    }

    public function testGetConfig()
    {
        $model = Blocks::find(1);
        $this->assertInstanceOf(BlocksComponent::class, $model->getConfig());
    }

    public function testGetFullUrlAttribute()
    {
        $model = Blocks::find(1);
        $this->assertEquals('/blocks/test', $model->full_url);
    }

    public function testGetDescriptionRenderAttribute()
    {
        $model = Blocks::find(1);
        $this->assertEquals('test', $model->description_render);
    }

    public function testSetUrlAttribute()
    {
        $model = Blocks::find(1);
        $model->url = 'test-test';
        $this->assertEquals('test_test', $model->url);
    }

    public function testLoadMedia()
    {
        $model = Blocks::find(1);
        $media = $model->loadMedia('images');
        $this->assertCount(1, $media);
        $this->assertInstanceOf(Media::class, $media->first());
        $this->assertEquals('1', $media->first()->id);
    }
}