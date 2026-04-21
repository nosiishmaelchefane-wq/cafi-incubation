<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            $table->date('open_date');
            $table->date('close_date');
            $table->enum('status', ['draft', 'active', 'expired'])->default('draft');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['call_id', 'status']);
            $table->index('open_date');
            $table->index('close_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_windows');
    }
};