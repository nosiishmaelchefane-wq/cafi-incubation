<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('screenings', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('application_id')->constrained('incubation_applications')->onDelete('cascade');
            $table->foreignId('call_id')->constrained('calls')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Status fields
            $table->enum('status', ['pending', 'in_review', 'eligible', 'rejected'])->default('pending');
            
            // Screening notes
            $table->text('screening_notes')->nullable();
            
            // Rejection fields
            $table->text('rejection_reason')->nullable();
            $table->string('rejection_category')->nullable();
            $table->text('rejection_details')->nullable();
            
            // Eligibility checklist (JSON storage)
            $table->json('eligibility_checklist')->nullable();
            
            // Screening metadata
            $table->timestamp('screened_at')->nullable();
            $table->foreignId('screened_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps and soft delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('status');
            $table->index('application_id');
            $table->index('call_id');
            $table->index('user_id');
            $table->index('screened_by');
            $table->index(['status', 'created_at']);
            $table->index(['application_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};