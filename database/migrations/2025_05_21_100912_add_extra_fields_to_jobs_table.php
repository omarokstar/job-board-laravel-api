<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToJobsTable extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            // New columns to add
            // $table->text('rejection_reason')->nullable()->after('status');
            
            // Optional additional fields from your initial full schema
            $table->enum('salary_type', ['range', 'fixed'])->nullable()->after('salary');
            $table->decimal('min_salary', 10, 2)->nullable()->after('salary_type');
            $table->decimal('max_salary', 10, 2)->nullable()->after('min_salary');
            $table->string('salary_tax')->nullable()->after('max_salary');

            $table->enum('education_level', ['high_school', 'bachelor', 'master', 'phd'])->nullable()->after('company');
            $table->enum('experience_level', ['entry', 'mid', 'senior'])->nullable()->after('education_level');
            $table->enum('job_level', ['junior', 'mid', 'senior'])->nullable()->after('experience_level');

            $table->text('responsibilities')->nullable()->after('description');
            $table->text('benefits')->nullable()->after('responsibilities');

            $table->string('slug')->unique()->nullable()->after('title');
            $table->timestamp('published_at')->nullable()->after('slug');
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'salary_type',
                'min_salary',
                'max_salary',
                'salary_tax',
                'education_level',
                'experience_level',
                'job_level',
                'responsibilities',
                'benefits',
                'slug',
                'published_at',
            ]);
        });
    }
}
