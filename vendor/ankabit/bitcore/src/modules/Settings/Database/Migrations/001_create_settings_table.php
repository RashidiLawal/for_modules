<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('settings')) {
            Capsule::schema()->create('settings', function (mixed $table) {
                /** @var BitCore\Foundation\Database\Blueprint $table */
                $table->increments('id');
                $table->string('group'); // The group the settings belong
                $table->string('group_id'); // The relative id of the owner
                $table->string('key'); // Setting key.
                $table->text('value')->nullable(); // Value for the setting, can be null

                // Composite unique constraint on group, group_id, and key
                $table->unique(['group', 'group_id', 'key']);
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('settings');
    }
};
