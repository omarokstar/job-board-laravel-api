<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnrelatedColumnsFromJobsTable extends Migration
{
    public function up()
    {
            Schema::disableForeignKeyConstraints();

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'published_at',
                'salary_tax',
                'benefits',
                'new_education_level',
                'new_experience_level',
                'new_job_level',
                'education_level_temp',
                'experience_level_temp',
                'job_level_temp',
            ]);
        });
            Schema::enableForeignKeyConstraints();

    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->decimal('salary_tax', 10, 2)->nullable();
            $table->text('benefits')->nullable();
            $table->string('new_education_level')->nullable();
            $table->string('new_experience_level')->nullable();
            $table->string('new_job_level')->nullable();
            $table->string('education_level_temp')->nullable();
            $table->string('experience_level_temp')->nullable();
            $table->string('job_level_temp')->nullable();
        });
    }
}
