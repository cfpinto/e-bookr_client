<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;

/**
 * Ebookr\Client\Models\Amenity
 *
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read null $translated
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Translation[] $translations
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Ebookr\Client\Models\Amenity onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Amenity withTranslations($locales = null, $fallback = true)
 * @method static \Illuminate\Database\Query\Builder|\Ebookr\Client\Models\Amenity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Ebookr\Client\Models\Amenity withoutTrashed()
 * @mixin \Eloquent
 */
class Amenity extends Model
{
    use Translatable, SoftDeletes;
}
