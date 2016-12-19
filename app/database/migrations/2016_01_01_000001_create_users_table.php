<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('language')->default('en');
			$table->string('timezone')->default('UTC');
			$table->integer('logins')->default(0)->unsigned();
			$table->dateTime('last_login')->nullable();
			$table->smallInteger('role')->default(3)->unsigned();
			$table->boolean('active')->default(0);
			$table->string('persist_code')->nullable();
			$table->string('remember_token')->nullable();
			$table->text('settings')->nullable();
			$table->dateTime('expires')->nullable();
			$table->smallInteger('expire_notifications')->default(0)->unsigned();
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
		Schema::drop('users');
	}

}
