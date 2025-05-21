<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

        public function up(): void
        {
            Schema::table('jobs', function (Blueprint $table) {
                $table->enum('education_level', [
                    'high_school',
                    'bachelor', 
                    'master',
                    'phd'
                ])->default('bachelor')->change();
                
                $table->enum('experience_level', [
                    'entry',
                    'mid',
                    'senior',
                    'executive'
                ])->default('mid')->change();
                
                $table->enum('job_level', [
                    'intern',
                    'junior',
                    'mid',
                    'senior',
                    'lead'
                ])->default('mid')->change();
            });
        }
    
        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropColumn([
                    'education_level',
                    'experience_level',
                    'job_level'
                ]);
            });
        }
};
