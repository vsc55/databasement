<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BackupJobResource;
use App\Models\BackupJob;
use App\Queries\BackupJobQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BackupJobController extends Controller
{
    /**
     * @group Jobs
     *
     * Get all Jobs.
     *
     * Retrieves a paginated list of backup and restore jobs with optional filtering and sorting.
     *
     * @queryParam filter[status] string Filter by status (pending, queued, running, completed, failed). Example: completed
     * @queryParam filter[type] string Filter by job type (backup, restore). Example: backup
     * @queryParam filter[search] string Search by server name, database name, or host. Example: production
     * @queryParam sort string Sort by field. Prefix with `-` for descending. Allowed: created_at, started_at, completed_at, status. Example: -created_at
     * @queryParam page int Page number for pagination. Example: 1
     * @queryParam per_page int Number of items per page (max 100). Example: 15
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $jobs = BackupJobQuery::make()->paginate($perPage);

        return BackupJobResource::collection($jobs);
    }

    /**
     * @group Jobs
     *
     * Get a specific Job.
     *
     * Retrieves detailed information about a specific backup or restore job by ID.
     *
     * @urlParam id string required The ID of the job. Example: 01HQ3XYZABC123DEF456GHI789
     */
    public function show(BackupJob $backupJob): BackupJobResource
    {
        $backupJob->load([
            'snapshot.databaseServer',
            'snapshot.triggeredBy',
            'restore.snapshot.databaseServer',
            'restore.targetServer',
            'restore.triggeredBy',
        ]);

        return new BackupJobResource($backupJob);
    }
}
