<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Job details
            $table->string('title');
            $table->enum('job_type', ['full-time', 'part-time', 'contract', 'freelance', 'internship']);
            $table->string('company');
            $table->string('location');
            
            // Salary information
            $table->enum('salary_type', ['range', 'fixed']);
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('salary_tax')->nullable();
            
            // Requirements
            $table->enum('education_level', ['high_school', 'bachelor', 'master', 'phd']);
            $table->enum('experience_level', ['entry', 'mid', 'senior']);
            $table->enum('job_level', ['junior', 'mid', 'senior']);
            
            // Content
            $table->text('description');
            $table->text('responsibilities');
            $table->text('benefits')->nullable();
            
            // Status and metadata
            $table->enum('status', ['draft', 'published', 'expired'])->default('draft');
            $table->string('slug')->unique();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('job_type');
            $table->index('location');
        });}

   
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
