<?php

namespace App\Http\Controllers\Data;

use DB;
use App\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {;
        $filter = $request->has('filter') ? $request->filter : 'per_month';
        $date = $request->has('date') ? $request->date : today();
        $arrow = $request->has('arrow') ? $request->arrow : 'in';
        
        if (auth()->user()->isCentral() && $request->has('store'))
            $store_code = $request->store;
        elseif (auth()->user()->isCentral() && !$request->has('store'))
            $store_code = \App\Store::first()->code;
        else 
            $store_code = auth()->user()->store_code;

        switch ($filter) {
            case 'per_month':
                $date = date('Y-m', strtotime($date));
                break;
            case 'per_year':
                $date = date('Y', strtotime($date));
                break;
        }

        $store = \App\Store::where('code', $store_code)->first();
        $stocks = Stock::select('stocks.id', 'stocks.jan_code', 'stocks.amount', 'stocks.type', 'stocks.price AS total', 'stocks.stock_datetime', 'bs_stocks.basic_price', 'arrow', DB::raw("SUM(stocks.amount) AS amounts"))->where(['store_code' => $store_code, 'arrow' => $arrow])
                            ->leftJoin('bs_stocks', function ($query) use ($date, $store, $filter) {
                                $date = $filter === 'per_day' ? date('Y-m', strtotime($date)) : $date;
                                return $query->on('bs_stocks.jan_code', '=', 'stocks.jan_code')
                                                ->where('receipt_date', 'like', $date.'%')
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('titip', 0);
                            })
                            ->groupBy('stocks.jan_code')
                            ->orderBy('stocks.stock_datetime', 'asc');

        // $stocks = Stock::select('stocks.id', 'stocks.jan_code', 'stocks.amount', 'stocks.type', 'stocks.price AS total', 'stocks.stock_datetime', 'bs_stocks.basic_price', 'arrow')->where(['store_code' => $store_code, 'arrow' => $arrow])
        //                     ->leftJoin('bs_stocks', function ($query) use ($date, $store, $filter) {
        //                         $date = $filter === 'per_day' ? date('Y-m', strtotime($date)) : $date;
        //                         return $query->on('bs_stocks.jan_code', '=', 'stocks.jan_code')
        //                                         ->where('receipt_date', 'like', $date.'%')
        //                                         ->where('client_code', $store->code_from_bs)
        //                                         ->where('titip', 0);
        //                     })
        //                     ->groupBy('stocks.jan_code')
        //                     ->orderBy('stocks.stock_datetime', 'asc');

        // $stocks = Stock::select('stocks.id', 'stocks.jan_code', 'stocks.price AS total', 'stocks.stock_datetime', 'bs_stock.basic_price', 'arrow', DB::raw)

        if ($arrow == 'in') {
            $totalAmount = Stock::select(DB::raw("SUM(stocks.amount) AS total, SUM(stocks.amount*stock_masters.price) AS total_price"))->where(['store_code' => $store_code, 'arrow' => $arrow])
                                ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                ->leftJoin('bs_stocks', function ($query) use ($date, $store) {
                                    return $query->on('bs_stocks.jan_code', '=', 'stocks.jan_code')
                                                    ->where('receipt_date', 'like', $date.'%')
                                                    ->where('client_code', $store->code_from_bs)
                                                    ->where('titip', 0);
                                });
        } else {
            $totalAmount = Stock::select(DB::raw("SUM(stocks.amount) AS total, SUM(stocks.price) AS total_price"))->where(['store_code' => $store_code, 'arrow' => $arrow])
                                ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                ->leftJoin('bs_stocks', function ($query) use ($date, $store) {
                                    return $query->on('bs_stocks.jan_code', '=', 'stocks.jan_code')
                                                    ->where('receipt_date', 'like', $date.'%')
                                                    ->where('client_code', $store->code_from_bs)
                                                    ->where('titip', 0);
                                });
        }

        // if ($arrow == 'in') {
        //     $totalAmount = Stock::select(DB::raw("SUM(stocks.amount) AS total, SUM(stocks.amount*stock_masters.price) AS total_price"))
        //                         ->where(['store_code' => $store_code, 'arrow' => $arrow])
        //                         ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code');
        // } else {
        //     $totalAmount = Stock::select(DB::raw("SUM(stocks.amount) AS total, SUM(stocks.price) AS total_price"))
        //                         ->where(['store_code' => $store_code, 'arrow' => $arrow])
        //                         ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code');
        // }

        $stocks = $stocks->whereDate('stock_datetime', 'like', $date.'%');
        $totalAmount = $totalAmount->whereDate('stock_datetime', 'like', $date.'%');

        $datatables = datatables()->of($stocks)->addIndexColumn()
        ->addColumn('receipt_number', function ($stock) {
            return $stock->bs_stock ? $stock->bs_stock->receipt_number : null;
        })->addColumn('version', function ($stock) {
            return $stock->stock_master ? $stock->stock_master->version : null;
        })->addColumn('stock_name', function ($stock) {
            return $stock->stock_master ? $stock->stock_master->size : null;
        })->addColumn('brand', function ($stock) {
            return $stock->stock_master ? $stock->stock_master->brand : null;
        })->editColumn('price', function ($stock) {
            return $stock->stock_master ? currency($stock->stock_master->price, 'jp') : null;
        })->editColumn('total', function ($stock) {
            return $stock->stock_master ? currency($stock->stock_master->price * $stock->amount, 'jp') : null ;
        })->with([
                'totalAmount' => number_format($totalAmount->first()->total) ? number_format($totalAmount->first()->total) : 0, 
                'totalPrice' => currency($totalAmount->first()->total_price, 'jp') ? currency($totalAmount->first()->total_price, 'jp') : 0, 
        ]);

        return $datatables->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'jan_code' => 'required',
            'amount' => 'required',
        ]);

        $stocks = new Stock;

        $stocks->stock_datetime = rtrim(str_replace(['年', '月', '日'], '-', $request->stock_datetime), '-');
        $stocks->jan_code = $request->jan_code;
        $stocks->amount = $request->amount;
        $stocks->type = $request->type;
        $stocks->arrow = $request->arw;
        $stocks->store_code = auth()->user()->store_code;

        $stocks->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        Stock::where('id', $request->id)
                ->update([
                    'stock_datetime' => rtrim(str_replace(['年', '月', '日'], '-', $request->stock_datetime), '-'),
                    'jan_code' => $request->jan_code,
                    'amount' => $request->amount,
                    'type' => $request->type,
                    'arrow' => $request->arw
                ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        if (! request()->has('datas'))
            $stock->delete();
        else
            $stock->whereIn('id', request()->datas)->delete();
    }
}
