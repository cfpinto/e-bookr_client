<?php

namespace Ebookr\Client\Http\Controllers;

use Ebookr\Client\Models\Page;
use Illuminate\Http\Request;

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

        return view(\View::exists('pages.' . $page->slug) ? 'pages.' . $page->slug : 'e-bookr::pages.show')
            ->with('canonical', url($slug . '.html'))
            ->with('pageName', ucfirst(str_replace('index', 'home', $slug)))
            ->with('page', $page);
    }

    public function home(Request $request)
    {
        $slug = 'home';

        return $this->page($request, $slug);
    }
}
