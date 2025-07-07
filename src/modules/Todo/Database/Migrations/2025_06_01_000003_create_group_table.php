<?php

 use BitCore\Foundation\Database\Manager as Capsule;
 use BitCore\Foundation\Database\Migration;

 return new class() extends Migration
 {
     public function up()
     {
         if (!Capsule::schema()->hasTable('todo_groups')) {
             Capsule::schema()->create('todo_groups', function (mixed $table) {
                 $table->increments('id');
                 $table->string('todo_title')->unique();
                 $table->text('todo_description');
                 $table->boolean('completed')->default(false);
                 $table->timestamps();
                 $table->softDeletes();
             });
         }
     }

     public function down()
     {
         Capsule::schema()->dropIfExists('todo_groups');
     }
 };