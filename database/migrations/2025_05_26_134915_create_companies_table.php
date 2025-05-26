<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('company_name');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('about')->nullable();
            $table->string('organization_type')->nullable();
            $table->date('establishment_year')->nullable();
            $table->text('company_vision')->nullable();
            $table->string('industry_type')->nullable();
            $table->string('team_size')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('github')->nullable();
            $table->string('facebook')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};