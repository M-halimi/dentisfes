<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', 'fr');

        app()->setLocale(in_array($locale, ['fr', 'ar', 'en'], true) ? $locale : 'fr');

        return $next($request);
    }
}
