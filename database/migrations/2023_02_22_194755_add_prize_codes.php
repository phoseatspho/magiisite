<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrizeCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prize_codes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('code', 15)->unique()->default(1);
            $table->integer('user_id')->unsigned()->default(1);  
            $table->integer('use_limit')->nullable()->default(0); 
            $table->boolean('is_active')->default(1);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('end_at')->nullable()->default(null);
            $table->text('output')->nullable()->default(null);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users'); 
        });

        Schema::create('prize_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('prize_id')->nullable(false);
            $table->string('rewardable_type', 32)->nullable(false);
            $table->unsignedInteger('rewardable_id')->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });

        Schema::create('user_prize_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->integer('prize_id')->unsigned()->default(0)->index(); 
            $table->timestamp('claimed_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prize_codes');
        Schema::dropIfExists('prize_rewards');
        Schema::dropIfExists('user_prize_logs');
    }
}
