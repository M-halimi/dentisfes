<?php

namespace Tests\Feature;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_clinic_pages_are_reachable(): void
    {
        foreach (['/', '/a-propos', '/services', '/rendez-vous', '/contact'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_appointment_request_is_validated_and_stored_as_pending(): void
    {
        $response = $this->post('/rendez-vous', [
            'full_name' => 'Patient Exemple',
            'phone' => '+212 600 000 000',
            'email' => 'patient@example.test',
            'service' => 'consultation',
            'preferred_date' => now()->addDay()->toDateString(),
            'preferred_time' => '10:30',
            'message' => 'Demande de première consultation.',
        ]);

        $response->assertRedirect('/rendez-vous')->assertSessionHas('success');
        $this->assertDatabaseHas('appointments', ['full_name' => 'Patient Exemple', 'service' => 'consultation', 'status' => 'pending']);
        $this->assertSame('pending', Appointment::query()->firstOrFail()->status);
    }

    public function test_invalid_appointment_request_is_rejected(): void
    {
        $this->from('/rendez-vous')->post('/rendez-vous', [
            'full_name' => '',
            'phone' => 'bad',
            'service' => 'unknown',
            'preferred_date' => now()->subDay()->toDateString(),
            'preferred_time' => 'later',
        ])->assertRedirect('/rendez-vous')->assertSessionHasErrors(['full_name', 'phone', 'service', 'preferred_date', 'preferred_time']);

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_admin_login_pages_remain_reachable_after_repeated_requests(): void
    {
        $this->freezeTime();

        for ($request = 0; $request < 6; $request++) {
            $this->get('/admin/connexion')->assertOk();
            $this->get('/admin/login')->assertRedirect('/admin/connexion');
        }
    }

    public function test_missing_pages_use_the_clinic_not_found_view(): void
    {
        $this->get('/page-inconnue')->assertNotFound()->assertSee('404');
    }

    public function test_arabic_locale_switch_renders_rtl_content(): void
    {
        $this->get('/langue/ar')->assertRedirect();
        $this->get('/')->assertOk()->assertSee('ابتسامتكم');
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/connexion');
    }

    public function test_admin_dashboard_supports_arabic_and_french_locale_switching(): void
    {
        config(['clinic.admin_email' => 'admin@example.test', 'clinic.admin_password' => 'demo-secret']);

        $this->post('/admin/connexion', [
            'email' => 'admin@example.test',
            'password' => 'demo-secret',
        ])->assertRedirect('/admin');

        $this->from('/admin')->get('/langue/ar')->assertRedirect('/admin');
        $this->get('/admin')->assertOk()->assertSee('طلبات المواعيد')->assertSee('عربي');

        $this->from('/admin')->get('/langue/fr')->assertRedirect('/admin');
        $this->get('/admin')->assertOk()->assertSee('Demandes récentes')->assertSee('FR');
    }

    public function test_admin_can_manage_an_appointment(): void
    {
        config(['clinic.admin_email' => 'admin@example.test', 'clinic.admin_password' => 'demo-secret']);
        $appointment = Appointment::create([
            'full_name' => 'Patient Admin',
            'phone' => '+212 600 000 000',
            'service' => 'consultation',
            'preferred_date' => now()->addDay()->toDateString(),
            'preferred_time' => '09:00',
            'status' => 'pending',
        ]);

        $this->post('/admin/connexion', ['email' => 'admin@example.test', 'password' => 'demo-secret'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk()->assertSee('Patient Admin');
        $this->patch('/admin/rendez-vous/'.$appointment->id.'/statut', ['status' => 'confirmed'])->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed']);
        $this->delete('/admin/rendez-vous/'.$appointment->id)->assertRedirect();
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }
}
