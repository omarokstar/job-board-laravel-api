<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'employer', 'candidate'])->default('candidate');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('professional_title')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->boolean('is_premium')->default(false);
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};