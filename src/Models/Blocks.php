<?php

namespace Larrock\ComponentBlocks\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Larrock\Core\Helpers\Plugins\RenderPlugins;
use Larrock\Core\Models\Seo;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;
use Spatie\MediaLibrary\Media;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
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
 * @property-read mixed $first_image
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentBlocks\Models\Blocks whereRedirect($value)
 * @property-read mixed $full_url
 */
class Blocks extends Model implements HasMediaConversions
{
    use HasMediaTrait;
    use SearchableTrait;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockBlocks::addFillableUserRows(['title', 'short', 'description', 'url', 'position', 'active']));
        $this->table = LarrockBlocks::getConfig()->table;
    }

    protected $searchable = [
        'columns' => [
            'blocks.title' => 10
        ]
    ];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('110x110')
            ->height(110)->width(110)
            ->performOnCollections('images');

        $this->addMediaConversion('140x140')
            ->height(140)->width(140)
            ->performOnCollections('images');
    }

    protected $casts = [
        'position' => 'integer',
        'active' => 'integer'
    ];

    public function get_seo()
    {
        return $this->hasOne(Seo::class, 'seo_id_connect', 'id')->whereSeoTypeConnect('blocks');
    }

    public function getImages()
    {
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', LarrockBlocks::getModelName()], ['collection_name', '=', 'images']])->orderBy('order_column', 'DESC');
    }

    public function getFirstImage()
    {
        return $this->hasOne('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', LarrockBlocks::getModelName()], ['collection_name', '=', 'images']])->orderBy('order_column', 'DESC');
    }

    public function getFiles()
    {
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', LarrockBlocks::getModelName()], ['collection_name', '=', 'files']])->orderBy('order_column', 'DESC');
    }

    public function getFirstImageAttribute()
    {
        $value = Cache::remember('image_f_blocks'. $this->id, 1440, function() {
            if($get_image = $this->getMedia('images')->sortByDesc('order_column')->first()){
                return $get_image->getUrl();
            }
            return FALSE;
        });
        return $value;
    }

    public function getFullUrlAttribute()
    {
        return '/blocks/'. $this->url;
    }

    /**
     * Замена тегов плагинов на их данные
     *
     * @return mixed
     */
    public function getDescriptionRenderAttribute()
    {
        return \Cache::remember('DescriptionRenderBlocks'. $this->id, 1440, function(){
            $renderPlugins = new RenderPlugins($this->description, $this);
            $render = $renderPlugins->renderBlocks()->renderImageGallery()->renderFilesGallery();
            return $render->rendered_html;
        });
    }
}
