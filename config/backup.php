<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Working Directory
    |--------------------------------------------------------------------------
    |
    | The temporary directory used for backup and restore operations.
    | Files are stored here temporarily before being transferred to
    | the final storage volume.
    |
    */

    'working_directory' => env('BACKUP_WORKING_DIRECTORY', '/tmp/backups'),

    /*
    |--------------------------------------------------------------------------
    | MySQL CLI Type
    |--------------------------------------------------------------------------
    |
    | The type of MySQL CLI to use for backup and restore operations.
    | Options: 'mariadb' (default) or 'mysql'
    |
    */

    'mysql_cli_type' => env('MYSQL_CLI_TYPE', 'mariadb'),

    /*
    |--------------------------------------------------------------------------
    | Daily Backup Schedule
    |--------------------------------------------------------------------------
    |
    | The cron expression for when daily backups should run.
    | Default: '0 2 * * *' (every day at 2:00 AM)
    |
    */

    'daily_cron' => env('BACKUP_DAILY_CRON', '0 2 * * *'),

    /*
    |--------------------------------------------------------------------------
    | Weekly Backup Schedule
    |--------------------------------------------------------------------------
    |
    | The cron expression for when weekly backups should run.
    | Default: '0 3 * * 0' (every Sunday at 3:00 AM)
    |
    */

    'weekly_cron' => env('BACKUP_WEEKLY_CRON', '0 3 * * 0'),

    /*
    |--------------------------------------------------------------------------
    | Snapshot Cleanup Schedule
    |--------------------------------------------------------------------------
    |
    | The cron expression for when expired snapshots should be cleaned up.
    | Default: '0 4 * * *' (every day at 4:00 AM)
    |
    */

    'cleanup_cron' => env('BACKUP_CLEANUP_CRON', '0 4 * * *'),
];
