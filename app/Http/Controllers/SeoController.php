<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $baseUrl = rtrim(config('seo.url'), '/');
        $urls = [];

        foreach (config('seo.pages') as $page) {
            $locales = in_array($page, config('seo.localized_pages'), true) ? config('seo.locales') : ['fr'];

            foreach ($locales as $locale) {
                $urls[] = $baseUrl.route($page, ['lang' => $locale], false);
            }
        }

        return response()->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\nDisallow: /langue/\n\nSitemap: ".rtrim(config('seo.url'), '/')."/sitemap.xml\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
