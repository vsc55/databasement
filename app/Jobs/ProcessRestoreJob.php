<?php

namespace App\Jobs;

use App\Models\DatabaseServer;
use App\Models\Snapshot;
use App\Services\Backup\RestoreTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRestoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * Set to 1 (no retries) because restore operations might have already
     * partially modified the target database.
     */
    public int $tries = 1;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 3600; // 1 hour

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $snapshotId,
        public string $targetServerId,
        public string $schemaName,
        public string $method = 'manual',
        public ?string $userId = null
    ) {
        $this->onQueue('backups');
    }

    /**
     * Execute the job.
     */
    public function handle(RestoreTask $restoreTask): void
    {
        // Fetch the snapshot and target server with relationships
        $snapshot = Snapshot::with(['volume'])->findOrFail($this->snapshotId);
        $targetServer = DatabaseServer::findOrFail($this->targetServerId);

        // Run the restore task (it will create restore and job, handling status updates)
        $restoreTask->run(
            targetServer: $targetServer,
            snapshot: $snapshot,
            schemaName: $this->schemaName,
            method: $this->method,
            userId: $this->userId
        );

        Log::info('Restore completed successfully', [
            'snapshot_id' => $this->snapshotId,
            'target_server_id' => $this->targetServerId,
            'schema_name' => $this->schemaName,
            'method' => $this->method,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Restore job failed', [
            'snapshot_id' => $this->snapshotId,
            'target_server_id' => $this->targetServerId,
            'schema_name' => $this->schemaName,
            'method' => $this->method,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
