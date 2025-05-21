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
<<<<<<< HEAD
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Job details
=======
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
>>>>>>> d2dfc3eb8fcf1dd1cfc5cd2938b9f11555759f29
            $table->string('title');
            $table->enum('job_type', ['full-time', 'part-time', 'contract', 'freelance', 'internship']);
            $table->string('company');
            $table->string('location');
<<<<<<< HEAD
            
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
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('slug')->unique();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('job_type');
            $table->index('location');
        });}
=======
            $table->string('salary')->nullable();
            $table->text('description')->nullable();
        });
    }
>>>>>>> d2dfc3eb8fcf1dd1cfc5cd2938b9f11555759f29

   
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
