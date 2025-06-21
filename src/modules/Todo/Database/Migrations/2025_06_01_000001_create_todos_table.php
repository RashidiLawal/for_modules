<?php

 use BitCore\Foundation\Database\Manager as Capsule;
 use BitCore\Foundation\Database\Migration;

 return new class() extends Migration
 {
     public function up()
     {
         if (!Capsule::schema()->hasTable('todos')) {
             Capsule::schema()->create('todos', function (mixed $table) {
                 $table->increments('id');
                 $table->string('title')->unique();
                 $table->text('description');
                 $table->boolean('completed')->default(false);
                 $table->timestamps();
                 $table->softDeletes();

                 $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
             });

         }
     }

     public function down()
     {
         Capsule::schema()->dropIfExists('todos');
     }
 };