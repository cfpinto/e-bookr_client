<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ebookr\Client\Models\Booking
 *
 * @property int $id
 * @property string $bookable_type
 * @property int $bookable_id
 * @property int $user_id
 * @property string $api_source
 * @property int $api_id
 * @property string|null $observations
 * @property string $status
 * @property \Carbon\Carbon $start
 * @property \Carbon\Carbon $end
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $bookable
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereApiSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereBookableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereBookableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereUserId($value)
 * @mixin \Eloquent
 */
class Booking extends Model
{
    protected $dates = ['start', 'end'];
    
    public function bookable()
    {
        return $this->morphTo();
    }
    
    public function getDurationAttribute()
    {
        return $this->end->diffInDays($this->start);
    }
    
}
