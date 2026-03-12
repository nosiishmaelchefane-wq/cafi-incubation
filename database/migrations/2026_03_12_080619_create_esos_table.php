<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_esos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esos', function (Blueprint $table) {
            $table->id();
            
            // Organisation Information
            $table->string('organisation_name');
            $table->string('website')->nullable();
            $table->string('official_email_address')->unique();
            $table->string('contact_telephone_number', 20);
            $table->text('short_bio')->nullable();
            $table->string('country', 100);
            $table->string('area_of_operation', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('area_of_focus', 255)->nullable();
            
            // Representative Information
            $table->string('representative_email', 255);
            $table->string('representative_name', 100);
            $table->string('representative_surname', 100);
            $table->string('representative_contact_number', 20);
            
            // Files/Documents
            $table->string('organisation_logo', 500)->nullable();
            $table->string('trading_license', 500)->nullable();
            $table->string('tax_clearance_certificate', 500)->nullable();
            
            // Approval Status
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esos');
    }
};