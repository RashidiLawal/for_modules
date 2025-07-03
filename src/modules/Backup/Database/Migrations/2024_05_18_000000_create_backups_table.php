<?php

declare(strict_types=1);

use BitCore\Foundation\Database\Migration;
use BitCore\Foundation\Database\Manager as Capsule;
/**
 * Migration for creating the backups table.
 *
 * Stores metadata for each backup (file/db, location, status, schedule, etc).
 */
return new class() extends Migration {
    public function up()
    {
        if (!Capsule::schema()->hasTable('backups')) {
            Capsule::schema()->create('backups', function (mixed $table) {
                $table->bigIncrements('id');
                $table->string('type'); // file, database, or both
                $table->string('file_path'); // Path to backup file
                $table->string('disk')->default('local'); // Storage disk (local, s3, etc)
                $table->string('status')->default('pending'); // pending, completed, failed
                $table->timestamp('scheduled_at')->nullable(); // For scheduled backups
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('backups');
    }
};