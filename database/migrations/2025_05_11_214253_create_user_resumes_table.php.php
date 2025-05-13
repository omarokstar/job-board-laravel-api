<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserResumesTable extends Migration
{
    public function up()
    {
        Schema::create('user_resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');        
            $table->string('path');         
            $table->string('size');         
            $table->string('extension'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_resumes');
    }
}
