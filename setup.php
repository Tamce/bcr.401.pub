<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

require 'src/bootstrap.php';

$schema = Manager::schema();
$schema->dropAllTables();

$schema->create('images', function (Blueprint $table) {
    $table->increments('id');
    $table->string('category', 128)->default('default')->index();
    $table->text('origin_url')->nullable();
    $table->string('local_path', 256)->nullable();
    $table->string('alias', 128)->nullable();
    $table->boolean('downloaded')->default(false)->index();
    $table->text('extra')->nullable();
    $table->timestamps();
});

$schema->create('state', function (Blueprint $table) {
    $table->increments('id');
    $table->string('sender', 32);
    $table->string('state', 256)->nullable();
    $table->timestamps();
});

