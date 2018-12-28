<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * App\Amenity
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Amenity withTranslations($locales = null, $fallback = true)
 * @mixin \Eloquent
 */
class Amenity extends Model
{
    use Translatable;
}
