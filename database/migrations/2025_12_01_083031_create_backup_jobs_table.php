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
        // Truncate tables to avoid foreign key issues
        DB::table('restores')->truncate();
        DB::table('snapshots')->truncate();

        // Create backup_jobs table
        Schema::create('backup_jobs', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // References - only one should be set
            $table->foreignUlid('snapshot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUlid('restore_id')->nullable()->constrained()->cascadeOnDelete();

            // Laravel queue job ID
            $table->string('job_id')->nullable();

            // Job Execution
            $table->enum('status', ['pending', 'queued', 'running', 'completed', 'failed']);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Error Tracking
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();

            // Logging
            $table->json('logs')->nullable();

            $table->timestamps();
        });

        // Remove job-related columns from snapshots
        Schema::table('snapshots', function (Blueprint $table) {
            $table->dropColumn([
                'job_id',
                'status',
                'completed_at',
                'error_message',
                'error_trace',
                'logs',
            ]);
        });

        // Remove job-related columns from restores
        Schema::table('restores', function (Blueprint $table) {
            $table->dropColumn([
                'job_id',
                'status',
                'started_at',
                'completed_at',
                'error_message',
                'error_trace',
                'logs',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns to snapshots
        Schema::table('snapshots', function (Blueprint $table) {
            $table->string('job_id')->nullable()->after('volume_id');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->after('completed_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->text('error_message')->nullable()->after('status');
            $table->text('error_trace')->nullable()->after('error_message');
            $table->json('logs')->nullable()->after('triggered_by_user_id');
        });

        // Re-add columns to restores
        Schema::table('restores', function (Blueprint $table) {
            $table->string('job_id')->nullable()->after('schema_name');
            $table->timestamp('started_at')->nullable()->after('job_id');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued')->after('completed_at');
            $table->text('error_message')->nullable()->after('status');
            $table->text('error_trace')->nullable()->after('error_message');
            $table->json('logs')->nullable()->after('triggered_by_user_id');
        });

        // Drop backup_jobs table
        Schema::dropIfExists('backup_jobs');
    }
};
