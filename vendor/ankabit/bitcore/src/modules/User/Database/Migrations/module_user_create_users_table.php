<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (mixed $table) {
                /** @var BitCore\Foundation\Database\Blueprint $table */
                $table->increments('id');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('first_name')->default('');
                $table->string('last_name')->default('');
                $table->string('password');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('users');
    }
};
