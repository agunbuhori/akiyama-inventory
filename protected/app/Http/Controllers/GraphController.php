<?php

namespace App\Http\Controllers;

use DB;
use App\Stock;
use App\Store;
use App\StockMaster;
use Illuminate\Http\Request;

class GraphController extends Controller
{
    public function perStore()
    {
    	$stocks = Stock::select(DB::raw("SUM(amount) AS total"), DB::raw("SUM(price) AS price"), DB::raw("MONTH(stock_datetime) AS month"), 'stores.code', 'type', 'arrow')
    						->join('stores', 'stores.code', '=', 'stocks.store_code')
    						->groupBy(DB::raw('MONTH(stock_datetime)'), 'store_code', 'type', 'arrow')
    						->whereYear('stock_datetime', date('Y'))
    						->get();

    	$stores = Store::all();

    	$types = Stock::select('type')->groupBy('type')->pluck('type');

    	foreach ($stores as $store) {

    		$store->month = (object) [];
    		
    		foreach (range(1, 12) as $month) {			

    			$store->month->{$month} = (object) [];
				$store->month->{$month}->amount = 0;
				$store->month->{$month}->price = 0;

				foreach ($stocks as $stock) {
					if ($stock->code == $store->code && $stock->month == $month && $stock->arrow == 'out') {
						$store->month->{$month}->amount += $stock->total; 
						$store->month->{$month}->price += $stock->price; 
    				}
				}
    		}

    		$store->type = (object) [];

    		foreach ($types as $type) {
				$store->type->{$type} = 0;

				foreach ($stocks as $stock) {
					if ($stock->code == $store->code && $stock->type == $type)
						$store->type->{$type} += $stock->total; 
				}
    		}
    	}

    	return $stores;
    }

    public function perBrand()
    {
    	$brands = StockMaster::select('brand AS name')
                                ->join('stocks', 'stocks.jan_code', '=', 'stock_masters.jan_code')
                                ->where('arrow', 'out')
                                ->whereYear('stock_datetime', date('Y'))
                                // ->whereMonth('stock_datetime', date('m'))
                                ->groupBy('brand')->get();

    	$stocks = Stock::select(DB::raw("SUM(amount) AS total"), 'brand')
    						->join('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
    						->groupBy(DB::raw('MONTH(stock_datetime)'), 'brand')
    						->whereYear('stock_datetime', date('Y'))
    						// ->whereMonth('stock_datetime', date('m'))
    						->where('arrow', 'out')
    						->get();

    	foreach ($brands as $brand) {
    		$brand->value = 0;
    		foreach ($stocks as $stock) {
    			if ($stock->brand == $brand->name)
    				$brand->value += (int) $stock->total;
    		}
    	}

    	return $brands;
    }
}
