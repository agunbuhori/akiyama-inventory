<?php

namespace App\Http\Controllers\Data;

use App\BsStock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BsStockController extends Controller
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
        $additional = $request->has('additional') ? $request->additional : '0';

        if ($additional == 0) {
            $bsStocks = BsStock::select('bs_stocks.id', 'bs_stocks.jan_code', 'receipt_date', 'receipt_number', 'stock_name', 'barang_titip', 'amount', 'sell_price', 'basic_price', 'company_name', 'client_code', 'stock_code', 'stores.name', 'stock_masters.size')
                        ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                        ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                        ->where('titip', $additional);
        } else {
            $bsStocks = BsStock::select('bs_stocks.id', 'bs_stocks.jan_code', 'receipt_date', 'receipt_number', 'stock_name', 'barang_titip', 'amount', 'company_name', 'client_code', 'stock_code', 'stores.name', 'stock_masters.price as basic_price', 'stock_masters.size')
                        ->addSelect(\DB::raw("SUM(amount*stock_masters.price) AS sell_price"))
                        ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                        ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                        ->groupBy('bs_stocks.id')
                        ->where('titip', $additional);
        }

        switch ($request->filter) {
            case 'per_day':
                $bsStocks = $bsStocks->whereDate('receipt_date', 'like', date('Y-m-d', strtotime($date)).'%');
                break;
            case 'per_month':
                $bsStocks = $bsStocks->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%');
                break;
            case 'per_year':
                $bsStocks = $bsStocks->whereYear('receipt_date', date('Y', strtotime($date)));
                break;
            default:
                $bsStocks = $bsStocks->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%');
        }

        $dates = BsStock::select(\DB::raw("DATE_FORMAT(created_at, '%e') as dates"))
                            ->where('titip', $additional)
                            ->where('created_at', 'like', date('Y-m', strtotime($date)).'%')
                            ->groupBy('dates')
                            ->orderBy('id', 'asc')
                            ->pluck('dates');

        $count = count($dates);

        foreach ($dates as $key => $value) {
            $dates[$key] = $dates[$key] < 10 ? '0'.$dates[$key] : $dates[$key];
        }

        $datatables = datatables()->of($bsStocks)->addIndexColumn()
                ->addColumn('version', function($bsStock) {
                    return $bsStock->stock_master ? $bsStock->stock_master->version : null;
                })->with([
                    'count'=>$count ? $count : 0, 
                    'dates'=>$dates, 
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
            'company_code' => 'required',
            'receipt_date' => 'required',
            'receipt_number' => 'required',
            'stock_code' => 'required',
            'jan_code' => 'required',
            'stock_name' => 'required',
            'amount' => 'required',
            'sell_price' => 'required',
            'basic_price' => 'required',
        ]);

        $bsStock = new BsStock;

        $bsStock->company_code = $request->company_code;
        $bsStock->receipt_date = rtrim(str_replace(['年', '月', '日'], '-', $request->receipt_date), '-');
        $bsStock->receipt_number = $request->receipt_number;
        $bsStock->stock_code = $request->stock_code;
        $bsStock->jan_code = $request->jan_code;
        $bsStock->stock_name = $request->stock_name;
        $bsStock->amount = $request->amount;
        $bsStock->sell_price = $request->sell_price;
        $bsStock->basic_price = $request->basic_price;
        $bsStock->user_id = auth()->user()->id;

        $bsStock->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BsStock  $bsStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BsStock $bsStock)
    {
        $bsStock->client_code = $request->company_code;
        $bsStock->receipt_date = rtrim(str_replace(['年', '月', '日'], '-', $request->receipt_date), '-');
        $bsStock->receipt_number = $request->receipt_number;
        $bsStock->stock_code = $request->stock_code;
        $bsStock->jan_code = $request->jan_code;
        $bsStock->stock_name = $request->stock_name;
        $bsStock->amount = $request->amount;
        $bsStock->sell_price = $request->sell_price;
        $bsStock->basic_price = $request->basic_price;
        $bsStock->user_id = auth()->user()->id;

        $bsStock->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BsStock  $bsStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(BsStock $bsStock)
    {
        if (! request()->has('datas'))
            $bsStock->delete();
        else
            $bsStock->whereIn('id', request()->datas)->delete();
    }
}
