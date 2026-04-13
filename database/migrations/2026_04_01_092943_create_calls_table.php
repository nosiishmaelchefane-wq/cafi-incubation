<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('title');
            $table->string('cohort');
            $table->text('description');
            $table->text('details')->nullable();
            
            // Eligibility
            $table->text('eligibility');
            $table->json('sectors')->nullable();
            $table->string('geography')->nullable();
            
            // Dates
            $table->date('publish_date')->nullable();
            $table->date('open_date');
            $table->date('close_date');
            $table->integer('duration_months')->default(6);
            $table->boolean('allow_late_submissions')->default(false);
            
            // Status & Tracking
            $table->string('status')->default('draft');
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            
            // Stats
            $table->integer('applications_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('cohort');
            $table->index('open_date');
            $table->index('close_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};