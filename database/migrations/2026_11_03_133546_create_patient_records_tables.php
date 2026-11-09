<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table): void {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->string('identity_key', 64)->unique();
            $table->string('full_name', 120);
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::table('appointments', function (Blueprint $table): void {
            $table->foreignId('patient_id')->nullable()->constrained()->restrictOnDelete();
            $table->index(['preferred_date', 'preferred_time']);
        });
        Schema::create('patient_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->string('label', 160);
            $table->date('performed_on');
            $table->unsignedBigInteger('price_cents');
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_service_id')->constrained()->restrictOnDelete();
            $table->string('receipt_number', 40)->unique();
            $table->uuid('submission_key')->unique();
            $table->unsignedBigInteger('amount_cents');
            $table->date('paid_on')->index();
            $table->string('method', 20);
            $table->string('reference', 120)->nullable();
            $table->string('recorded_by');
            $table->json('receipt_snapshot');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('payment_audits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained()->restrictOnDelete();
            $table->string('action', 20);
            $table->string('actor');
            $table->text('reason')->nullable();
            $table->json('snapshot');
            $table->timestamps();
        });
        Schema::create('patient_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->text('body');
            $table->string('author');
            $table->timestamps();
        });
        Schema::create('patient_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('uploaded_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_documents');
        Schema::dropIfExists('patient_notes');
        Schema::dropIfExists('payment_audits');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('patient_services');
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('patient_id');
            $table->dropIndex(['preferred_date', 'preferred_time']);
        });
        Schema::dropIfExists('patients');
    }
};
