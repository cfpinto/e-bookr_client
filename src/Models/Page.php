<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

/**
 * Ebookr\Client\Models\Page
 *
 * @property int $id
 * @property string|null $page_id
 * @property int $author_id
 * @property int $location_id
 * @property string $title
 * @property string|null $excerpt
 * @property string|null $body
 * @property string|null $image
 * @property string $slug
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $image_gallery_url
 * @property-read mixed $image_hero_url
 * @property-read mixed $image_list_url
 * @property-read mixed $image_url
 * @property-read null $translated
 * @property-read \Ebookr\Client\Models\Page|null $page
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ebookr\Client\Models\Page[] $pages
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Translation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\Page withTranslations($locales = null, $fallback = true)
 * @mixin \Eloquent
 */
class Page extends Model
{
    
    use Translatable, Resizable;

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
    
    public function getImageUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url($this->image);
    }

    public function getImageListUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($this->image, 570, 371));
    }

    public function getImageGalleryUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($this->image, 1140, 742));
    }

    public function getImageHeroUrlAttribute()
    {
        return \Storage::disk(config('voyager.storage.disk'))->url(cloud_thumbnail_settings($this->image, 2000, 970));
    }

}
