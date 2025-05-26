<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_skill', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('skill_id');
            $table->timestamps();

            $table->primary(['job_id', 'skill_id']);
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_skill');
    }
};