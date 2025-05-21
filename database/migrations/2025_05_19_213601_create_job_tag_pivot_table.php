<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_tag', function (Blueprint $table) {
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['job_id', 'tag_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_tag');
    }
};