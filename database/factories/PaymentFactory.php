<?php

namespace Database\Factories;

use App\Models\PatientService;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_service_id' => PatientService::factory(),
            'amount_cents' => 50000,
            'paid_on' => today()->toDateString(),
            'method' => 'cash',
            'receipt_number' => 'TEST-'.Str::ulid(),
            'submission_key' => (string) Str::uuid(),
            'recorded_by' => 'admin@example.test',
            'receipt_snapshot' => [],
        ];
    }
}
