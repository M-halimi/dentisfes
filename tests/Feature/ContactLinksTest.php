<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ContactLinksTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['0771751087'])]
    #[TestWith(['07 71 75 10 87'])]
    #[TestWith(['+212 771 751 087'])]
    #[TestWith(['00212771751087'])]
    #[TestWith(['212771751087'])]
    #[TestWith([''])]
    public function test_whatsapp_links_open_the_owner_chat_for_saved_number_formats(string $savedNumber): void
    {
        config(['clinic.phone' => '0771751087']);
        ClinicSetting::create(['key' => 'whatsapp_number', 'value' => $savedNumber]);

        $response = $this->get('/contact?lang=ar');

        $response->assertOk();
        preg_match_all('/<a\b[^>]*href="(https:\/\/wa\.me\/[^\"]+)"/', $response->getContent(), $matches);
        $this->assertNotEmpty($matches[1]);

        foreach ($matches[1] as $link) {
            $this->assertSame('/212771751087', parse_url($link, PHP_URL_PATH));
        }
    }

    #[TestWith(['fr', 'Discuter sur WhatsApp'])]
    #[TestWith(['en', 'Chat with us on WhatsApp'])]
    #[TestWith(['ar', 'تواصلوا معنا على واتساب'])]
    public function test_contact_offers_a_translated_whatsapp_action_and_working_contact_links(string $locale, string $action): void
    {
        config(['clinic.phone' => '0771751087', 'clinic.email' => 'contact@dentafes.example']);

        $response = $this->get('/contact?lang='.$locale);

        $response->assertOk()
            ->assertSee($action)
            ->assertSee('href="tel:0771751087"', false)
            ->assertSee('href="mailto:contact@dentafes.example"', false)
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=', false);
    }
}
