<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClinicController;
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

Route::get('/langue/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['fr', 'ar', 'en'], true), 404);

    session(['locale' => $locale]);

    return back();
})->name('language.switch');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/connexion', [AdminController::class, 'showLogin'])->name('login');
    Route::get('/login', fn (): RedirectResponse => to_route('admin.login'))->name('login.alias');
    Route::post('/connexion', [AdminController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::middleware(AdminAuth::class)->group(function (): void {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/parametres', [AdminController::class, 'settings'])->name('settings');
        Route::put('/parametres', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/rendez-vous/export', [AdminController::class, 'exportAppointments'])->name('appointments.export');
        Route::post('/deconnexion', [AdminController::class, 'logout'])->name('logout');
        Route::patch('/rendez-vous/{appointment}/statut', [AdminController::class, 'updateStatus'])->name('appointments.status');
        Route::delete('/rendez-vous/{appointment}', [AdminController::class, 'destroy'])->name('appointments.destroy');
    });
});
