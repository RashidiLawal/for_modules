<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('groups')) {
            Capsule::schema()->create('groups', function (mixed $table) {
                $table->increments('id');
                $table->string('group_name');
                $table->string('group_slug');
                $table->integer('clicks_generated')->default(0);
                $table->decimal('total_earnings', 10, 2)->default(0.00);
                $table->boolean('is_auto_approved')->default(false);
                $table->decimal('default_commission_rate', 5, 2)->default(0.00);
                $table->integer('commission_lock_period')->default(0); // in days
                $table->enum('reward_type', ['first_click', 'last_click'])->default('last_click');
                $table->boolean('is_enable_commission')->default(true);
                $table->decimal('commission_rate', 5, 2)->default(0.00);
                $table->string('commission_type')->nullable(); // e.g. 'percentage', 'fixed'
                $table->text('commission_rule')->nullable();    // JSON or rule description
                $table->decimal('commission_amount', 10, 2)->default(0.00);
                $table->decimal('payout_minimum', 10, 2)->default(0.00);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('groups');
    }
};
