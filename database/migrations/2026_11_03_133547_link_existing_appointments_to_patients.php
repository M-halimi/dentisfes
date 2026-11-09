<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('appointments')->whereNull('patient_id')->orderBy('id')->chunkById(200, function ($appointments): void {
            foreach ($appointments as $appointment) {
                DB::transaction(function () use ($appointment): void {
                    $phone = preg_replace('/\D/', '', $appointment->phone);
                    $phone = preg_replace('/^(?:00212|212)/', '0', $phone);
                    $name = mb_strtolower(preg_replace('/\s+/u', ' ', trim($appointment->full_name)));
                    $key = hash('sha256', $name.'|'.$phone);
                    $patient = DB::table('patients')->where('identity_key', $key)->first();
                    $patientId = $patient?->id ?? DB::table('patients')->insertGetId([
                        'reference' => 'PAT-'.Str::ulid(), 'identity_key' => $key,
                        'full_name' => $appointment->full_name, 'phone' => $appointment->phone,
                        'email' => $appointment->email, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                    DB::table('appointments')->where('id', $appointment->id)->whereNull('patient_id')->update(['patient_id' => $patientId]);
                });
            }
        });
    }

    public function down(): void
    {
        // Patient identities and historical appointments must survive a data rollback.
    }
};
