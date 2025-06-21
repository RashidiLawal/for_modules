<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('modules_manager')) {
            Capsule::schema()->create('modules_manager', function ($table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('status')->default('inactive'); // active/inactive
                $table->json('metadata')->nullable(); // meta i.e Array of image paths or URLs, descriptions
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('modules_manager');
    }
};
