<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * App\Facility
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility withTranslations($locales = null, $fallback = true)
 * @mixin \Eloquent
 */
class Facility extends Model
{
    use Translatable;
}
