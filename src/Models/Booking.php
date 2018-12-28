<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ebookr\Client\Models\Booking
 *
 * @property int $id
 * @property string $bookable_type
 * @property int $bookable_id
 * @property string $user_type
 * @property int $user_id
 * @property string $currency
 * @property string $starts_at
 * @property string $ends_at
 * @property float $price
 * @property mixed|null $price_equation
 * @property string|null $cancelled_at
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereBookableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereBookableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking wherePriceEquation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Booking whereUserType($value)
 * @mixin \Eloquent
 */
class Booking extends Model
{
    //
}
