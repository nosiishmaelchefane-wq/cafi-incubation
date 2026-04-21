<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_evaluation_scores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('incubation_applications')->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            
            // Section 1: Innovation and Creativity (15 points)
            $table->integer('innovation_uniqueness')->default(0); // 0-10
            $table->integer('innovation_development')->default(0); // 0-5
            
            // Section 2: Commercial Feasibility (25 points)
            $table->integer('commercial_vision')->default(0); // 0-10
            $table->integer('commercial_disruption')->default(0); // 0-10
            $table->integer('commercial_market_size')->default(0); // 0-5
            
            // Section 3: Team Capability (30 points)
            $table->integer('team_experience')->default(0); // 0-6
            $table->integer('team_diversity')->default(0); // 0-6
            $table->integer('team_size')->default(0); // 0-10
            $table->boolean('team_women_shareholders')->default(false); // 0 or 4 points
            $table->boolean('team_youth_shareholders')->default(false); // 0 or 4 points
            
            // Section 4: Operation Survival (20 points)
            $table->integer('operation_sustainability')->default(0); // 0,5,10,15,20
            
            // Section 5: Social & Environmental Safeguards (10 points)
            $table->integer('social_safeguards')->default(0); // 0-5
            $table->integer('social_risk_mitigation')->default(0); // 0-5
            
            // Total score
            $table->integer('total_score')->default(0);
            
            // Comments and metadata
            $table->text('evaluator_comments')->nullable();
            // Status
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // One score per application per evaluator
            $table->unique(['application_id', 'evaluator_id']);
            
            // Indexes for faster queries
            $table->index(['call_id', 'evaluator_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_scores');
    }
};