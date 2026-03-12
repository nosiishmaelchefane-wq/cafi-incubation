<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add polymorphic relationship columns
            $table->nullableMorphs('userable');
            
            // Remove old fields that are now in profiles
            $table->dropColumn(['name', 'username', 'phone', 'bio']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropMorphs('userable');
            
            $table->string('name')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
        });
    }
};