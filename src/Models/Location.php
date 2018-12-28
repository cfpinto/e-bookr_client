<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * Class Location
 *
 * @property-read array $image_list
 * @package App
 * @property int $id
 * @property int|null $address_id
 * @property string $name
 * @property string|null $description
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $image
 * @property string|null $images
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $color
 * @property string|null $deleted_at
 * @property-read null $translated
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Review[] $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Room[] $rooms
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Translation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location withTranslations($locales = null, $fallback = true)
 * @mixin \Eloquent
 */
class Location extends Model
{
    use Translatable;
    
    protected $translatable = ['name', 'description', 'meta_description', 'meta_keywords'];
    
    public function rooms()
    {
        return $this->hasMany(Room::class)->where('status', Room::STATUS_ACTIVE);
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', '=', true);
    }
    
    public function getImageListAttribute()
    {
        $list = json_decode($this->images);
        
        if (!is_array($list)) {
            return [];
        }
        
        return $list;
    }
    
    public function getImage($idx = null)
    {
        if (!$idx) {
            $idx = rand(0, count($this->image_list) - 1);
        }
        
        if (!isset($this->image_list[$idx])) {
            return null;
        }
        
        return env('CDN_URL_SECURE') . '/storage/' . $this->image_list[$idx];
    }
}
