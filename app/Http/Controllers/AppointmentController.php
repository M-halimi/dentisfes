<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        DB::transaction(fn () => Appointment::create($request->validated() + ['status' => 'pending']));

        return to_route('appointment')->with('success', __('site.appointment_success'));
    }
}
