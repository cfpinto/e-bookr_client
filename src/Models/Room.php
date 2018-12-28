<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

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
