<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_additional_fields_to_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            // Add profile fields
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('profile_photo')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('profile_photo');
            
            // Account status fields
            $table->boolean('is_active')->default(true)->after('bio');
            $table->boolean('is_suspended')->default(false)->after('is_active');
            $table->timestamp('suspended_until')->nullable()->after('is_suspended');
            $table->string('suspension_reason')->nullable()->after('suspended_until');
            
            // Last activity tracking
            $table->timestamp('last_login_at')->nullable()->after('suspension_reason');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            
            // User metadata
            $table->json('metadata')->nullable()->after('last_login_ip');
            $table->string('timezone')->default('UTC')->after('metadata');
            $table->string('language')->default('en')->after('timezone');
            
            // For email verification (if using custom verification)
            $table->string('verification_token')->nullable()->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'profile_photo',
                'bio',
                'is_active',
                'is_suspended',
                'suspended_until',
                'suspension_reason',
                'last_login_at',
                'last_login_ip',
                'metadata',
                'timezone',
                'language',
                'verification_token',
            ]);
        });
    }
};