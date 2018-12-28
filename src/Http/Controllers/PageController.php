<?php

namespace Ebookr\Client\Http\Controllers;

use App\Http\Middleware\Locale;
use Ebookr\Client\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function page(Request $request, $slug)
    {
        $page = Page::whereSlug($slug)
            ->where('location_id', config('e-bookr.location_id'))
            ->first();

        //handle 404s
        if (!$page) {
            view()->composer(
                ['layouts.app', 'errors::404'],
                function ($view) use ($slug) {
                    $view
                        ->with('canonical', url($slug . '.html'))
                        ->with('pageName', ucfirst(str_replace('index', 'home', $slug)))
                        ->with('page', $slug);

                }
            );

            return abort(404);
        }

        return view(\View::exists('pages.' . $page->slug) ? 'pages.' . $page->slug : 'pages.show')
            ->with('canonical', url($slug . '.html'))
            ->with('pageName', ucfirst(str_replace('index', 'home', $slug)))
            ->with('page', $page);
    }

    public function home(Request $request)
    {
        $slug = 'home';

        return $this->page($request, $slug);
    }

    public function setLocale($locale)
    {
        if (in_array($locale, config('voyager.multilingual.locales'))) {
            app()->setLocale($locale);
            Session::put(Locale::SESSION_KEY, $locale);
        }

        return back(302, ['Cache-Control' => 'no-store, no-cache, must-revalidate']);
    }

}
