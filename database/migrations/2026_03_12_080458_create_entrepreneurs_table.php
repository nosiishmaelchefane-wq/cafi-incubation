<?php

// database/migrations/2024_01_01_000001_create_entrepreneurs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrepreneurs', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('first_name');
            $table->string('surname');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Location
            $table->string('area_of_operation')->nullable();
            
            // Business Information
            $table->string('organization_name')->nullable();
            $table->text('short_bio')->nullable();
            
            // Documents
            $table->string('tax_clearance_path')->nullable();
            $table->string('traders_license_path')->nullable();
            
            // Additional Data
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrepreneurs');
    }
};