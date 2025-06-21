<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        Capsule::schema()->table('settings', function (mixed $table) {
            /** @var BitCore\Foundation\Database\Blueprint $table */
            $table->json('metadata')->nullable()->after('value');
        });
    }

    public function down()
    {
        Capsule::schema()->table('settings', function (mixed $table) {
            /** @var BitCore\Foundation\Database\Blueprint $table */
            $table->dropColumn('metadata');
        });
    }
};
