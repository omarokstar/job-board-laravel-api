<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up(): void
{
    Schema::table('jobs', function (Blueprint $table) {
        // 1. First check if columns exist and rename them if they do
        if (Schema::hasColumn('jobs', 'education_level')) {
            $table->renameColumn('education_level', 'old_education_level');
        }
        if (Schema::hasColumn('jobs', 'experience_level')) {
            $table->renameColumn('experience_level', 'old_experience_level');
        }
        if (Schema::hasColumn('jobs', 'job_level')) {
            $table->renameColumn('job_level', 'old_job_level');
        }

        // 2. Add new enum columns with desired values
        $table->enum('education_level', ['high_school', 'bachelor', 'master', 'phd'])
              ->default('bachelor');
        $table->enum('experience_level', ['entry', 'mid', 'senior', 'executive'])
              ->default('mid');
        $table->enum('job_level', ['intern', 'junior', 'mid', 'senior', 'lead'])
              ->default('mid');
    });

    // 3. Migrate data from old columns to new ones with fallbacks
    if (Schema::hasColumn('jobs', 'old_education_level')) {
        DB::table('jobs')->update([
            'education_level' => DB::raw("CASE 
                WHEN old_education_level IN ('high_school', 'bachelor', 'master', 'phd') THEN old_education_level
                ELSE 'bachelor' END"),
            'experience_level' => DB::raw("CASE 
                WHEN old_experience_level IN ('entry', 'mid', 'senior', 'executive') THEN old_experience_level
                ELSE 'mid' END"),
            'job_level' => DB::raw("CASE 
                WHEN old_job_level IN ('intern', 'junior', 'mid', 'senior', 'lead') THEN old_job_level
                ELSE 'mid' END"),
        ]);
    }

    // 4. Drop the old columns if they exist
    Schema::table('jobs', function (Blueprint $table) {
        if (Schema::hasColumn('jobs', 'old_education_level')) {
            $table->dropColumn('old_education_level');
        }
        if (Schema::hasColumn('jobs', 'old_experience_level')) {
            $table->dropColumn('old_experience_level');
        }
        if (Schema::hasColumn('jobs', 'old_job_level')) {
            $table->dropColumn('old_job_level');
        }
    });
}

public function down(): void
{
    Schema::table('jobs', function (Blueprint $table) {
        // 1. First rename current columns to temporary names
        $table->renameColumn('education_level', 'new_education_level');
        $table->renameColumn('experience_level', 'new_experience_level');
        $table->renameColumn('job_level', 'new_job_level');

        // 2. Recreate original columns (adjust values if you know them)
        $table->enum('education_level', ['original_value1', 'original_value2'])->default('original_default');
        $table->enum('experience_level', ['original_value1', 'original_value2'])->default('original_default');
        $table->enum('job_level', ['original_value1', 'original_value2'])->default('original_default');
    });

    // 3. Copy data back from new columns to original ones
    DB::table('jobs')->update([
        'education_level' => DB::raw('new_education_level'),
        'experience_level' => DB::raw('new_experience_level'),
        'job_level' => DB::raw('new_job_level'),
    ]);

    // 4. Drop the temporary columns
    Schema::table('jobs', function (Blueprint $table) {
        $table->dropColumn(['new_education_level', 'new_experience_level', 'new_job_level']);
    });
}
};
