<?php

namespace Ebookr\Client\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

/**
 * App\Page
 *
 * @property int                 $id
 * @property int                 $author_id
 * @property string              $title
 * @property string|null         $excerpt
 * @property string|null         $body
 * @property string|null         $image
 * @property string              $slug
 * @property string|null         $meta_description
 * @property string|null         $meta_keywords
 * @property string              $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $page_id
 * @property-read mixed $image_gallery_url
 * @property-read mixed $image_hero_url
 * @property-read mixed $image_list_url
 * @property-read mixed $image_url
 * @property-read null $translated
 * @property-read \App\Page|null $page
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Page[] $pages
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Translation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page withTranslation($locale = null, $fallback = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page withTranslations($locales = null, $fallback = true)
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
        return env('CDN_URL_SECURE') . '/storage/' . $this->image;
    }

    public function getImageListUrlAttribute()
    {
        return env('CDN_URL_SECURE') . '/storage/' . $this->thumbnail('list');
    }

    public function getImageGalleryUrlAttribute()
    {
        return env('CDN_URL_SECURE') . '/storage/' . $this->thumbnail('gallery');
    }

    public function getImageHeroUrlAttribute()
    {
        return env('CDN_URL_SECURE') . '/storage/' . $this->thumbnail('hero');
    }

}
