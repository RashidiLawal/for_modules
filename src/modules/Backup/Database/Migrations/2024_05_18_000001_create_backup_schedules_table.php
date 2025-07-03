<?php

declare(strict_types=1);

use BitCore\Foundation\Database\Migration;
use BitCore\Foundation\Database\Manager as Capsule;
/**
 * Migration for creating the backup_schedules table.
 *
 * Stores recurring backup schedules (interval, time, type, paths, disk, status, etc).
 */
return new class() extends Migration {
    public function up()
    {
        if (!Capsule::schema()->hasTable('backup_schedules')) {
            Capsule::schema()->create('backup_schedules', function (mixed $table) {
                $table->bigIncrements('id');
                $table->string('interval'); // hourly, daily, weekly, monthly
                $table->string('time')->nullable(); // e.g., '02:00' for daily
                $table->string('type'); // files, database, both
                $table->json('paths')->nullable(); // files/directories to backup
                $table->string('disk')->default('local');
                $table->string('status')->default('active'); // active, paused, etc.
                $table->timestamp('last_run_at')->nullable();
                $table->timestamp('next_run_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('backup_schedules');
    }
}; 