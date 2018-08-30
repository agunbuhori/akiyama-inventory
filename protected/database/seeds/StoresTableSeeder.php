<?php

use App\Store;
use Illuminate\Database\Seeder;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Store::insert([
        	['code' => '101', 'name' => '深谷南', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00101', 'store_group_code' => '1'],
        	['code' => '102', 'name' => '台坂上', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00102', 'store_group_code' => '1'],
        	['code' => '103', 'name' => 'イセヤ', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00103', 'store_group_code' => '1'],
            ['code' => '104', 'name' => '熊谷ハイタウン', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00104', 'store_group_code' => '1'],
            ['code' => '105', 'name' => '花園インター', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00105', 'store_group_code' => '1'],
            ['code' => '106', 'name' => '笠幡', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00106', 'store_group_code' => '1'],
            ['code' => '107', 'name' => '狭山日高', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00107', 'store_group_code' => '1'],
            ['code' => '108', 'name' => '笹井', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '1'],

            ['code' => '201', 'name' => 'コバック 花園店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '202', 'name' => 'コバック 本庄店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '203', 'name' => 'コバック 小舞木店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '204', 'name' => 'コバック 秩父店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '205', 'name' => 'コバック 深谷店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '206', 'name' => 'コバック 藤阿久店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '207', 'name' => 'コバック 籠原店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '208', 'name' => 'コバック 足利店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],
            ['code' => '209', 'name' => 'コバック 狭山店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '2'],

            ['code' => '301', 'name' => 'モドーリー 本庄店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '3'],
            ['code' => '302', 'name' => 'モドーリー 小舞木店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '3'],
        	['code' => '303', 'name' => 'モドーリー 寄居店', 'email' => str_random(10).'@inv.sumroch.com', 'contact' => rand(1, 100).'-00108', 'store_group_code' => '3'],
        ]);
    }
}
