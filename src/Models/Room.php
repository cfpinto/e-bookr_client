<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
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
 * @property int $max_adult_count
 * @property int $max_children_count
 * @property string|null $api_source
 * @property string|null $api_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Amenity[] $amenities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Booking[] $bookings
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Facility[] $facilities
 * @property-read mixed $image_gallery_url
 * @property-read mixed $image_list
 * @property-read mixed $image_list_url
 * @property-read mixed $image_thumb_url
 * @property-read mixed $image_url
 * @property-read null $translated
 * @property-read \Ebookr\Client\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Review[] $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Translation[] $translations
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Ebookr\Client\Models\Room onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereApiSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereMaxAdultCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereMaxChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereSinopsis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Room withTranslations($locales = null, $fallback = true)
 * @method static \Illuminate\Database\Query\Builder|\Ebookr\Client\Models\Room withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Ebookr\Client\Models\Room withoutTrashed()
 * @mixin \Eloquent
 */
class Room extends Model
{
    use Translatable, Resizable, SoftDeletes;

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
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', '=', true);
    }

    public function getImageUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url($this->image);
    }

    public function getImageListAttribute()
    {
        $list = json_decode($this->images);

        if (!is_array($list)) {
            return [];
        }

        return array_map(
            function ($item) {
                return (object)[
                    'image'  => \Storage::disk(config('voyager.storage.disk'))->url($item),
                    'thumbs' => (object)[
                        'gallery' => \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($item, 1140, 742)),
                        'list'    => \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($item, 570, 371)),
                        'thumb'   => \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($item, 100, 100)),
                    ],
                ];
            }, $list
        );
    }

    public function getImageThumbUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($this->image, 100, 100));
    }

    public function getImageListUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($this->image, 570, 371));
    }

    public function getImageGalleryUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($this->image, 1140, 742));
    }
}
