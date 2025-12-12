<?php

use App\Http\Controllers\Api\BackupJobController;
use App\Http\Controllers\Api\DatabaseServerController;
use App\Http\Controllers\Api\SnapshotController;
use App\Http\Controllers\Api\VolumeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('database-servers', DatabaseServerController::class)
        ->only(['index', 'show']);
    Route::post('database-servers/{database_server}/backup', [DatabaseServerController::class, 'backup'])
        ->name('database-servers.backup');

    Route::apiResource('jobs', BackupJobController::class)
        ->only(['index', 'show'])
        ->parameters(['jobs' => 'backupJob']);

    Route::apiResource('snapshots', SnapshotController::class)
        ->only(['index', 'show']);

    Route::apiResource('volumes', VolumeController::class)
        ->only(['index', 'show']);
});
