<?php

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        \GMC\Model\User::create(array(
            'name' => 'Admin',
            'password' => Hash::make('welcome'),
            'email' => 'info@example.com',
            'role' => 1,
            'active' => 1
        ));
    }
}