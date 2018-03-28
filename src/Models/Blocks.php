<?php

namespace Larrock\ComponentBlocks\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\Core\Component;
use Larrock\Core\Helpers\Plugins\RenderPlugins;
use Larrock\Core\Traits\GetFilesAndImages;
use Larrock\Core\Traits\GetLink;
use Larrock\Core\Traits\GetSeo;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use LarrockBlocks;
use Cache;
use Spatie\MediaLibrary\Models\Media;

/**
 * Larrock\ComponentBlocks\Models\Blocks
 *
 * @property integer $id
 * @property string $title
 * @property string $descriptions
 * @property string $url
 * @property integer $position
 * @property integer $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $redirect
 * @property mixed $description_render
 * @property-read mixed $first_image
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereRedirect($value)
 * @property-read mixed $full_url
 */
class Blocks extends Model implements HasMedia
{
    /** @var $this Component */
    protected $config;

    use SearchableTrait, GetFilesAndImages, GetSeo, GetLink;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockBlocks::addFillableUserRows([]));
        $this->config = LarrockBlocks::getConfig();
        $this->table = LarrockBlocks::getTable();
    }

    protected $searchable = [
        'columns' => [
            'blocks.title' => 10
        ]
    ];

    protected $casts = [
        'position' => 'integer',
        'active' => 'integer'
    ];

    public function getFullUrlAttribute()
    {
        return '/blocks/'. $this->url;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Замена тегов плагинов на их данные
     *
     * @return mixed
     * @throws \Throwable
     */
    public function getDescriptionRenderAttribute()
    {
        $cache_key = 'DescriptionRender'. $this->table.'-'. $this->id;
        if(\Auth::check()){
            $cache_key .= '-'. \Auth::user()->role->first()->level;
        }

        return \Cache::rememberForever($cache_key, function(){
            $renderPlugins = new RenderPlugins($this->description, $this);
            $render = $renderPlugins->renderBlocks()->renderImageGallery()->renderFilesGallery();
            return $render->rendered_html;
        });
    }

    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = strtolower(str_replace('-', '_', $value));
    }

    /**
     * Перезаписываем метод из HasMediaTrait, добавляем кеш
     * @param string $collectionName
     * @return mixed
     */
    public function loadMedia(string $collectionName)
    {
        $cache_key = sha1('loadMediaCache'. $collectionName . $this->id . $this->getConfig()->getModelName());
        return Cache::rememberForever($cache_key, function () use ($collectionName) {
            $collection = $this->exists
                ? $this->media
                : collect($this->unAttachedMediaLibraryItems)->pluck('media');

            return $collection->filter(function (Media $mediaItem) use ($collectionName) {
                    if ($collectionName === '') {
                        return true;
                    }
                    return $mediaItem->collection_name === $collectionName;
                })->sortBy('order_column')->values();
        });
    }
}