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
        // Add new JSON column
        Schema::table('database_servers', function (Blueprint $table) {
            $table->json('database_names')->nullable()->after('database_type');
        });

        // Migrate existing data: convert single database_name to array
        DB::table('database_servers')->whereNotNull('database_name')->orderBy('id')->each(function ($server) {
            DB::table('database_servers')
                ->where('id', $server->id)
                ->update(['database_names' => json_encode([$server->database_name])]);
        });

        // Drop old column
        Schema::table('database_servers', function (Blueprint $table) {
            $table->dropColumn('database_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the old column
        Schema::table('database_servers', function (Blueprint $table) {
            $table->string('database_name')->nullable()->after('database_type');
        });

        // Migrate data back: take first database from array
        DB::table('database_servers')->whereNotNull('database_names')->orderBy('id')->each(function ($server) {
            $databases = json_decode($server->database_names, true);
            $firstDatabase = is_array($databases) && count($databases) > 0 ? $databases[0] : null;

            DB::table('database_servers')
                ->where('id', $server->id)
                ->update(['database_name' => $firstDatabase]);
        });

        // Drop JSON column
        Schema::table('database_servers', function (Blueprint $table) {
            $table->dropColumn('database_names');
        });
    }
};
