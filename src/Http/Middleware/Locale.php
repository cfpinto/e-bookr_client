<?php

namespace Ebookr\Client\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class Locale
{

    const SESSION_KEY = 'locale';
    const LOCALE_PT = 'pt';
    const LOCALE_EN = 'en';

    public function handle(Request $request, Closure $next) {
        /** @var Session $session */
        $session = $request->getSession();

        if (!$session->has(self::SESSION_KEY)) {
            $session->put(self::SESSION_KEY, $request->getPreferredLanguage(config('voyager.multilingual.locales')));
        }

        app()->setLocale($session->get(self::SESSION_KEY));
        Carbon::setLocale(app()->getLocale());

        return $next($request);
    }
}
