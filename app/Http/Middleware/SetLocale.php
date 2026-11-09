<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang', session('locale', 'fr'));

        app()->setLocale(in_array($locale, ['fr', 'ar', 'en'], true) ? $locale : 'fr');

        if ($request->has('lang') && in_array($locale, ['fr', 'ar', 'en'], true)) {
            $request->session()->put('locale', $locale);
        }

        $response = $next($request);
        $response->headers->set('Content-Language', app()->getLocale());

        if ($request->routeIs('admin.*', 'language.switch')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
