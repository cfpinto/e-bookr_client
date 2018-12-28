<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

/**
 * Ebookr\Client\Models\Room
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $sinopsis
 * @property int $location_id
 * @property string|null $description
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $image
 * @property string|null $images
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Amenity[] $amenities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Booking[] $bookings
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Facility[] $facilities
 * @property-read mixed $image_gallery_url
 * @property-read mixed $image_list
 * @property-read mixed $image_list_url
 * @property-read mixed $image_url
 * @property-read null $translated
 * @property-read \Ebookr\Client\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Review[] $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Translation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereSinopsis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room withTranslations($locales = null, $fallback = true)
 * @mixin \Eloquent
 */
class Room extends Model
{
    use Translatable, Resizable;

    protected $translatable = ['name', 'description', 'sinopsis', 'meta_description', 'meta_keywords'];
    
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class)->withPivot(['cost', 'cost_period', 'cost_period_type']);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class)->withPivot(['cost', 'cost_period', 'cost_period_type']);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', '=', true);
    }

    public function getImageUrlAttribute()
    {
        return env('CDN_URL_SECURE') . '/storage/' . $this->image;
    }

    public function getImageListAttribute()
    {
        $list = json_decode($this->images);

        if (!is_array($list)) {
            return [];
        }

        return array_map(
            function ($item) {
                $parts = explode('.', $item);
                $extension = array_pop($parts);
                return (object)[
                    'image' => env('CDN_URL_SECURE') . '/storage/' . $item,
                    'thumbs' => (object)[
                        'gallery' => env('CDN_URL_SECURE') . '/storage/' . implode('.', $parts) . '-gallery.' . $extension,
                        'gallery' => env('CDN_URL_SECURE') . '/storage/' . implode('.', $parts) . '-list.' . $extension,
                    ],
                ];
            }, $list
        );
    }

    public function getImageListUrlAttribute()
    {
        return env('CDN_URL_SECURE') . '/storage/' . $this->thumbnail('list');
    }

    public function getImageGalleryUrlAttribute()
    {
        return env('CDN_URL_SECURE') . '/storage/' . $this->thumbnail('gallery');
    }
}
