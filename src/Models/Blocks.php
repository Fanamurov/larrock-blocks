<?php

namespace Larrock\ComponentBlocks\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\Core\Component;
use Larrock\Core\Helpers\Plugins\RenderPlugins;
use Larrock\Core\Traits\GetFilesAndImages;
use Larrock\Core\Traits\GetSeo;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Larrock\ComponentBlocks\Facades\LarrockBlocks;

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
    /**
     * @var $this Component
     */
    public $config;

    use HasMediaTrait;
    use SearchableTrait;
    use GetFilesAndImages;
    use GetSeo;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockBlocks::addFillableUserRows(['title', 'short', 'description', 'url', 'position', 'active']));
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

    /**
     * Замена тегов плагинов на их данные
     *
     * @return mixed
     */
    public function getDescriptionRenderAttribute()
    {
        $cache_key = 'DescriptionRender'. $this->table.'-'. $this->id;
        if(\Auth::check()){
            $cache_key .= '-'. \Auth::user()->role->first()->level;
        }

        return \Cache::remember($cache_key, 1440, function(){
            $renderPlugins = new RenderPlugins($this->description, $this);
            $render = $renderPlugins->renderBlocks()->renderImageGallery()->renderFilesGallery();
            return $render->rendered_html;
        });
    }
}