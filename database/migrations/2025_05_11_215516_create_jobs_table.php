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
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->enum('job_type', ['Fulltime', 'Part-time', 'Contract', 'Internship', 'Remotely']);
            $table->string('company');
            $table->string('location');
            $table->string('salary')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->string('salary_type');        // 'annual', 'monthly', etc.
            $table->decimal('min_salary', 10, 2); // For salary ranges
            $table->decimal('max_salary', 10, 2);
            $table->string('education_level');    // 'Bachelor', 'Master', etc.
            $table->string('experience_level');   // 'Entry', 'Mid', 'Senior'
            $table->string('job_level'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
