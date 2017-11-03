<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    if( !Schema::hasTable('blocks')){
            Schema::create('blocks', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('url', 191)->unique();
                $table->integer('position')->default(0);
                $table->string('redirect')->default('');
                $table->integer('active')->default(1);
                $table->timestamps();
            });
        }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('blocks');
	}
}