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

        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
