<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            ['store_code' => '101', 'name' => 'user101', 'fullname' => '深谷南', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '102', 'name' => 'user102', 'fullname' => '台坂上', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '103', 'name' => 'user103', 'fullname' => 'イセヤ', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '104', 'name' => 'user104', 'fullname' => '熊谷ハイタウン', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '105', 'name' => 'user105', 'fullname' => '花園インター', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '106', 'name' => 'user106', 'fullname' => '笠幡', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '107', 'name' => 'user107', 'fullname' => '狭山日高', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '108', 'name' => 'user108', 'fullname' => '笹井', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '201', 'name' => 'user201', 'fullname' => 'コバック 花園店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '202', 'name' => 'user202', 'fullname' => 'コバック 本庄店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '203', 'name' => 'user203', 'fullname' => 'コバック 小舞木店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '204', 'name' => 'user204', 'fullname' => 'コバック 秩父店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '205', 'name' => 'user205', 'fullname' => 'コバック 深谷店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '206', 'name' => 'user206', 'fullname' => 'コバック 藤阿久店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '207', 'name' => 'user207', 'fullname' => 'コバック 籠原店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '208', 'name' => 'user208', 'fullname' => 'コバック 足利店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '209', 'name' => 'user209', 'fullname' => 'コバック 狭山店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '301', 'name' => 'user301', 'fullname' => 'モドーリー 本庄店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '302', 'name' => 'user302', 'fullname' => 'モドーリー 小舞木店', 'email' => str_random(10).'@inv.sumroch.com'],
            ['store_code' => '303', 'name' => 'user303', 'fullname' => 'モドーリー 寄居店', 'email' => str_random(10).'@inv.sumroch.com'],
        ]);
    }
}
