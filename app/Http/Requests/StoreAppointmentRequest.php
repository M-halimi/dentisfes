<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $services = collect(config('clinic.services'))->pluck('key')->implode(',');

        return [
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,30}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'service' => ['required', 'string', 'in:'.$services],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => __('site.form.full_name'),
            'phone' => __('site.form.phone'),
            'email' => __('site.form.email'),
            'service' => __('site.form.service'),
            'preferred_date' => __('site.form.date'),
            'preferred_time' => __('site.form.time'),
            'message' => __('site.form.message'),
        ];
    }
}
