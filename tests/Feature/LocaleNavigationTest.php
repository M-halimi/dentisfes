<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class LocaleNavigationTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['/'])]
    #[TestWith(['/a-propos'])]
    #[TestWith(['/services'])]
    #[TestWith(['/rendez-vous'])]
    #[TestWith(['/contact'])]
    #[TestWith(['/mentions-legales'])]
    #[TestWith(['/confidentialite'])]
    public function test_public_pages_follow_the_selected_language_in_both_directions(string $path): void
    {
        $arabic = $this->withSession(['locale' => 'fr'])->get($path.'?lang=ar');

        $arabic->assertOk()
            ->assertHeader('Content-Language', 'ar')
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('/contact?lang=ar"', false)
            ->assertSee('/mentions-legales?lang=ar"', false)
            ->assertSee('/confidentialite?lang=ar"', false);

        $this->get($path.'?lang=fr')->assertOk()
            ->assertHeader('Content-Language', 'fr')
            ->assertSessionHas('locale', 'fr')
            ->assertSee('<html lang="fr" dir="ltr">', false)
            ->assertSee('/contact?lang=fr"', false);

        $this->get($path)->assertOk()->assertSee('<html lang="fr" dir="ltr">', false);
    }

    #[TestWith(['fr', 'ar', 'rtl'])]
    #[TestWith(['ar', 'fr', 'ltr'])]
    public function test_language_switch_replaces_a_stale_query_parameter_and_preserves_the_page(string $previous, string $selected, string $direction): void
    {
        $response = $this->from('/contact?lang='.$previous.'&campaign=spring')->get('/langue/'.$selected);

        $response->assertRedirect('/contact?lang='.$selected.'&campaign=spring')
            ->assertSessionHas('locale', $selected);

        $this->get($response->headers->get('Location'))->assertOk()
            ->assertSee('<html lang="'.$selected.'" dir="'.$direction.'">', false);
    }

    public function test_contact_content_is_arabic_and_keeps_phone_and_email_readable(): void
    {
        config(['clinic.phone' => '07 71 75 10 87', 'clinic.email' => 'contact@example.test']);

        $response = $this->get('/contact?lang=ar');

        $response->assertOk()
            ->assertSee('البريد الإلكتروني')
            ->assertSee('ساعات العمل')
            ->assertSee('المغرب')
            ->assertDontSee('Maroc')
            ->assertDontSee('Nous trouver')
            ->assertSee('<bdi dir="ltr">07 71 75 10 87</bdi>', false)
            ->assertSee('<bdi dir="ltr">contact@example.test</bdi>', false);
    }

    #[TestWith(['/mentions-legales', 'المعلومات القانونية', 'Mentions légales'])]
    #[TestWith(['/confidentialite', 'سياسة الخصوصية', 'Politique de confidentialité'])]
    public function test_legal_content_does_not_fall_back_to_french_in_arabic(string $path, string $arabicTitle, string $frenchTitle): void
    {
        $response = $this->get($path.'?lang=ar');

        $response->assertOk()->assertSee($arabicTitle)->assertDontSee($frenchTitle);
    }
}
