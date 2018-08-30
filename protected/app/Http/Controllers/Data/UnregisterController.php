<?php

namespace App\Http\Controllers\Data;

use App\BsStock;
use App\StockMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnregisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unregisters = BsStock::select('bs_stocks.jan_code', 'stock_masters.brand', 'stores.name AS store_name')
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=','bs_stocks.jan_code')
                                ->addSelect(\DB::raw("SUM(amount) as amounts"))
                                ->join('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
        						->where('stock_masters.brand', NULL)
        						->groupBy('jan_code');

        $datatables = datatables()->of($unregisters)->addIndexColumn();

        return $datatables->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) 
    {
        StockMaster::insert([
                    'jan_code' => $request->jan_code,
                    'code' => $request->code,
                    'brand' => $request->brand,
                    'version' => $request->version,
                    'size' => $request->size,
                    'price' => $request->price,
                    'type' => $request->type,
                    'section' => $request->section,
                    'series' => $request->series,
                    'rim' => $request->rim
                ]);
        return back();
    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($jan_code)
    {
        BsStock::where('jan_code', $jan_code)->delete();

        return back();
    }
}
