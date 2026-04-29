<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incubation_applications', function (Blueprint $table) {
            $table->timestamp('pitch_scheduled_at')->nullable();
            $table->integer('pitch_score')->nullable();
            $table->text('pitch_comments')->nullable();
        });
    }

    public function down()
    {
        Schema::table('incubation_applications', function (Blueprint $table) {
            $table->dropColumn([
                'pitch_scheduled_at',
                'pitch_score',
                'pitch_comments'
            ]);
        });
    }
};