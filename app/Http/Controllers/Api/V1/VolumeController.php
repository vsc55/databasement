<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VolumeResource;
use App\Models\Volume;
use App\Queries\VolumeQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Volumes
 */
class VolumeController extends Controller
{
    /**
     * List all volumes.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $volumes = VolumeQuery::make()->paginate($perPage);

        return VolumeResource::collection($volumes);
    }

    /**
     * Get a volume.
     */
    public function show(Volume $volume): VolumeResource
    {
        return new VolumeResource($volume);
    }
}
