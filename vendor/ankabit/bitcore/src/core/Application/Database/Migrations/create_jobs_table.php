<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('jobs')) {
            Capsule::schema()->create('jobs', function (mixed $table) {
                /** @var BitCore\Foundation\Database\Blueprint $table */
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('jobs');
    }
};
