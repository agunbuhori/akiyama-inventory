<?php

namespace App\Http\Controllers\Data;

use DB;
use Excel;
use App\Stock;
use App\BsStock;
use App\StockMaster;
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
    {

        $filter = $request->has('filter') ? $request->filter : 'per_month';
        $date = $request->has('date') ? $request->date : today();
        $arrow = $request->has('arrow') ? $request->arrow : 'out';
        $stockreturn = $request->has('stockreturn') ? $request->stockreturn : '0';
        
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
            case'per_day':
            $date = date('Y-m-d', strtotime($date));
            break;
        }

        $store = \App\Store::where('code', $store_code)->first();

        // $stocks = Stock::select('stocks.id', 'stocks.jan_code', 'stocks.amount', 'stocks.type', 'stocks.price', 'stocks.stock_datetime', 'bs_stocks.basic_price', 'arrow', DB::raw("SUM(stocks.amount) AS amounts"), 'bs_stocks.receipt_number', 'stock_masters.brand')
        //                ->where(['store_code' => $store_code, 'arrow' => $arrow])
        //                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
        //                ->leftJoin('bs_stocks', function ($query) use ($date, $store, $filter) {
        //                     $date = $filter === 'per_day' ? date('Y-m', strtotime($date)) : $date;
        //                     return $query->on('bs_stocks.jan_code', '=', 'stocks.jan_code')
        //                     ->where('receipt_date', 'like', $date.'%')
        //                     ->where('client_code', $store->code_from_bs)
        //                     ->where('titip', 0);
        //                 })
        //                 ->groupBy('stocks.id');

        if ($stockreturn == '0') {
            $stocks = StockMaster::select('brand', 'version', 'size', 'stock_masters.type', 'stock_datetime', DB::raw("SUM(amount) as amount"), DB::raw("SUM(stocks.price) as price"))
                                    ->rightJoin('stocks', 'stocks.jan_code', '=', 'stock_masters.jan_code')
                                    ->where('arrow', 'out')
                                    ->whereDate('stock_datetime', 'like', $date.'%')
                                    ->where('store_code', $store_code)
                                    ->groupBy('stocks.jan_code');

            $totalAmount = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
                               ->where(['store_code' => $store_code, 'arrow' => 'out']);

            $totalPrice = Stock::select(DB::raw("SUM(price) AS total"))
                                ->where(['store_code' => $store_code, 'arrow' => 'out']);

            $totalAmount = $totalAmount->whereDate('stock_datetime', 'like', $date.'%');
            $totalPrice = $totalPrice->whereDate('stock_datetime', 'like', $date.'%');

        } else {
            $stocks = StockMaster::select('brand', 'version', 'size', 'stock_masters.type', 'receipt_date as stock_datetime', DB::raw("SUM(amount) as amount"), DB::raw("SUM(bs_stocks.sell_price) as price"))
                                    ->rightJoin('bs_stocks', 'bs_stocks.jan_code', '=', 'stock_masters.jan_code')
                                    ->where('client_code', $store->code_from_bs )
                                ->whereDate('receipt_date', 'like', $date.'%')
                                    ->where('amount', '<', '0')
                                    ->groupBy('bs_stocks.jan_code');

            $totalAmount = BsStock::select(DB::raw("SUM(amount) AS total"))
                                ->where('client_code', $store->code_from_bs)
                                ->where('amount', '<', '0');

            $totalPrice = BsStock::select(DB::raw("SUM(sell_price) AS total"))
                                ->where('client_code', $store->code_from_bs)
                                ->where('amount', '<', '0');

            $totalAmount = $totalAmount->whereDate('receipt_date', 'like', $date.'%');
            $totalPrice = $totalPrice->whereDate('receipt_date', 'like', $date.'%');
        }

        $uploads = \App\Store::select('code', 'name')
                                ->where('code', $store_code)
                                ->get();

        foreach ($uploads as $key => $upload) {

            $upload->count = Stock::select(DB::raw("DATE(created_at) AS upload"))
                                    ->where('arrow', $arrow)
                                    ->whereDate('created_at', 'like', $date.'%')
                                    ->groupBy(DB::raw("DATE(created_at)"))
                                    ->where('store_code', $upload->code)
                                    ->get();

            $upload->count = $upload->count->isEmpty() ? 0 : count($upload->count);
        }

        $dates = Stock::select(\DB::raw("DATE_FORMAT(created_at, '%e') as dates"))
                                    ->where('arrow', $arrow)
                                    ->where('store_code', $store_code)
                                    ->whereDate('created_at', 'like', $date.'%')
                                    ->orderBy('id', 'asc')
                                    ->groupBy(\DB::raw("DATE_FORMAT(created_at, '%e')"))
                                    ->pluck('dates');

        foreach ($dates as $key => $value) {
            $dates[$key] = $dates[$key] < 10 ? '0'.$dates[$key] : $dates[$key];
        }

        // return $dates;

        $total_amount = number_format($totalAmount->first()->total) ? number_format($totalAmount->first()->total) : 0;
        $total_price = currency($totalPrice->first()->total, 'jp') ? currency($totalPrice->first()->total, 'jp') : 0;

        $datatables = datatables()->of($stocks)->addIndexColumn()
        ->editColumn('stock_datetime', function ($stock) {
                return date('Y年m月d日', strtotime($stock->stock_datetime));
        })->editColumn('price', function ($stock) {
                return rtrim(str_replace(['-'], '', currency($stock->price, 'jp')), '');
        })->editColumn('amount', function ($stock) {
                return rtrim(str_replace(['-'], '', number_format($stock->amount)), '');
        })->with([
                'totalAmount' => rtrim(str_replace(['-'], '', $total_amount), ''),
                'totalPrice' => rtrim(str_replace(['-'], '', $total_price), ''), 
                'count'=> $uploads[0]->count ? $uploads[0]->count : 0, 
                'dates'=> $dates,
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
        $stocks->arrow = 'out';
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
            'type' => $request->type
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
