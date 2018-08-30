<?php

use App\Stock;
use Illuminate\Database\Seeder;

class StockTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$stocks = [];

    	$jenis = ['タイヤ', 'バッテリー', 'ホイール'];
    	$arrow = ['in', 'out'];

        foreach (range(1, 100) as $stock) {
        	$arah = 'out';


        	$stocks[] = [
        		'stock_datetime' => date('Y-m-d H:i:s', strtotime(now().'-1 month')),
        		'jan_code' => DB::table('stocks')->select('jan_code')->where('id', $stock)->first()->jan_code,
        		'type' => $jenis[rand(0, 2)],
        		'amount' => $arah == 'in' ? rand(26, 50) : rand(10, 25),
        		'price' => $arah == 'in' ? 0 : rand(1000, 10000),
        		'arrow' => $arah,
        		'store_code' => '101',
        		'user_id' => 5,
        		'created_at' => date('Y-m-d H:i:s', strtotime(now().'-1 month'))
        	];
        }

        Stock::insert($stocks);
    }
}
