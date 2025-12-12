<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VolumeResource;
use App\Models\Volume;
use App\Queries\VolumeQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VolumeController extends Controller
{
    /**
     * @group Volumes
     *
     * Get all Volumes.
     *
     * Retrieves a paginated list of storage volumes with optional filtering and sorting.
     *
     * @queryParam filter[name] string Filter by volume name (partial match). Example: backups
     * @queryParam filter[type] string Filter by volume type (exact match: local, s3). Example: s3
     * @queryParam filter[search] string Search by name or type. Example: production
     * @queryParam sort string Sort by field. Prefix with `-` for descending. Allowed: name, type, created_at. Example: -created_at
     * @queryParam page int Page number for pagination. Example: 1
     * @queryParam per_page int Number of items per page (max 100). Example: 15
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $volumes = VolumeQuery::make()->paginate($perPage);

        return VolumeResource::collection($volumes);
    }

    /**
     * @group Volumes
     *
     * Get a specific Volume.
     *
     * Retrieves detailed information about a specific storage volume by ID.
     *
     * @urlParam id string required The ID of the volume. Example: 01HQ3XYZABC123DEF456GHI789
     */
    public function show(Volume $volume): VolumeResource
    {
        return new VolumeResource($volume);
    }
}
