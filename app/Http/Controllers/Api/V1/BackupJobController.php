<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BackupJobResource;
use App\Models\BackupJob;
use App\Queries\BackupJobQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Jobs
 */
class BackupJobController extends Controller
{
    /**
     * List all jobs.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $jobs = BackupJobQuery::make()->paginate($perPage);

        return BackupJobResource::collection($jobs);
    }

    /**
     * Get a job.
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
