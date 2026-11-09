<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\PatientService;
use App\Models\Payment;
use App\Models\User;
use App\PatientPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PatientRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_routes_require_the_existing_admin_session(): void
    {
        $patient = Patient::factory()->create();

        $this->get(route('admin.patients.index'))->assertRedirectToRoute('admin.login');
        $this->get(route('admin.patients.show', $patient))->assertRedirectToRoute('admin.login');
        $this->get(route('admin.patients.pdf', $patient))->assertRedirectToRoute('admin.login');
        $this->get(route('admin.payments.index'))->assertRedirectToRoute('admin.login');
        $this->post(route('admin.patients.notes.store', $patient), ['body' => 'Secret'])->assertRedirectToRoute('admin.login');
        $this->post(route('admin.patients.payments.store', $patient), [])->assertRedirectToRoute('admin.login');
        $this->post(route('admin.patients.documents.store', $patient), [])->assertRedirectToRoute('admin.login');
        $this->assertDatabaseCount('patient_notes', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_an_unapproved_session_cannot_read_patient_records(): void
    {
        config(['clinic.admin_email' => 'admin@example.test']);

        $this->withSession(['admin_authenticated' => true, 'admin_email' => 'other@example.test'])
            ->get(route('admin.patients.index'))->assertForbidden();
    }

    public function test_patient_creation_reuses_a_matching_name_and_normalized_phone(): void
    {
        $this->admin();
        $contact = ['full_name' => 'Yassine Bennani', 'phone' => '+212 600 000 001', 'email' => null];

        $this->post(route('admin.patients.store'), $contact)->assertRedirect();
        $patient = Patient::query()->sole();
        $this->post(route('admin.patients.store'), ['full_name' => ' yassine  bennani ', 'phone' => '0600000001'])->assertRedirectToRoute('admin.patients.show', $patient);

        $this->assertDatabaseCount('patients', 1);
        $this->assertStringStartsWith('PAT-', $patient->reference);
        $this->post(route('admin.patients.store'), ['full_name' => 'Autre Patient', 'phone' => '0600000001'])->assertRedirect();
        $this->assertDatabaseCount('patients', 2);
    }

    public function test_contact_service_and_appointment_validation_preserves_existing_records(): void
    {
        $this->admin();
        $patient = Patient::factory()->create();

        $this->post(route('admin.patients.store'), ['full_name' => '', 'phone' => 'bad', 'email' => 'bad'])->assertSessionHasErrors(['full_name', 'phone', 'email']);
        $this->post(route('admin.patients.services.store', $patient), ['label' => 'Test', 'performed_on' => today()->toDateString(), 'price' => '0'])->assertSessionHasErrors('price');
        $this->post(route('admin.patients.services.store', $patient), ['label' => '', 'performed_on' => 'invalid', 'price' => '1.234'])->assertSessionHasErrors(['label', 'performed_on', 'price']);
        $this->post(route('admin.patients.appointments.store', $patient), ['service' => 'unknown', 'preferred_date' => today()->subDay()->toDateString(), 'preferred_time' => 'bad'])->assertSessionHasErrors(['service', 'preferred_date', 'preferred_time']);

        $this->assertDatabaseCount('patients', 1);
        $this->assertDatabaseCount('patient_services', 0);
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_payment_date_filter_accepts_an_end_date_without_a_start_date(): void
    {
        $this->admin();
        $this->freezeTime();
        Payment::factory()->create(['paid_on' => today()->subDay()->toDateString(), 'amount_cents' => 12345]);

        $this->get(route('admin.payments.index', ['to' => today()->toDateString()]))->assertOk()->assertViewHas('collected', 12345);
        $this->get(route('admin.payments.index', ['from' => today()->toDateString(), 'to' => today()->subDay()->toDateString()]))->assertSessionHasErrors('to');
    }

    public function test_contact_edit_keeps_the_reference_and_rejects_duplicate_contacts(): void
    {
        $this->admin();
        $patient = Patient::factory()->create();
        $other = Patient::factory()->create();
        $reference = $patient->reference;

        $this->put(route('admin.patients.update', $patient), ['full_name' => 'Nom Modifié', 'phone' => '0612345678', 'email' => null])->assertRedirect();

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'full_name' => 'Nom Modifié', 'reference' => $reference, 'email' => null]);
        $this->put(route('admin.patients.update', $patient), $other->only(['full_name', 'phone', 'email']))->assertSessionHasErrors('phone');
    }

    public function test_legacy_appointment_backfill_preserves_history_and_is_repeatable(): void
    {
        $data = ['full_name' => 'Ancien Patient', 'phone' => '+212600000001', 'email' => null, 'service' => 'consultation', 'preferred_date' => '2025-01-02', 'preferred_time' => '09:00', 'message' => 'Historique', 'status' => 'confirmed'];
        $first = DB::table('appointments')->insertGetId($data);
        $second = DB::table('appointments')->insertGetId(array_replace($data, ['phone' => '0600000001']));
        $migration = require database_path('migrations/2026_11_03_133547_link_existing_appointments_to_patients.php');

        $migration->up();
        $migration->up();

        $patient = Patient::query()->sole();
        $this->assertDatabaseHas('appointments', ['id' => $first, 'patient_id' => $patient->id, 'message' => 'Historique', 'status' => 'confirmed']);
        $this->assertDatabaseHas('appointments', ['id' => $second, 'patient_id' => $patient->id]);
        $this->assertDatabaseCount('payments', 0);
    }

    #[TestWith(['fr', 'Dossier patient', 'ltr'])]
    #[TestWith(['en', 'Patient record', 'ltr'])]
    #[TestWith(['ar', 'ملف المريض', 'rtl'])]
    public function test_all_patient_tabs_render_in_the_selected_language(string $locale, string $title, string $direction): void
    {
        $this->admin();
        $patient = Patient::factory()->create(['full_name' => '<script>alert(1)</script>']);
        $patient->notes()->create(['body' => '<script>secret()</script>', 'author' => 'admin@example.test']);

        foreach (['overview', 'appointments', 'payments', 'documents'] as $tab) {
            $this->get(route('admin.patients.show', [$patient, 'tab' => $tab, 'lang' => $locale]))->assertOk()
                ->assertSee($title)->assertSee('dir="'.$direction.'"', false)
                ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        }
        $this->get(route('admin.patients.index'))->assertOk();
        $this->get(route('admin.payments.index'))->assertOk();
    }

    public function test_authorized_notes_are_dated_and_hidden_if_permission_is_removed(): void
    {
        $this->admin();
        $this->travelTo(now()->setDate(2026, 11, 3)->setTime(12, 0));
        $patient = Patient::factory()->create();

        $this->post(route('admin.patients.notes.store', $patient), ['body' => 'Suivi confidentiel'])->assertRedirect();

        $this->assertDatabaseHas('patient_notes', ['patient_id' => $patient->id, 'body' => 'Suivi confidentiel', 'author' => 'admin@example.test', 'created_at' => '2026-11-03 12:00:00']);
        Gate::define('patient-notes', fn (?User $user): bool => false);
        $this->post(route('admin.patients.notes.store', $patient), ['body' => 'Autre note'])->assertForbidden();
        $this->get(route('admin.patients.show', $patient))->assertOk()->assertDontSee('Suivi confidentiel');
        $this->assertDatabaseCount('patient_notes', 1);
    }

    public function test_patient_appointment_uses_existing_agenda_and_shows_the_next_active_slot(): void
    {
        $this->admin();
        $this->travelTo(now()->setDate(2026, 11, 3)->setTime(12, 0));
        $patient = Patient::factory()->create();
        $this->appointment($patient, ['preferred_date' => '2026-11-03', 'preferred_time' => '09:00']);
        $this->appointment($patient, ['preferred_date' => '2026-11-04', 'status' => 'cancelled']);
        $this->appointment($patient, ['preferred_date' => '2026-11-04', 'status' => 'completed']);

        $this->post(route('admin.patients.appointments.store', $patient), ['service' => 'prosthetics', 'preferred_date' => '2026-11-05', 'preferred_time' => '10:30'])->assertRedirect();

        $this->assertDatabaseHas('appointments', ['patient_id' => $patient->id, 'full_name' => $patient->full_name, 'service' => 'prosthetics', 'status' => 'confirmed']);
        $this->get(route('admin.patients.show', $patient))->assertOk()->assertViewHas('nextAppointment', fn ($appointment): bool => $appointment->preferred_date->toDateString() === '2026-11-05');
        $this->get(route('admin.dashboard', ['date' => '2026-11-05']))->assertOk()->assertSee($patient->full_name);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_appointment_rescheduling_is_scoped_to_its_patient(): void
    {
        $this->admin();
        $patient = Patient::factory()->create();
        $other = Patient::factory()->create();
        $appointment = $this->appointment($patient);
        $data = ['service' => 'prosthetics', 'preferred_date' => today()->addDays(3)->toDateString(), 'preferred_time' => '11:30'];

        $this->patch(route('admin.patients.appointments.update', [$other, $appointment]), $data)->assertNotFound();
        $this->patch(route('admin.patients.appointments.update', [$patient, $appointment]), $data)->assertRedirect();

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'preferred_time' => '11:30', 'service' => 'prosthetics']);
    }

    public function test_services_and_partial_payments_keep_exact_balances(): void
    {
        $this->admin();
        $patient = Patient::factory()->create();
        $this->post(route('admin.patients.services.store', $patient), ['label' => 'Prestation', 'performed_on' => today()->toDateString(), 'price' => '1,00'])->assertRedirect();
        $service = $patient->services()->sole();

        foreach (['0.10', '0.20', '0.70'] as $amount) {
            $this->pay($patient, $service, ['amount' => $amount])->assertRedirect()->assertSessionHasNoErrors();
        }

        $this->assertDatabaseHas('patient_services', ['id' => $service->id, 'price_cents' => 100]);
        $this->assertDatabaseCount('payment_audits', 3);
        $this->get(route('admin.patients.show', $patient))->assertOk()->assertViewHas('total', 100)->assertViewHas('paid', 100);
        $this->pay($patient, $service, ['amount' => '0.01'])->assertSessionHasErrors(['amount' => 'Ce paiement dépasse le reste à payer de cette prestation.']);
        $this->assertDatabaseCount('payments', 3);
        $this->assertSame(3, Payment::query()->distinct()->count('receipt_number'));
    }

    #[TestWith(['0'])]
    #[TestWith(['-1'])]
    #[TestWith(['1.234'])]
    #[TestWith(['1e2'])]
    #[TestWith(['100000000'])]
    #[TestWith(['NaN'])]
    public function test_invalid_payment_amounts_do_not_change_the_ledger(string $amount): void
    {
        $this->admin();
        $service = PatientService::factory()->create();

        $this->pay($service->patient, $service, ['amount' => $amount])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('payment_audits', 0);
    }

    public function test_payment_validation_rejects_invalid_date_method_and_missing_fields(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $patient = $service->patient;

        $this->post(route('admin.patients.payments.store', $patient), [])->assertSessionHasErrors(['amount', 'paid_on', 'method', 'patient_service_id', 'submission_key']);
        $this->pay($patient, $service, ['method' => 'bitcoin', 'paid_on' => today()->addDay()->toDateString()])->assertSessionHasErrors(['method', 'paid_on']);
        $this->pay($patient, $service, ['paid_on' => 'invalid'])->assertSessionHasErrors('paid_on');
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_an_advance_payment_keeps_its_actual_collection_date(): void
    {
        $this->admin();
        $this->freezeTime();
        $service = PatientService::factory()->create(['performed_on' => today()->addDays(7)->toDateString()]);

        $this->pay($service->patient, $service, ['paid_on' => today()->subDay()->toDateString()])->assertRedirect()->assertSessionHasNoErrors();

        $payment = Payment::query()->sole();
        $this->assertSame(today()->subDay()->toDateString(), $payment->paid_on->toDateString());
        $this->assertSame(50000, $payment->amount_cents);
    }

    public function test_repeated_payment_submission_does_not_duplicate_a_receipt(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $key = (string) Str::uuid();

        $this->pay($service->patient, $service, ['submission_key' => $key])->assertRedirect();
        $this->pay($service->patient, $service, ['submission_key' => $key])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('payment_audits', 1);
        $this->pay($service->patient, $service, ['submission_key' => $key, 'amount' => '100.00'])->assertSessionHasErrors('amount');
    }

    public function test_cancellation_retains_the_payment_and_records_actor_reason_and_original_values(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $patient = $service->patient;
        $this->pay($patient, $service)->assertRedirect();
        $payment = Payment::query()->sole();

        $this->post(route('admin.patients.payments.cancel', [$patient, $payment]), [])->assertSessionHasErrors('reason');
        $this->post(route('admin.patients.payments.cancel', [$patient, $payment]), ['reason' => 'Erreur de saisie'])->assertRedirect();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'amount_cents' => 50000]);
        $this->assertNotNull($payment->fresh()->cancelled_at);
        $this->assertDatabaseHas('payment_audits', ['payment_id' => $payment->id, 'action' => 'cancelled', 'actor' => 'admin@example.test', 'reason' => 'Erreur de saisie']);
        $this->get(route('admin.patients.show', $patient))->assertViewHas('paid', 0)->assertViewHas('total', 150000);
        $this->post(route('admin.patients.payments.cancel', [$patient, $payment]), ['reason' => 'Encore'])->assertSessionHasErrors('reason');
        $this->assertDatabaseCount('payment_audits', 2);
        $this->pay($patient, $service, ['amount' => '1500.00'])->assertRedirect()->assertSessionHasNoErrors();
    }

    public function test_payments_and_receipts_cannot_be_accessed_through_another_patient(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $other = Patient::factory()->create();
        $this->pay($service->patient, $service)->assertRedirect();
        $payment = Payment::query()->sole();

        $this->pay($other, $service)->assertNotFound();
        $this->get(route('admin.patients.payments.receipt', [$other, $payment]))->assertNotFound();
        $this->post(route('admin.patients.payments.cancel', [$other, $payment]), ['reason' => 'Erreur'])->assertNotFound();
        $this->assertNull($payment->fresh()->cancelled_at);
    }

    public function test_dashboard_revenue_uses_payment_dates_and_excludes_cancelled_payments(): void
    {
        $this->admin();
        $this->travelTo(now()->setDate(2026, 11, 3)->setTime(12, 0));
        $service = PatientService::factory()->create();
        $this->appointment($service->patient, ['status' => 'confirmed', 'preferred_date' => '2026-11-03']);
        Payment::factory()->for($service)->create(['amount_cents' => 50123, 'paid_on' => '2026-11-03']);
        Payment::factory()->for($service)->create(['amount_cents' => 10000, 'paid_on' => '2026-11-01']);
        Payment::factory()->for($service)->create(['amount_cents' => 20000, 'paid_on' => '2026-10-01']);
        Payment::factory()->for($service)->create(['amount_cents' => 30000, 'paid_on' => '2025-11-03']);
        Payment::factory()->for($service)->create(['amount_cents' => 40000, 'paid_on' => '2026-11-03', 'cancelled_at' => now()]);

        $response = $this->get(route('admin.dashboard'))->assertOk();
        $stats = $response->viewData('periodStats');
        $this->assertSame(50123, $stats['day']['revenue']);
        $this->assertSame(60123, $stats['month']['revenue']);
        $this->assertSame(80123, $stats['year']['revenue']);
        $response->assertSee('601,23 MAD')->assertDontSee('temps réel')->assertDontSee('Synchronisation')->assertDontSee('Dr. Anas');
        $this->get(route('admin.payments.index', ['from' => '2026-11-01', 'to' => '2026-11-03']))->assertOk()->assertViewHas('collected', 60123);
    }

    #[TestWith(['fr'])]
    #[TestWith(['en'])]
    #[TestWith(['ar'])]
    public function test_numbered_pdf_receipts_use_frozen_establishment_and_patient_details(string $locale): void
    {
        $this->admin();
        ClinicSetting::updateOrCreate(['key' => 'profession'], ['value' => 'Prothésiste configuré']);
        ClinicSetting::updateOrCreate(['key' => 'practitioner_name'], ['value' => 'Anas Kheribech']);
        config(['seo.url' => 'https://dentafes.example']);
        $patient = Patient::factory()->create(['full_name' => 'مريض تجريبي']);
        $service = PatientService::factory()->for($patient)->create();
        $this->pay($patient, $service)->assertRedirect();
        $payment = Payment::query()->sole();
        ClinicSetting::where('key', 'profession')->update(['value' => 'Nouvelle désignation']);
        ClinicSetting::where('key', 'practitioner_name')->update(['value' => 'Nouveau nom']);
        config(['seo.url' => 'https://changed.example']);

        $response = $this->get(route('admin.patients.payments.receipt', [$patient, $payment, 'lang' => $locale]));

        $response->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertDownload($payment->receipt_number.'.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertSame('Prothésiste configuré', $payment->receipt_snapshot['clinic']['profession']);
        $html = $this->receiptHtml($payment);
        $this->assertStringContainsString('Prothésiste configuré', $html);
        $this->assertStringNotContainsString('Nouvelle désignation', $html);
        $this->assertStringContainsString('مريض تجريبي', $html);
        $this->assertStringContainsString('Anas Kheribech', $html);
        $this->assertStringContainsString('href="https://dentafes.example"', $html);
        $this->assertStringContainsString('src="data:image/png;base64,', $html);
        $this->assertStringNotContainsString('Nouveau nom', $html);
        $this->assertStringNotContainsString('https://changed.example', $html);
    }

    public function test_cancelled_receipt_is_marked_and_remains_downloadable(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $this->pay($service->patient, $service)->assertRedirect();
        $payment = Payment::query()->sole();
        $this->post(route('admin.patients.payments.cancel', [$service->patient, $payment]), ['reason' => 'Correction'])->assertRedirect();

        $this->get(route('admin.patients.payments.receipt', [$service->patient, $payment]))->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringContainsString('REÇU ANNULÉ', $this->receiptHtml($payment->fresh()));
    }

    public function test_older_receipts_receive_branding_without_overwriting_their_snapshot(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $this->pay($service->patient, $service)->assertRedirect();
        $payment = Payment::query()->sole();
        $snapshot = $payment->receipt_snapshot;
        unset($snapshot['clinic']['website'], $snapshot['clinic']['practitioner_name']);
        $snapshot['clinic']['phone'] = '0600000001';
        $payment->update(['receipt_snapshot' => $snapshot]);
        config(['clinic.practitioner_name' => 'Anas Kheribech', 'seo.url' => 'https://dentafes.example']);
        ClinicSetting::updateOrCreate(['key' => 'practitioner_name'], ['value' => 'Anas Kheribech configuré']);

        $this->get(route('admin.patients.payments.receipt', [$service->patient, $payment]))->assertOk()->assertDownload($payment->receipt_number.'.pdf');

        $html = $this->receiptHtml($payment);
        $this->assertStringContainsString('Anas Kheribech', $html);
        $this->assertStringContainsString('0600000001', $html);
        $this->assertStringContainsString('Anas Kheribech configuré', $html);
        $this->assertSame($snapshot, $payment->fresh()->receipt_snapshot);
    }

    #[TestWith(['fr'])]
    #[TestWith(['en'])]
    #[TestWith(['ar'])]
    public function test_patient_record_pdf_is_branded_and_excludes_internal_notes(string $locale): void
    {
        $this->admin();
        config(['seo.url' => 'https://dentafes.example']);
        $patient = Patient::factory()->create(['full_name' => 'Patient <script>secret()</script>']);
        $service = PatientService::factory()->for($patient)->create();
        $this->pay($patient, $service)->assertRedirect();
        $cancelled = Payment::factory()->for($service)->create(['cancelled_at' => now()]);
        $patient->notes()->create(['body' => 'NOTE STRICTEMENT INTERNE', 'author' => 'admin@example.test']);
        $this->appointment($patient);

        $response = $this->get(route('admin.patients.pdf', [$patient, 'lang' => $locale]));

        $response->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertDownload('Dossier-'.$patient->reference.'.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $service->setAttribute('paid_cents', 50000);
        $html = view('admin.patients.record-pdf', [
            'patient' => $patient, 'services' => collect([$service]), 'total' => 150000, 'paid' => 50000,
            'payments' => $patient->payments()->with('patientService')->get(),
            'appointments' => $patient->appointments()->get(), 'generatedAt' => now(),
        ] + (new PatientPdf)->branding(ClinicSetting::apply(config('clinic'))))->render();
        $this->assertStringContainsString('Anas Kheribech', $html);
        $this->assertStringContainsString('https://dentafes.example', $html);
        $this->assertStringContainsString($cancelled->receipt_number, $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>secret()</script>', $html);
        $this->assertStringNotContainsString('NOTE STRICTEMENT INTERNE', $html);
    }

    public function test_pdf_branding_does_not_embed_an_unsafe_website_link(): void
    {
        $this->admin();
        $service = PatientService::factory()->create();
        $this->pay($service->patient, $service)->assertRedirect();
        $payment = Payment::query()->sole();
        $snapshot = $payment->receipt_snapshot;
        $snapshot['clinic']['website'] = 'javascript:alert(1)';
        $payment->receipt_snapshot = $snapshot;

        $this->assertStringNotContainsString('javascript:', $this->receiptHtml($payment));
    }

    public function test_professional_name_is_configurable_in_the_existing_settings(): void
    {
        $this->admin();
        $clinic = config('clinic');
        $data = array_intersect_key($clinic, array_flip(['name', 'profession', 'city', 'address', 'phone', 'email', 'instagram', 'whatsapp_number', 'hours']));

        $this->put(route('admin.settings.update'), $data + ['practitioner_name' => 'Anas configuré', 'admin_locale' => 'fr', 'default_duration' => '45'])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('clinic_settings', ['key' => 'practitioner_name', 'value' => 'Anas configuré']);
        $this->get(route('admin.settings'))->assertOk()->assertSee('Anas configuré');
    }

    private function receiptHtml(Payment $payment): string
    {
        return view('admin.patients.receipt', compact('payment') + (new PatientPdf)->branding($payment->receipt_snapshot['clinic']))->render();
    }

    public function test_documents_are_private_and_downloads_are_scoped_and_authenticated(): void
    {
        $this->admin();
        Storage::fake('patients');
        $patient = Patient::factory()->create();
        $other = Patient::factory()->create();
        $file = UploadedFile::fake()->image('document.png', 10, 10);

        $this->post(route('admin.patients.documents.store', $patient), ['document' => $file])->assertRedirect();

        $document = $patient->documents()->sole();
        Storage::disk('patients')->assertExists($document->path);
        $this->get(route('admin.patients.documents.show', [$patient, $document]))->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get(route('admin.patients.documents.download', [$patient, $document]))->assertOk()->assertDownload('document.png');
        $this->get(route('admin.patients.documents.download', [$other, $document]))->assertNotFound();
        $this->get('/storage/'.$document->path)->assertForbidden();
        $this->withSession(['admin_authenticated' => false])->get(route('admin.patients.documents.download', [$patient, $document]))->assertRedirectToRoute('admin.login');
    }

    public function test_documents_reject_oversized_files_and_active_or_disguised_types(): void
    {
        $this->admin();
        Storage::fake('patients');
        $patient = Patient::factory()->create();

        $this->post(route('admin.patients.documents.store', $patient), ['document' => UploadedFile::fake()->create('huge.pdf', 10241, 'application/pdf')])->assertSessionHasErrors('document');
        $disguised = UploadedFile::fake()->createWithContent('bad.pdf', '<script>alert(1)</script>');
        $this->post(route('admin.patients.documents.store', $patient), ['document' => new UploadedFile($disguised->getPathname(), 'bad.pdf', 'application/pdf', null, true)])->assertSessionHasErrors('document');
        $this->post(route('admin.patients.documents.store', $patient), ['document' => UploadedFile::fake()->image('image.php')])->assertSessionHasErrors('document');

        $this->assertDatabaseCount('patient_documents', 0);
        Storage::disk('patients')->assertDirectoryEmpty('/');
    }

    private function admin(): void
    {
        config(['clinic.admin_email' => 'admin@example.test']);
        $this->withSession(['admin_authenticated' => true, 'admin_email' => 'admin@example.test']);
    }

    private function pay(Patient $patient, PatientService $service, array $overrides = []): TestResponse
    {
        return $this->post(route('admin.patients.payments.store', $patient), array_replace([
            'patient_service_id' => $service->id, 'amount' => '500.00', 'paid_on' => today()->toDateString(),
            'method' => 'cash', 'submission_key' => (string) Str::uuid(),
        ], $overrides));
    }

    private function appointment(Patient $patient, array $overrides = []): Appointment
    {
        return $patient->appointments()->create(array_replace($patient->only(['full_name', 'phone', 'email']) + [
            'service' => 'consultation', 'preferred_date' => today()->addDay()->toDateString(), 'preferred_time' => '09:00', 'status' => 'confirmed',
        ], $overrides));
    }
}
