<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('snapshots', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('method');
        });

        // Migrate existing data to metadata
        $snapshots = DB::table('snapshots')
            ->join('database_servers', 'snapshots.database_server_id', '=', 'database_servers.id')
            ->join('volumes', 'snapshots.volume_id', '=', 'volumes.id')
            ->select([
                'snapshots.id',
                'snapshots.database_name',
                'snapshots.database_host',
                'snapshots.database_port',
                'database_servers.username',
                'volumes.type as volume_type',
                'volumes.config as volume_config',
            ])
            ->get();

        foreach ($snapshots as $snapshot) {
            $metadata = [
                'database_server' => [
                    'host' => $snapshot->database_host,
                    'port' => $snapshot->database_port,
                    'username' => $snapshot->username,
                    'database_name' => $snapshot->database_name,
                ],
                'volume' => [
                    'type' => $snapshot->volume_type,
                    'config' => json_decode($snapshot->volume_config, true),
                ],
            ];

            DB::table('snapshots')
                ->where('id', $snapshot->id)
                ->update(['metadata' => json_encode($metadata)]);
        }

        Schema::table('snapshots', function (Blueprint $table) {
            $table->dropColumn(['database_host', 'database_port']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snapshots', function (Blueprint $table) {
            $table->string('database_host')->nullable()->after('database_type');
            $table->integer('database_port')->nullable()->after('database_host');
        });

        // Restore data from metadata
        $snapshots = DB::table('snapshots')->whereNotNull('metadata')->get();

        foreach ($snapshots as $snapshot) {
            $metadata = json_decode($snapshot->metadata, true);
            $databaseServer = $metadata['database_server'] ?? [];

            DB::table('snapshots')
                ->where('id', $snapshot->id)
                ->update([
                    'database_host' => $databaseServer['host'] ?? null,
                    'database_port' => $databaseServer['port'] ?? null,
                ]);
        }

        Schema::table('snapshots', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
