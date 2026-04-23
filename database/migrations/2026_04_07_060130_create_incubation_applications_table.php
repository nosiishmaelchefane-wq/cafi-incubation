<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incubation_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->date('applied_date');
            $table->time('applied_time');
            $table->string('company_name');
            $table->string('registered_company_name')->nullable();
            $table->text('business_description')->nullable();
            $table->string('business_model')->nullable();
            $table->string('offering')->nullable();
            $table->string('revenue_model')->nullable();
            $table->string('website')->nullable();
            $table->string('company_type')->nullable();
            $table->string('industry')->nullable();
            $table->string('country')->default('Lesotho');
            $table->string('district')->nullable();
            $table->integer('year_of_establishment')->nullable();
            $table->string('company_stage')->nullable();
            $table->string('company_size')->nullable();
            $table->json('social_media')->nullable();
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_title')->nullable();
            $table->string('applicant_gender')->nullable();
            $table->string('applicant_nationality')->default('Lesotho');
            $table->string('applicant_contact_number');
            $table->text('applicant_about')->nullable();
            $table->json('applicant_social_media')->nullable();
            $table->string('received_financial_support')->nullable();
            $table->string('participated_competitions')->nullable();
            $table->boolean('willing_to_commit')->default(false);
            $table->integer('number_of_shareholders')->default(0);
            $table->integer('number_of_customers')->default(0);
            $table->decimal('average_monthly_sales', 12, 2)->default(0);
            $table->integer('jobs_to_create_12_months')->default(0);
            $table->integer('number_women_shareholders')->default(0);
            $table->integer('number_youth_shareholders')->default(0);
            $table->text('industry_other_elaboration')->nullable();
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['call_id', 'status']);
            $table->index('application_number');
            $table->index('applicant_email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incubation_applications');
    }
};