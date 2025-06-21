<?php

use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migration;

return new class () extends Migration
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('affiliates')) {
            Capsule::schema()->create('affiliates', function (mixed $table) {
                $table->increments('id');
                $table->string('affiliate_name');
                $table->string('affiliate_slug');
                $table->string('referral_link')->nullable();
                $table->enum('status', ['enabled', 'disabled'])->default('enabled');
                $table->integer('clicks_generated')->default(0);
                $table->decimal('earnings', 10, 2)->default(0.00);
                $table->date('payout_date')->nullable();
                $table->enum('payout_status', ['paid', 'pending'])->default('pending');
                $table->integer('total_sales')->default(0);
                $table->decimal('commission', 10, 2)->default(0.00);
                $table->unsignedInteger('group_id');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('affiliates');
    }
};
