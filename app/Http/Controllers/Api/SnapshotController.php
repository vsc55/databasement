<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SnapshotResource;
use App\Models\Snapshot;
use App\Queries\SnapshotQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SnapshotController extends Controller
{
    /**
     * @group Snapshots
     *
     * Get all Snapshots.
     *
     * Retrieves a paginated list of backup snapshots with optional filtering and sorting.
     *
     * @queryParam filter[database_name] string Filter by database name (partial match). Example: production
     * @queryParam filter[database_host] string Filter by database host (partial match). Example: localhost
     * @queryParam filter[database_type] string Filter by database type (exact match). Example: mysql
     * @queryParam filter[method] string Filter by backup method (exact match). Example: manual
     * @queryParam filter[status] string Filter by job status (pending, running, completed, failed). Example: completed
     * @queryParam filter[search] string Search by server name, database name, host, or path. Example: production
     * @queryParam sort string Sort by field. Prefix with `-` for descending. Allowed: started_at, created_at, file_size, database_name. Example: -started_at
     * @queryParam page int Page number for pagination. Example: 1
     * @queryParam per_page int Number of items per page (max 100). Example: 15
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $snapshots = SnapshotQuery::make()->paginate($perPage);

        return SnapshotResource::collection($snapshots);
    }

    /**
     * @group Snapshots
     *
     * Get a specific Snapshot.
     *
     * Retrieves detailed information about a specific backup snapshot by ID.
     *
     * @urlParam id string required The ID of the snapshot. Example: 01HQ3XYZABC123DEF456GHI789
     */
    public function show(Snapshot $snapshot): SnapshotResource
    {
        $snapshot->load(['databaseServer', 'backup', 'volume', 'triggeredBy', 'job']);

        return new SnapshotResource($snapshot);
    }
}
