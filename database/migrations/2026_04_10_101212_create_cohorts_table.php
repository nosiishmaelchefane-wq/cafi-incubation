<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->integer('cohort_number')->unique();
            $table->string('name');
            $table->integer('year');
            $table->integer('duration_months')->default(6);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('target_enterprises')->nullable();
            $table->string('status')->default('Draft');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};