<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up()
    {
        // First make column nullable temporarily
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_type')->nullable()->change();
        });

        // Convert existing data with exact case matching
        DB::table('jobs')
            ->where('job_type', 'Fulltime')
            ->update(['job_type' => 'full-time']);
            
        DB::table('jobs')
            ->where('job_type', 'Part-time')
            ->update(['job_type' => 'part-time']);
            
        DB::table('jobs')
            ->where('job_type', 'Internship')
            ->update(['job_type' => 'internship']);

        // Now change to enum with proper values
        Schema::table('jobs', function (Blueprint $table) {
            $table->enum('job_type', [
                'full-time', 
                'part-time', 
                'contract', 
                'temporary', 
                'internship', 
                'remote'
            ])->nullable(false)->change();
        });
    }

    public function down()
    {
        // First make column nullable temporarily
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_type')->nullable()->change();
        });

        // Revert data to original format
        DB::table('jobs')
            ->where('job_type', 'full-time')
            ->update(['job_type' => 'Fulfilme']);
            
        DB::table('jobs')
            ->where('job_type', 'part-time')
            ->update(['job_type' => 'Part-time']);
            
        DB::table('jobs')
            ->where('job_type', 'internship')
            ->update(['job_type' => 'Internship']);

        // Revert column to original enum
        Schema::table('jobs', function (Blueprint $table) {
            $table->enum('job_type', [
                'Fulfilme',
                'Part-time',
                'Contract',
                'Internship',
                'Remotely'
            ])->nullable(false)->change();
        });
    }
};
