<?php
// database/migrations/2014_10_12_000000_create_users_table.php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Authentication fields
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            
            // Profile fields
            $table->string('username')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('profile_photo')->nullable();
            $table->text('bio')->nullable();
            
            // Polymorphic relationship fields
            $table->string('userable_type')->nullable();
            $table->unsignedBigInteger('userable_id')->nullable();
            $table->index(['userable_type', 'userable_id']);
            
            // Account status fields
            $table->boolean('is_active')->default(true);
            $table->boolean('is_suspended')->default(false);
            $table->timestamp('suspended_until')->nullable();
            $table->text('suspension_reason')->nullable();
            
            // Login tracking
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            // User preferences and metadata
            $table->json('metadata')->nullable();
            $table->string('timezone')->nullable();
            $table->string('language')->default('en');
            $table->string('verification_token')->nullable();
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};