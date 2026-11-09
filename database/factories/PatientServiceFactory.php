<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientService>
 */
class PatientServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'label' => 'Prestation',
            'performed_on' => today()->toDateString(),
            'price_cents' => 150000,
        ];
    }
}
