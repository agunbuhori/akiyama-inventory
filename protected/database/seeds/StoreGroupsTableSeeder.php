<?php

use App\StoreGroup;
use Illuminate\Database\Seeder;

class StoreGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StoreGroup::insert([
        	['code' => '1', 'name' => 'SS'],
        	['code' => '2', 'name' => 'コバック'],
        	['code' => '3', 'name' => 'モドーリー'],
        ]);
    }
}
