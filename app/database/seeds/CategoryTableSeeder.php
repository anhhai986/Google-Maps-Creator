<?php

class CategoryTableSeeder extends Seeder {

    public function run()
    {
        DB::table('categories')->delete();

        GMC\Model\Category::create(array(
            'user_id' => 1,
            'name' => 'My Map'
        ));
    }
}