<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldExpansionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_expansion_attachments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->integer('attacher_id')->unsigned();
            $table->string('attacher_type');

            $table->integer('attachable_id')->unsigned();
            $table->string('attachable_type');                          // AKA Figure, Items, etc.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('world_expansion_attachments');
    }
}
