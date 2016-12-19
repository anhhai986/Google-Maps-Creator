<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
			$table->integer('map_style_id')->unsigned()->default(1);
            $table->foreign('map_style_id')->references('id')->on('categories');
			$table->string('name');
			$table->string('address');
			$table->string('image')->nullable();
			$table->string('marker')->nullable();
			$table->string('phone')->nullable();
			$table->string('email')->nullable();
			$table->string('website')->nullable();
			$table->text('short_description')->nullable();
			$table->text('description')->nullable();
			$table->decimal('lat', 10, 8)->nullable();
			$table->decimal('lng', 11, 8)->nullable();
			$table->text('settings')->nullable();
			$table->boolean('active')->default(1);
			$table->boolean('undeletable')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('items');
	}

}
