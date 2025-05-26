<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->enum('job_type', ['full-time', 'part-time', 'contract', 'temporary', 'internship', 'remote']);
            $table->string('company');
            $table->string('location');
            $table->string('salary')->nullable();
            $table->enum('salary_type', ['range', 'fixed'])->nullable();
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->enum('education_level', ['high_school', 'bachelor', 'master', 'phd'])->default('bachelor');
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'executive'])->default('mid');
            $table->enum('job_level', ['intern', 'junior', 'mid', 'senior', 'lead'])->default('mid');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};