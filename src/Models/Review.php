<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Review
 *
 * @property int $id
 * @property string $review
 * @property mixed|null $rate
 * @property string|null $by
 * @property string|null $from
 * @property int $is_approved
 * @property int|null $room_id
 * @property int|null $location_id
 * @property string|null $activated_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $rates
 * @property-read \App\Location|null $location
 * @property-read \App\Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Review whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Review extends Model
{
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
    public function getRatesAttribute()
    {
        $rates = json_decode($this->rate, true);
        
        if (!is_array($rates)) {
            return [];
        }
        
        return $rates;
    }
}
