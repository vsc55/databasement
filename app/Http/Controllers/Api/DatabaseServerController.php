<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatabaseServerResource;
use App\Http\Resources\SnapshotResource;
use App\Models\DatabaseServer;
use App\Queries\DatabaseServerQuery;
use App\Services\Backup\TriggerBackupAction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class DatabaseServerController extends Controller
{
    use AuthorizesRequests;

    /**
     * @group Database Servers
     *
     * Get all Database Servers.
     *
     * Retrieves a paginated list of database servers with optional filtering and sorting.
     *
     * @queryParam filter[name] string Filter by server name (partial match). Example: production
     * @queryParam filter[host] string Filter by host (partial match). Example: localhost
     * @queryParam filter[database_type] string Filter by database type (exact match). Example: mysql
     * @queryParam filter[description] string Filter by description (partial match). Example: main
     * @queryParam sort string Sort by field. Prefix with `-` for descending. Allowed: name, host, database_type, created_at. Example: -created_at
     * @queryParam page int Page number for pagination. Example: 1
     * @queryParam per_page int Number of items per page (max 100). Example: 15
     *
     * @response 200 scenario="Success" {
     *   "data": [
     *     {
     *       "id": "01HQ3XYZABC123DEF456GHI789",
     *       "name": "Production MySQL",
     *       "host": "db.example.com",
     *       "port": 3306,
     *       "database_type": "mysql",
     *       "database_name": "app_production",
     *       "backup_all_databases": false,
     *       "description": "Main production database",
     *       "created_at": "2024-01-15T10:30:00.000000Z",
     *       "updated_at": "2024-01-15T10:30:00.000000Z",
     *       "backup": {
     *         "id": "01HQ3XYZABC123DEF456GHI790",
     *         "recurrence": "daily",
     *         "volume_id": "01HQ3XYZABC123DEF456GHI791"
     *       }
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/database-servers?page=1",
     *     "last": "http://localhost/api/database-servers?page=1",
     *     "prev": null,
     *     "next": null
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 1,
     *     "path": "http://localhost/api/database-servers",
     *     "per_page": 15,
     *     "to": 1,
     *     "total": 1
     *   }
     * }
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $servers = DatabaseServerQuery::make()->paginate($perPage);

        return DatabaseServerResource::collection($servers);
    }

    /**
     * @group Database Servers
     *
     * Get a specific Database Server.
     *
     * Retrieves detailed information about a specific database server by ID.
     *
     * @urlParam id string required The ID of the database server. Example: 01HQ3XYZABC123DEF456GHI789
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": "01HQ3XYZABC123DEF456GHI789",
     *     "name": "Production MySQL",
     *     "host": "db.example.com",
     *     "port": 3306,
     *     "database_type": "mysql",
     *     "database_name": "app_production",
     *     "backup_all_databases": false,
     *     "description": "Main production database",
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T10:30:00.000000Z",
     *     "backup": {
     *       "id": "01HQ3XYZABC123DEF456GHI790",
     *       "recurrence": "daily",
     *       "volume_id": "01HQ3XYZABC123DEF456GHI791"
     *     }
     *   }
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "No query results for model [App\\Models\\DatabaseServer]"
     * }
     */
    public function show(DatabaseServer $databaseServer): DatabaseServerResource
    {
        $databaseServer->load(['backup.volume']);

        return new DatabaseServerResource($databaseServer);
    }

    /**
     * @group Database Servers
     *
     * Trigger a backup for a Database Server.
     *
     * Queues a backup job for the specified database server. Returns the created snapshot(s).
     *
     * @urlParam id string required The ID of the database server. Example: 01HQ3XYZABC123DEF456GHI789
     *
     * @response 202 scenario="Backup Queued" {
     *   "message": "Backup queued successfully!",
     *   "snapshots": [
     *     {
     *       "id": "01HQ3XYZABC123DEF456GHI792",
     *       "database_name": "app_production",
     *       "database_type": "mysql",
     *       "database_host": "db.example.com",
     *       "database_port": 3306,
     *       "method": "manual",
     *       "started_at": "2024-01-15T10:30:00.000000Z"
     *     }
     *   ]
     * }
     * @response 422 scenario="No Backup Configuration" {
     *   "message": "No backup configuration found for this database server."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "This action is unauthorized."
     * }
     */
    public function backup(DatabaseServer $databaseServer, TriggerBackupAction $action): JsonResponse
    {
        $databaseServer->load(['backup.volume']);

        $this->authorize('backup', $databaseServer);

        try {
            $result = $action->execute($databaseServer, auth()->id());

            return response()->json([
                'message' => $result['message'],
                'snapshots' => SnapshotResource::collection($result['snapshots']),
            ], 202);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
