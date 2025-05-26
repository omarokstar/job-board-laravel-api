<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->primary(['job_id', 'tag_id']);
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_tag');
    }
};