<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['fr', 'Cabinet Dentaire à Fès', 'ltr'])]
    #[TestWith(['en', 'Dental clinic in Fès', 'ltr'])]
    #[TestWith(['ar', 'عيادة أسنان في Fès', 'rtl'])]
    public function test_language_urls_render_localized_metadata_and_override_the_session(string $locale, string $title, string $direction): void
    {
        $response = $this->withSession(['locale' => 'ar'])->get('/?lang='.$locale.'&utm_source=example');

        $response->assertOk()
            ->assertHeader('Content-Language', $locale)
            ->assertSessionHas('locale', $locale)
            ->assertSee('<html lang="'.$locale.'" dir="'.$direction.'">', false)
            ->assertSee($title)
            ->assertSee('<link rel="canonical" href="https://dentafes.com/?lang='.$locale.'">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="https://dentafes.com/?lang=en">', false)
            ->assertSee('<meta property="og:site_name" content="DENTAFES">', false)
            ->assertSee('<meta name="application-name" content="DENTAFES">', false)
            ->assertDontSee('topbar-inner')
            ->assertDontSee('[Nom du cabinet]')
            ->assertDontSee('[Clinic name]')
            ->assertDontSee('[اسم العيادة]');
    }

    #[TestWith(['/?lang=unsupported'])]
    #[TestWith(['/?lang[]=ar'])]
    public function test_invalid_language_queries_fall_back_to_french(string $url): void
    {
        $response = $this->get($url);

        $response->assertOk()
            ->assertHeader('Content-Language', 'fr')
            ->assertSee('<link rel="canonical" href="https://dentafes.com/?lang=fr">', false);
    }

    public function test_sitemap_contains_only_public_canonical_pages_in_their_available_languages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $document = new \DOMDocument;
        $this->assertTrue($document->loadXML($response->getContent()));
        $locations = $document->getElementsByTagName('loc');
        $this->assertCount(21, $locations);
        $response->assertSee('https://dentafes.com/services?lang=ar', false)
            ->assertSee('https://dentafes.com/contact?lang=en', false)
            ->assertSee('https://dentafes.com/mentions-legales?lang=fr', false)
            ->assertDontSee('/admin')
            ->assertDontSee('/langue/')
            ->assertDontSee('localhost');
    }

    public function test_robots_announces_the_configured_production_sitemap(): void
    {
        config(['seo.url' => 'https://dentafes.example/']);

        $response = $this->get('/robots.txt');

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee("Allow: /\n", false)
            ->assertSee('Sitemap: https://dentafes.example/sitemap.xml', false);
    }

    #[TestWith(['/admin/connexion'])]
    #[TestWith(['/admin'])]
    #[TestWith(['/admin/parametres'])]
    #[TestWith(['/admin/rendez-vous/export'])]
    public function test_admin_pages_are_excluded_from_indexing(string $url): void
    {
        config(['clinic.admin_email' => 'admin@example.test']);
        $response = $this->withSession(['admin_authenticated' => true, 'admin_email' => 'admin@example.test'])->get($url);

        $response->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_missing_pages_do_not_advertise_indexing_or_structured_data(): void
    {
        $response = $this->get('/does-not-exist');

        $response->assertNotFound()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertDontSee('application/ld+json')
            ->assertDontSee('rel="canonical"', false);
    }

    public function test_structured_data_is_valid_json_and_escapes_clinic_text(): void
    {
        config(['clinic.address' => '</script><script>alert("test")</script>']);

        $response = $this->get('/contact?lang=en');

        $response->assertOk()->assertDontSee('</script><script>alert("test")</script>', false);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $structuredData = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('DENTAFES', $structuredData['@graph'][0]['name']);
        $this->assertSame('</script><script>alert("test")</script>', $structuredData['@graph'][0]['address']['streetAddress']);
        $this->assertSame('ContactPage', $structuredData['@graph'][2]['@type']);
        $this->assertSame('https://dentafes.com/contact?lang=en', $structuredData['@graph'][2]['url']);
    }

    public function test_legal_pages_use_the_selected_language_in_their_metadata(): void
    {
        $response = $this->get('/mentions-legales?lang=ar');

        $response->assertOk()
            ->assertHeader('Content-Language', 'ar')
            ->assertSee('<link rel="canonical" href="https://dentafes.com/mentions-legales?lang=ar">', false)
            ->assertSee('<link rel="alternate" hreflang="ar"', false);
    }
}
