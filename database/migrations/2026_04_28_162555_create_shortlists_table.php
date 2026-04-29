<?php
// database/migrations/xxxx_xx_xx_create_shortlists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shortlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('applications_count')->default(0);
            $table->enum('status', ['draft', 'confirmed'])->default('draft');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->unique('call_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shortlists');
    }
};