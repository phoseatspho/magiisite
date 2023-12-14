<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //my idiot ass forgor they are called ranks and not roles 
        //please dont point and laugh at the migration name.
        //( ･ᴗ･̥̥̥ )

        Schema::create('rank_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('rank_id');
            $table->string('data')->nullable()->default(null);
            $table->boolean('is_active')->default(0);
            $table->unsignedInteger('reward_time')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        schema::dropIfExists('rank_rewards');
    }
}
