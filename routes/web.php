<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientDocumentController;
use App\Http\Controllers\PatientPaymentController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClinicController::class, 'home'])->name('home');
Route::get('/a-propos', [ClinicController::class, 'about'])->name('about');
Route::get('/services', [ClinicController::class, 'services'])->name('services');
Route::get('/rendez-vous', [ClinicController::class, 'appointment'])->name('appointment');
Route::post('/rendez-vous', [AppointmentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('appointments.store');
Route::get('/contact', [ClinicController::class, 'contact'])->name('contact');
Route::get('/mentions-legales', [ClinicController::class, 'legal'])->name('legal');
Route::get('/confidentialite', [ClinicController::class, 'privacy'])->name('privacy');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::get('/langue/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['fr', 'ar', 'en'], true), 404);

    session(['locale' => $locale]);
    app()->setLocale($locale);

    $previousUrl = url()->previous();
    $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?? '/';
    parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?? '', $previousQuery);

    if (str_starts_with($previousPath, '/admin') && ! array_key_exists('lang', $previousQuery)) {
        return back();
    }

    return redirect()->to(url()->query($previousUrl, ['lang' => $locale]));
})->name('language.switch');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/connexion', [AdminController::class, 'showLogin'])->name('login');
    Route::get('/login', fn (): RedirectResponse => to_route('admin.login'))->name('login.alias');
    Route::post('/connexion', [AdminController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::middleware([AdminAuth::class, 'can:manage-patients'])->group(function (): void {
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/pdf', [PatientController::class, 'export'])->name('patients.pdf');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::post('/patients/{patient}/rendez-vous', [PatientController::class, 'storeAppointment'])->name('patients.appointments.store');
        Route::patch('/patients/{patient}/rendez-vous/{appointment}', [PatientController::class, 'updateAppointment'])->name('patients.appointments.update');
        Route::post('/patients/{patient}/notes', [PatientController::class, 'storeNote'])->middleware('can:patient-notes')->name('patients.notes.store');
        Route::post('/patients/{patient}/prestations', [PatientController::class, 'storeService'])->name('patients.services.store');
        Route::get('/paiements', [PatientPaymentController::class, 'index'])->name('payments.index');
        Route::post('/patients/{patient}/paiements', [PatientPaymentController::class, 'store'])->name('patients.payments.store');
        Route::post('/patients/{patient}/paiements/{payment}/annulation', [PatientPaymentController::class, 'cancel'])->name('patients.payments.cancel');
        Route::get('/patients/{patient}/paiements/{payment}/recu', [PatientPaymentController::class, 'receipt'])->name('patients.payments.receipt');
        Route::post('/patients/{patient}/documents', [PatientDocumentController::class, 'store'])->name('patients.documents.store');
        Route::get('/patients/{patient}/documents/{document}', [PatientDocumentController::class, 'show'])->name('patients.documents.show');
        Route::get('/patients/{patient}/documents/{document}/telecharger', [PatientDocumentController::class, 'download'])->name('patients.documents.download');
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/parametres', [AdminController::class, 'settings'])->name('settings');
        Route::put('/parametres', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/rendez-vous/export', [AdminController::class, 'exportAppointments'])->name('appointments.export');
        Route::post('/deconnexion', [AdminController::class, 'logout'])->name('logout');
        Route::patch('/rendez-vous/{appointment}/statut', [AdminController::class, 'updateStatus'])->name('appointments.status');
        Route::delete('/rendez-vous/{appointment}', [AdminController::class, 'destroy'])->name('appointments.destroy');
    });
});
