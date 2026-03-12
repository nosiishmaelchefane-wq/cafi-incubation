<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esos', function (Blueprint $table) {
            $table->id();
            
            $table->string('organisation_name');
            $table->string('website')->nullable();
            $table->string('official_email_address');
            $table->string('contact_telephone_number');
            $table->text('short_bio')->nullable();
            $table->string('country');
            $table->string('area_of_operation')->nullable();
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