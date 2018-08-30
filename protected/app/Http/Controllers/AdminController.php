<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Stock;
use App\Store;
use App\BsStock;
use App\StoreGroup;
use App\StockMaster;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('central')->only(['store', 'store_group', 'company', 'stockMaster', 'reportStore', 'reportAll', 'bsStock', 'history', 'translate']);
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockMaster()
    {
    	return view('admin.stock_master');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function bsStock()
    {
        return view('admin.bs_stock', compact('count'));
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function stock()
    {

        return view('admin.stock');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $stores = Store::all();

        return view('admin.store', compact('stores'));
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeGroup()
    {
        return view('admin.store_group');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function company()
    {
        return view('admin.company');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function translate()
    {
        return view('admin.translate');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function unregister()
    {
        return view('admin.unregister');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function closeBook()
    {
        return view('admin.close_book');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function shift()
    {
        if (auth()->user()->role == 1) {
            return view('admin.shift_central');
        } else {
            return view('admin.shift_store');
        }
    }

    public function shiftDipinjam() {
        return view('admin.shift_dipinjam');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function languageJp()
    {
        \App\User::where('id', auth()->user()->id)->update(['language' => 'jp']);

        return back();
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function languageEn()
    {
        \App\User::where('id', auth()->user()->id)->update(['language' => 'en']);

        return back();
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function newReport() 
    {
        return view ('admin.new_report');
    }
    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        if (auth()->user()->role == 1) {
            $graph = new GraphController;

            $stores = $graph->perStore();
            $perBrand = $graph->perBrand();

            $storeNames = array_pluck($stores, 'name');
            $perType = [];
            $perPrice = [];
            $perAmount = [];
            $perGroup = [];
            $totalGroup = [];

            foreach (StoreGroup::all() as $k => $group) {
                foreach ($stores as $ks => $store) {
                    foreach ($store->type as $key => $type) {
                        if (! array_key_exists($key, $perType)) {
                            $perType[$key] = [
                                'name' => $key,
                                'type' => 'bar',
                                'stack' => 'Total',
                                'data' => []
                            ];
                        }

                        if (! array_key_exists($ks, $perType[$key]['data']))
                            $perType[$key]['data'][$ks] = $type;
                        else
                            $perType[$key]['data'][$ks] += $type;
                    }

                    $perPrice[$ks] = [
                        'name' => $store->name,
                        'type' => 'bar',
                        'stack' => 'Advertising',
                        'data' => array_pluck(array_values((array)$store->month), 'price')
                    ];

                    $perAmount[$ks] = array_pluck(array_values((array)$store->month), 'amount');
                    array_unshift($perAmount[$ks], $store->name);
                }

            }

            $perGroup = $this->perGroup(new Request);
            $perTypes = $this->perType(new Request);

            return view('admin.dashboard_central', compact('perType', 'storeNames', 'perPrice', 'perAmount', 'perBrand', 'perGroup', 'perTypes'));

        } else {
            $search = $request->input('search');

            $date = $request->has('date') ? $request->date : today();
            $this_month = date('Y-m', strtotime($date));
            $last_month = date('Y-m', strtotime($date.'-1 month'));

            $graph_bar_store = $this->graphBarStore(new Request);
            $graph_pie_store = $this->graphPieStore(new Request);
            $graph_column_store = $this->graphColumnStore(new Request);

            $type = $request->has('type') ? $request->type : 'タイヤ';
            
            if ($request->type == 'oli') {
                $type = 'オイル';
            } elseif ($request->type == 'battery') {
                $type = 'バッテリー';
            } elseif ($request->type == 'velg') {
                $type = 'ホイール';
            } else {
                $type = 'タイヤ';
            }

            $datas = BsStock::select('bs_stocks.jan_code', 'stock_masters.size', 'stock_masters.brand')
                            ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                            ->where('stores.code', auth()->user()->store_code)
                            ->where('type', $type)
                            ->groupBy('bs_stocks.jan_code')
                            ->get();

            foreach ($datas as $data) {
                $data->stock_in = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                        ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                        ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                        ->where('stores.code', auth()->user()->store_code)
                                                        ->where('bs_stocks.jan_code', $data->jan_code)
                                                        ->where('type', $type)
                                                        ->first()
                                                        ->total;
                $data->stock_out = Stock::select(DB::raw("SUM(amount) AS total"))
                                                        ->where('store_code', auth()->user()->store_code)
                                                        ->where('jan_code', $data->jan_code)
                                                        ->where('arrow', 'out')
                                                        ->where('type', $type)
                                                        ->first()
                                                        ->total;

                $data->total = $data->stock_in - $data->stock_out;
            }

            $stocks = Stock::select('stocks.*', 'stores.name', 'stock_masters.brand', 'stock_masters.size', 'stock_masters.version', 'stock_masters.jan_code')
                                ->addSelect(DB::raw("SUM(amount) AS amount"))
                                ->join('stores', 'stores.code', '=', 'stocks.store_code')
                                ->join('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                ->where('stocks.arrow', 'in')
                                ->where('stock_masters.size', 'like', '%'.$search.'%')
                                ->where('stocks.store_code', '!=', auth()->user()->store_code)
                                ->groupBy('stocks.jan_code')
                                ->paginate(25);

            return view('admin.dashboard_store', compact('stocks', 'graph_bar_store', 'graph_pie_store', 'graph_column_store', 'sell_amount', 'datas'));
        }
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportStore()
    {
        return view('admin.report_store');
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportAll(Request $request)
    {
        $this_month = $request->has('month') ? $request->month : date('m');
        $this_year = $request->has('year') ? $request->year : date('Y');

        $last_month = $this_month - 1;

        // $date = $request->has('date') ? $request->date : today();
        // $this_month = date('Y-m', strtotime($date));
        // $last_month = date('Y-m', strtotime($date.'-1 month'));
        $stores = Store::orderBy('store_group_code', 'asc')->get();
        
        if ($request->type == 'oli') {
            $type = ['オイル'];
        } elseif ($request->type == 'battery') {
            $type = ['バッテリー'];
        } elseif ($request->type == 'velg') {
            $type = ['ホイール'];
        } elseif ($request->type == 'tire')  {
            $type = ['タイヤ'];
        } else {
            $type = ['オイル', 'バッテリー', 'ホイール', 'タイヤ'];
        }

        foreach ($stores as $store) {
            $store->stock_in_last_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                // ->where('stock_datetime', 'like', $last_month.'%')
                                                ->where('arrow', 'in')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->stock_out_last_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                // ->where('stock_datetime', 'like', $last_month.'%')
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->stock_in_this_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                // ->where('stock_datetime', 'like', $this_month.'%')
                                                ->where('arrow', 'in')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->stock_out_this_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                // ->where('stock_datetime', 'like', $this_month.'%')
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->price_stock_in_last_month = Stock::select(DB::raw("SUM(stocks.amount*stock_masters.price) AS total"))
                                                ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                // ->where('stock_datetime', 'like', $last_month.'%')
                                                ->where('arrow', 'in')
                                                ->where('store_code', $store->code)
                                                ->whereIn('stocks.type', $type)
                                                ->first()
                                                ->total;

            $store->price_stock_out_last_month = Stock::select(DB::raw("SUM(price) AS total"))
                                                // ->where('stock_datetime', 'like', $last_month.'%')
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->price_stock_in_this_month = Stock::select(DB::raw("SUM(stocks.amount*stock_masters.price) AS total"))
                                                ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                                // ->where('stock_datetime', 'like', $this_month.'%')
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'in')
                                                ->where('store_code', $store->code)
                                                ->whereIn('stocks.type', $type)
                                                ->first()
                                                ->total;

            $store->price_stock_out_this_month = Stock::select(DB::raw("SUM(price) AS total"))
                                                // ->where('stock_datetime', 'like', $this_month.'%')
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->barang_titip_this_month = BsStock::select(DB::raw("SUM(bs_stocks.amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;
                                               
            $store->price_barang_titip_this_month = BsStock::select(DB::raw("SUM(bs_stocks.amount*stock_masters.price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;

            $store->barang_titip_last_month = BsStock::select(DB::raw("SUM(bs_stocks.amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;
                                               
            $store->price_barang_titip_last_month = BsStock::select(DB::raw("SUM(bs_stocks.amount*stock_masters.price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;

            $store->stock_last_month = $store->stock_in_last_month - $store->stock_out_last_month + $store->barang_titip_last_month;
            $store->price_last_month = $store->price_stock_in_last_month - $store->price_stock_out_last_month + $store->price_barang_titip_last_month;
            $store->total_stock = $store->stock_last_month + ($store->stock_in_this_month - $store->stock_out_this_month) + $store->barang_titip_this_month;
            $store->total_price_this_month = $store->price_last_month + $store->price_stock_in_this_month - $store->price_stock_out_this_month + $store->price_barang_titip_this_month;

            $store->date = Stock::select('created_at')
                                    ->orderBy('created_at', 'desc')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('created_at', $this_month)
                                    ->limit(1)
                                    ->first();

            $store->date = $store->date == null ? '-' : date('Y年m月d日', strtotime($store->date->created_at));

        }

        // return $stores;


        return view('admin.report_all', compact('stores'));
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportApi(Request $request)
    {
        $store_code = $request->has('store') ? $request->store : \App\Store::first()->code;
        $date = $request->has('date') ? $request->date : today();
        $this_month = date('Y-m', strtotime($date));
        $last_month = date('Y-m', strtotime($date.'-1 month'));

        if (request()->type == 'oli') {
            $type = ['オイル'];
        } elseif (request()->type == 'battery') {
            $type = ['バッテリー'];
        } elseif (request()->type == 'velg') {
            $type = ['ホイール'];
        } elseif (request()->type == 'tire')  {
            $type = ['タイヤ'];
        } else {
            $type = ['オイル', 'バッテリー', 'ホイール', 'タイヤ'];
        }

        $store = \App\Store::where('code', $store_code)->first();

        // return $store;

        $stocks = Stock::select('stocks.id', 'stocks.jan_code', 'stocks.type', 'stocks.amount', 'stocks.stock_datetime', 'bs_stocks.basic_price', 'stock_masters.price', 'stocks.created_at', 'stock_masters.brand')
                            ->addSelect('bs_stocks.memo')
                            // ->addSelect(DB::raw("SUM(bs_stocks.amount) AS bs_amount"))
                            ->leftJoin('bs_stocks', function ($query) use ($store, $this_month) {
                                return $query->on('bs_stocks.jan_code', '=', 'stocks.jan_code')
                                                ->where('bs_stocks.client_code', $store->code_from_bs)
                                                ->where('bs_stocks.receipt_date', 'like', $this_month.'%');
                            })
                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                            ->where('store_code', $store_code)
                            ->whereIn('stocks.type', $type)
                            ->groupBy('stocks.jan_code');

        switch ($request->filter) {
            case 'per_day':
                $stocks = $stocks->whereDate('stock_datetime', 'like', date('Y-m-d', strtotime($date)).'%');
                break;
            case 'per_month':
                $stocks = $stocks->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%');
                break;
            case 'per_year':
                $stocks = $stocks->whereYear('stock_datetime', date('Y', strtotime($date)));
                break;
            default:
                $stocks = $stocks->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%');
        }

        $in_last_month = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
                                // ->join('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
                                ->where('stock_datetime', '<', date('Y-m-d H:i:s', strtotime($this_month)))
                                ->where('arrow', 'in')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $last_month.'%')
                                ->first()
                                ->total;

        $out_last_month = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
                                // ->join('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
                                ->where('stock_datetime', '<', date('Y-m-d H:i:s', strtotime($this_month)))
                                ->where('arrow', 'out')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $last_month.'%')
                                ->first()
                                ->total;

        $in_this_month = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
                                // ->join('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
                                ->where('stock_datetime', 'like', $this_month.'%')
                                ->where('arrow', 'in')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $this_month.'%')
                                ->first()
                                ->total;

        $out_this_month = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
                                // ->join('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
                                ->where('stock_datetime', 'like', $this_month.'%')
                                ->where('arrow', 'out')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.receipt_date', 'like', $this_month.'%')
                                // ->where('bs_stocks.titip', 0)
                                ->first()
                                ->total;

        $price_in_last_month = Stock::select(DB::raw("SUM(stocks.amount*stock_masters.price) AS total"))
                                ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                // ->where('stock_datetime', 'like', $last_month.'%')
                                ->where('stock_datetime', '<', date('Y-m-d H:i:s', strtotime($this_month)))
                                ->where('arrow', 'in')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $this_month.'%')
                                ->first()
                                ->total;

        $price_out_last_month = Stock::select(DB::raw("SUM(stocks.price) AS total"))
                                // ->join('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
                                // ->where('stock_datetime', 'like', $last_month.'%')
                                ->where('stock_datetime', '<', date('Y-m-d H:i:s', strtotime($this_month)))
                                ->where('arrow', 'out')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $last_month.'%')
                                ->first()
                                ->total;

        $price_in_this_month = Stock::select(DB::raw("SUM(stocks.amount*stock_masters.price) AS total"))
                                ->leftjoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                ->where('stock_datetime', 'like', $this_month.'%')
                                ->where('arrow', 'in')
                                ->whereIn('stocks.type', $type)
                                ->where('store_code', $store_code)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $this_month.'%')
                                ->first()
                                ->total;

        $price_out_this_month = Stock::select(DB::raw("SUM(stocks.price) AS total"))
                                // ->join('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
                                ->where('stock_datetime', 'like', $this_month.'%')
                                ->where('arrow', 'out')
                                ->where('store_code', $store_code)
                                ->whereIn('stocks.type', $type)
                                // ->where('bs_stocks.titip', 0)
                                // ->where('bs_stocks.receipt_date', 'like', $this_month.'%')
                                ->first()
                                ->total;

        $total_last_month = $in_last_month - $out_last_month;
        $total_this_month = $total_last_month + $in_this_month - $out_this_month;
        $price_last_month = $price_in_last_month - $price_out_last_month;
        $price_this_month = $price_last_month + $price_in_this_month - $price_out_this_month;

        $datatables = datatables()->of($stocks)->addIndexColumn()
                        ->addColumn('stock_in_this_month', function ($stock) use ($this_month, $store_code, $type) {
                            $result = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->where('jan_code', $stock->jan_code)
                                                ->where('stock_datetime', 'like', $this_month.'%')
                                                ->where('arrow', 'in')
                                                ->whereIn('stocks.type', $type)
                                                ->where('store_code', $store_code)
                                                ->first();
                            return ! is_null($result->total) ? $result->total : 0;
                        })->addColumn('bs_amount', function ($stock) use ($this_month, $store, $type) {
                            $result = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                ->where('jan_code', $stock->jan_code)
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('receipt_date', 'like', $this_month.'%')
                                                ->where('titip', 0)
                                                ->first();
                            return ! is_null($result->total) ? $result->total : 0;
                        })->addColumn('stock_out_this_month', function ($stock) use ($this_month, $store_code, $type) {
                            $result = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->where('jan_code', $stock->jan_code)
                                                ->where('stock_datetime', 'like', $this_month.'%')
                                                ->where('store_code', $store_code)
                                                ->whereIn('stocks.type', $type)
                                                ->where('arrow', 'out')
                                                ->first();
                            return ! is_null($result->total) ? $result->total : 0;
                        })->addColumn('stock_in_last_month', function ($stock) use ($this_month, $store_code, $type) {
                            $result = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->where('jan_code', $stock->jan_code)
                                                // ->where('stock_datetime', 'like', $last_month.'%')
                                                ->where('stock_datetime', '<', date('Y-m-d H:i:s', strtotime($this_month)))
                                                ->where('store_code', $store_code)
                                                ->whereIn('stocks.type', $type)
                                                ->where('arrow', 'in')
                                                ->first();
                            return ! is_null($result->total) ? $result->total : 0;
                        })->addColumn('stock_out_last_month', function ($stock) use ($this_month, $store_code, $type) {
                            $result = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->where('jan_code', $stock->jan_code)
                                                // ->where('stock_datetime', 'like', $last_month.'%')
                                                ->where('stock_datetime', '<', date('Y-m-d H:i:s', strtotime($this_month)))
                                                ->where('store_code', $store_code)
                                                ->whereIn('stocks.type', $type)
                                                ->where('arrow', 'out')
                                                ->first();
                            return ! is_null($result->total) ? $result->total : 0;
                        })->addColumn('section', function ($stock) {
                            return $stock->stock_master ? $stock->stock_master->section : null;
                        })->addColumn('series', function ($stock) {
                            return $stock->stock_master ? $stock->stock_master->series : null;
                        })->addColumn('rim', function ($stock) {
                            return $stock->stock_master ? $stock->stock_master->rim : null;
                        })->addColumn('bs_titip', function ($stock) use ($this_month, $store) {
                            $titip = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                ->where('jan_code', $stock->jan_code)
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('receipt_date', 'like', $this_month.'%')
                                                ->where('titip', 1)
                                                ->first();
                            return $titip->total ? $titip->total : 0;
                        })->addColumn('stock_name', function ($stock) {
                            return $stock->stock_master ? $stock->stock_master->size : null;
                        })->editColumn('stock_datetime', function ($stock) {
                            return date('Y年m月d日', strtotime($stock->stock_datetime));
                        })->with([
                            'total_last_month'=>$total_last_month ? $total_last_month : 0, 
                            'total_this_month'=>$total_this_month ? $total_this_month : 0, 
                            'in_this_month'=>$in_this_month ? $in_this_month : 0, 
                            'out_this_month'=>$out_this_month ? $out_this_month : 0,
                            'price_last_month'=>$price_last_month ? $price_last_month : 0,
                            'price_this_month'=>$price_this_month ? $price_this_month : 0,
                            'price_in_this_month'=>$price_in_this_month ? $price_in_this_month : 0,
                            'price_out_this_month'=>$price_out_this_month ? $price_out_this_month : 0
                        ]);

        return $datatables->make(true);
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function graphBarStore(Request $request)
    {
        $date = $request->has('date') ? $request->date : today();
        $this_month = date('Y-m', strtotime($date));
        $last_month = date('Y-m', strtotime($date.'-1 month'));

        $stocks = Stock::select('type as name', 'id')->groupBy('name')->get();

        foreach ($stocks as $stock) {
            $stock->data_in = BsStock::select(DB::raw("SUM(amount) as total"))
                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                ->where('type', $stock->name)
                                ->where('stores.code', auth()->user()->store_code)
                                ->first()
                                ->total;
            $stock->data_out = Stock::select(DB::raw("SUM(amount) as total"))
                                ->where('arrow', 'out')
                                ->where('type', $stock->name)
                                ->where('stock_datetime', 'like', $this_month.'%')
                                ->where('store_code', auth()->user()->store_code)
                                ->first()
                                ->total;
        }

        $graph = [];

        foreach ($stocks as $s) {
            $graph['type'][] = (int)$s['data_in'] - (int)$s['data_out'];
        }

        return $graph;
    }

    /**
     * Show the application page.
     *
     * @return \Illuminate\Http\Response
     */
    public function graphPieStore() {
        $brand = StockMaster::select(DB::raw("SUM(stocks.amount) as value"), 'stock_masters.brand as name')
                                ->groupBy('stock_masters.brand')
                                ->join('stocks', 'stocks.jan_code', '=', 'stock_masters.jan_code')
                                ->where('stocks.arrow', 'out')
                                ->whereYear('stock_datetime', date('Y'))
                                // ->whereMonth('stocks.stock_datetime', date('m'))
                                ->where('stocks.store_code', auth()->user()->store_code)
                                ->where('stock_masters.brand', '!=', null)
                                ->get();
        return $brand;

    }

    public function graphColumnStore() {
        $stores = Store::select('name', 'code')->where('code', auth()->user()->store_code)->get();

        foreach ($stores as $store) {
            $per_month = [];
            $total_amount = [];

            foreach (range(1, 12) as $month) {
                $per_month[$month] = Stock::select(DB::raw("SUM(price) as total"))
                                    ->where('arrow', 'out')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('stock_datetime', $month)
                                    ->whereYear('stock_datetime', date('Y'))
                                    ->first();

                $per_month[$month] = $per_month[$month]->total ? $per_month[$month]->total : 0;

                $total_amount[$month] = Stock::select(DB::raw("SUM(amount) as total"))
                                    ->where('arrow', 'out')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('stock_datetime', $month)
                                    ->whereYear('stock_datetime', date('Y'))
                                    ->first();

                $total_amount[$month] = $total_amount[$month]->total ? $total_amount[$month]->total : 0;
            }

            $store->type = 'bar';
            $store->stack = 'Advertising';
            $store->data = array_values($per_month);
            $store->data_amount = array_values($total_amount);
            $per_month = [];
            $total_amount = [];
        }

        return $stores;
    }

    public function perGroup()
    {
        $groups = StoreGroup::all();

        foreach ($groups as $group) {
            $per_month = [];
            $normal = array();

            if ($group->code == 2712) {
                foreach (range(1, 12) as $month) {
                    $per_month[$month] = Stock::select(DB::raw("SUM(price) as total"))
                                        ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                        ->where('arrow', 'out')
                                        ->where('dealer', '5')
                                        ->whereMonth('stock_datetime', $month)
                                        ->whereYear('stock_datetime', date('Y'))
                                        ->first();

                    $per_month[$month] = $per_month[$month]->total ? $per_month[$month]->total : 0;
                }

                $group->type = 'bar';
                $group->stack = 'Advertising';
                $group->data = array_values($per_month);
                $per_month = [];

                foreach ($groups as $group) {
                    $group->itemStyle = array("normal" 
                                            => array("color" 
                                                => StoreGroup::select('color as color')
                                                ->where('code', $group->code)
                                                ->first()->color)
                                        );
                }
            } else {
                foreach (range(1, 12) as $month) {
                    $per_month[$month] = Stock::select(DB::raw("SUM(price) as total"))
                                        ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                        ->where('stores.store_group_code', $group->code)
                                        ->where('arrow', 'out')
                                        ->where('dealer', '!=', '5')
                                        ->whereMonth('stock_datetime', $month)
                                        ->whereYear('stock_datetime', date('Y'))
                                        ->first();

                    $per_month[$month] = $per_month[$month]->total ? $per_month[$month]->total : 0;
                }

                $group->type = 'bar';
                $group->stack = 'Advertising';
                $group->data = array_values($per_month);
                $per_month = [];

                foreach ($groups as $group) {
                    $group->itemStyle = array("normal" 
                                            => array("color" 
                                                => StoreGroup::select('color as color')
                                                ->where('code', $group->code)
                                                ->first()->color)
                                        );
                }
            }
        }

        return $groups;
    }

    public function perType() {

                $stores = Store::select('name', 'code', 'code_from_bs')->get();

        foreach ($stores as $store) {
            $store->ban_in = BsStock::select(DB::raw("SUM(amount) as total"))
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                ->where('client_code', $store->code_from_bs)
                                ->where('type', 'タイヤ')
                                ->first()
                                ->total;
            $store->battery_in = BsStock::select(DB::raw("SUM(amount) as total"))
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                ->where('client_code', $store->code_from_bs)
                                ->where('type', 'バッテリー')
                                ->first()
                                ->total;
            $store->velg_in = BsStock::select(DB::raw("SUM(amount) as total"))
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                ->where('client_code', $store->code_from_bs)
                                ->where('type', 'ホイール')
                                ->first()
                                ->total;
            $store->oli_in = BsStock::select(DB::raw("SUM(amount) as total"))
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                ->where('client_code', $store->code_from_bs)
                                ->where('type', 'オイル')
                                ->first()
                                ->total;
            $store->ban_out = Stock::select(DB::raw("SUM(amount) as total"))
                                ->where('arrow', 'out')
                                ->where('store_code', $store->code)
                                ->where('type', 'タイヤ')
                                ->first()
                                ->total;
            $store->battery_out = Stock::select(DB::raw("SUM(amount) as total"))
                                ->where('arrow', 'out')
                                ->where('store_code', $store->code)
                                ->where('type', 'バッテリー')
                                ->first()
                                ->total;
            $store->velg_out = Stock::select(DB::raw("SUM(amount) as total"))
                                ->where('arrow', 'out')
                                ->where('store_code', $store->code)
                                ->where('type', 'ホイール')
                                ->first()
                                ->total;
            $store->oli_out = Stock::select(DB::raw("SUM(amount) as total"))
                                ->where('arrow', 'out')
                                ->where('store_code', $store->code)
                                ->where('type', 'オイル')
                                ->first()
                                ->total;
        }

        $graph = [];

        foreach ($stores as $s) {
            $graph['ban'][] = (int)$s['ban_in'] - (int)$s['ban_out'];
            $graph['battery'][] = (int)$s['battery_in'] - (int)$s['battery_out'];
            $graph['velg'][] = (int)$s['velg_in'] - (int)$s['velg_out'];
            $graph['oli'][] = (int)$s['oli_in'] - (int)$s['oli_out'];
        }
        
        return $graph;
    }

    public function newReportApi(Request $request) {

        $store_code = $request->has('store') ? $request->store : \App\Store::first()->code;
        $date = $request->has('date') ? $request->date : today();
        $this_month = date('Y-m', strtotime($date));
        $last_month = date('Y-m', strtotime($date.'-1 month'));

        $price_this_month = 0;
        $total_last_month = 0;
        $total_this_month = 0;
        $price_last_month = 0;
        $in_this_month = 0;
        $out_this_month = 0;
        $price_in_this_month = 0;
        $price_out_this_month = 0;

        if (request()->type == 'oli') {
            $type = ['オイル'];
        } elseif (request()->type == 'battery') {
            $type = ['バッテリー'];
        } elseif (request()->type == 'velg') {
            $type = ['ホイール'];
        } elseif (request()->type == 'tire')  {
            $type = ['タイヤ'];
        } else {
            $type = ['オイル', 'バッテリー', 'ホイール', 'タイヤ'];
        }

        $store = \App\Store::where('code', $store_code)->first();

        $reset = \DB::table('resets') 
                        ->where('store_code', $store->code)
                        ->whereMonth('date', date('m', strtotime($date)))
                        ->whereYear('date', date('Y', strtotime($date)))
                        ->get();

        $stocks = BsStock::select('type', 'brand', 'size', 'bs_stocks.jan_code', 'stock_masters.price as basic_price', 'memo', 'receipt_date')
                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date)))
                            ->where('client_code', $store->code_from_bs)
                            ->whereIn('type', $type)
                            ->groupBy('bs_stocks.jan_code')
                            ->get();

        // switch ($request->filter) {
        //     case 'per_day':
        //         $stocks = $stocks->whereDate('receipt_date', 'like', date('Y-m-d', strtotime($date)).'%');
        //         break;
        //     case 'per_month':
        //         $stocks = $stocks->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%');
        //         break;
        //     case 'per_year':
        //         $stocks = $stocks->whereYear('receipt_date', date('Y', strtotime($date)));
        //         break;
        //     default:
        //         $stocks = $stocks->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%');
        // }

        foreach ($stocks as $stock) {
            $stock->stock_in_last_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('client_code', $store->code_from_bs)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->where('titip', 0)
                                            ->whereIn('type', $type)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->barang_titip_last_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('client_code', $store->code_from_bs)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('titip', 1)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->stock_out_last_month = Stock::select(DB::raw("SUM(amount) as total"))
                                            ->where('stock_datetime', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('store_code', $store->code)
                                            ->where('jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('arrow', 'out')
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->stock_in_this_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('client_code', $store->code_from_bs)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('titip', 0)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->barang_titip_this_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('client_code', $store->code_from_bs)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('titip', 1)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->stock_in_store = Stock::select(DB::raw("SUM(amount) as total"))
                                            ->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('store_code', $store->code)
                                            ->where('jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('arrow', 'in')
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->stock_out_this_month = Stock::select(DB::raw("SUM(amount) as total"))
                                            ->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('store_code', $store->code)
                                            ->where('jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('arrow', 'out')
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->sell_price = Stock::select(DB::raw("SUM(price) as total"))
                                            ->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('store_code', $store->code)
                                            ->where('jan_code', $stock->jan_code)
                                            ->where('arrow', 'out')
                                            ->whereIn('type', $type)
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->price_in_last_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('client_code', $store->code_from_bs)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->price_in_this_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('client_code', $store->code_from_bs)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();

            $stock->price_in_last_month = $stock->price_in_last_month == null ? 0 : $stock->price_in_last_month->total;
            $stock->price_in_this_month = $stock->price_in_this_month == null ? 0 : $stock->price_in_this_month->total;
            $stock->barang_titip_this_month = $stock->barang_titip_this_month == null ? 0 : $stock->barang_titip_this_month->total;
            $stock->barang_titip_last_month = $stock->barang_titip_last_month == null ? 0 : $stock->barang_titip_last_month->total;
            $stock->stock_in_store = $stock->stock_in_store == null ? 0 : $stock->stock_in_store->total;
            $stock->stock_in_last_month = $stock->stock_in_last_month == null ? 0 : $stock->stock_in_last_month->total;
            $stock->stock_out_last_month = $stock->stock_out_last_month == null ? 0 : $stock->stock_out_last_month->total;
            $stock->stock_in_this_month = $stock->stock_in_this_month == null ? 0 : $stock->stock_in_this_month->total;
            $stock->stock_out_this_month = $stock->stock_out_this_month == null ? 0 : $stock->stock_out_this_month->total;
            $stock->sell_price = $stock->sell_price == null ? 0 : $stock->sell_price->total;
            $stock->stock_last_month = $stock->stock_in_last_month - $stock->stock_out_last_month + $stock->barang_titip_last_month;
            $stock->stock_this_month = $stock->stock_last_month + $stock->stock_in_this_month - $stock->stock_out_this_month + $stock->barang_titip_this_month;
            $stock->difference = $stock->stock_in_store - $stock->stock_in_this_month - $stock->barang_titip_this_month;
            $stock->total_price_this_month = $stock->stock_this_month * $stock->basic_price;

            $total_last_month = $total_last_month + $stock->stock_last_month;
            $price_last_month = $price_last_month + ($stock->stock_last_month * $stock->basic_price);
            $price_this_month = $price_this_month + $stock->total_price_this_month;
            $total_this_month = $total_this_month + $stock->stock_this_month;
            $in_this_month = $in_this_month + $stock->stock_in_this_month;
            $out_this_month = $out_this_month + $stock->stock_out_this_month;
            $price_in_this_month = $price_in_this_month + ($stock->stock_in_this_month * $stock->basic_price);
            $price_out_this_month = $price_out_this_month + $stock->sell_price;
        }

        $datatables = datatables()->of($stocks)->addIndexColumn()
                            ->editColumn('receipt_date', function ($stock) {
                                 return date('Y年m月d日', strtotime($stock->receipt_date));
                            })->with([
                                'total_last_month'=>$total_last_month ? $total_last_month : 0, 
                                'total_this_month'=>$total_this_month ? $total_this_month : 0, 
                                'in_this_month'=>$in_this_month ? $in_this_month : 0, 
                                'out_this_month'=>$out_this_month ? $out_this_month : 0,
                                'price_last_month'=>$price_last_month ? $price_last_month : 0,
                                'price_this_month'=>$price_this_month ? $price_this_month : 0,
                                'price_in_this_month'=>$price_in_this_month ? $price_in_this_month : 0,
                                'price_out_this_month'=>$price_out_this_month ? $price_out_this_month : 0
                            ]);

        return $datatables->make(true);

    }

    public function reportDealer() {

        $stores = Store::select('code')->get();

        foreach ($stores as $store) {
            $amount_per_month = [];
            $price_per_month = [];

            foreach (range(1, 12) as $month) {
                $amount_per_month[$month] = Stock::select(DB::raw("SUM(stocks.amount) as total"))
                                    ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                    ->whereMonth('stock_datetime', $month)
                                    ->where('dealer', 5)
                                    ->where('arrow', 'out')
                                    ->where('stores.code', $store->code)
                                    ->first();

                $amount_per_month[$month] = $amount_per_month[$month]->total ? $amount_per_month[$month]->total : 0;

                $price_per_month[$month] = Stock::select(DB::raw("SUM(price) AS total"))
                                    ->whereMonth('stock_datetime', $month)
                                    ->where('arrow', 'out')
                                    ->where('dealer', 5)
                                    ->where('store_code', $store->code)
                                    ->first();

                $price_per_month[$month] = $price_per_month[$month]->total ? $price_per_month[$month]->total : 0;
            }

            $store->total_amount = array_values($amount_per_month);
            $store->total_price = array_values($price_per_month);
            $amount_per_month = [];
            $price_per_month = [];
        }

        $groups = StoreGroup::whereNotIn('code', [8, 2712])->get();

        foreach ($groups as $key => $group) {
            $group->amount =  Stock::select(DB::raw("SUM(stocks.amount) as total"))
                                    ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                    ->where('dealer', 5)
                                    ->where('arrow', 'out')
                                    ->where('store_group_code', $group->code)
                                    ->first()
                                    ->total;
            $group->price =  Stock::select(DB::raw("SUM(stocks.price) as total"))
                                    ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                    ->where('dealer', 5)
                                    ->where('arrow', 'out')
                                    ->where('store_group_code', $group->code)
                                    ->first()
                                    ->total;
        }

        // return $groups;

        return view ('admin.report_dealer', compact('stores', 'groups'));
    }

    public function newReportAll(Request $request)
    {
        $this_month = $request->has('month') ? $request->month : date('m');
        $this_year = $request->has('year') ? $request->year : date('Y');

        $last_month = $this_month - 1;

        $stores = Store::orderBy('store_group_code', 'asc')->get();
        
        if ($request->type == 'oli') {
            $type = ['オイル'];
        } elseif ($request->type == 'battery') {
            $type = ['バッテリー'];
        } elseif ($request->type == 'velg') {
            $type = ['ホイール'];
        } elseif ($request->type == 'tire')  {
            $type = ['タイヤ'];
        } else {
            $type = ['オイル', 'バッテリー', 'ホイール', 'タイヤ'];
        }

        foreach ($stores as $store) {
            $store->stock_in_last_month = BsStock::select(DB::raw("SUM(bs_stocks.amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $store->stock_out_last_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->stock_in_this_month = BsStock::select(DB::raw("SUM(bs_stocks.amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $store->stock_out_this_month = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            // $store->stock_out_this_month_nr = Stock::select(DB::raw("SUM(stocks.amount) AS total"))
            //                                     ->leftJoin('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
            //                                     ->whereMonth('stock_datetime', $this_month)
            //                                     ->whereYear('stock_datetime', $this_year)
            //                                     ->where('arrow', 'out')
            //                                     ->where('store_code', $store->code)
            //                                     ->whereIn('type', $type)
            //                                     ->where('bs_stocks.jan_code', NULL)
            //                                     ->first()
            //                                     ->total;

            // $store->price_stock_out_this_month_nr = Stock::select(DB::raw("SUM(stocks.price) AS total"))
            //                                     ->leftJoin('bs_stocks', 'bs_stocks.jan_code', '=', 'stocks.jan_code')
            //                                     ->whereMonth('stock_datetime', $this_month)
            //                                     ->whereYear('stock_datetime', $this_year)
            //                                     ->where('arrow', 'out')
            //                                     ->where('store_code', $store->code)
            //                                     ->whereIn('type', $type)
            //                                     ->where('bs_stocks.jan_code', NULL)
            //                                     ->first()
            //                                     ->total;

            $store->price_stock_in_last_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $store->price_stock_out_last_month = Stock::select(DB::raw("SUM(price) AS total"))
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->price_stock_in_this_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('client_code', $store->code_from_bs)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $store->price_stock_out_this_month = Stock::select(DB::raw("SUM(price) AS total"))
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $store->barang_titip_this_month = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;
                                               
            $store->price_barang_titip_this_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;

            $store->barang_titip_last_month = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;
                                               
            $store->price_barang_titip_last_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('client_code', $store->code_from_bs)
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;

            $store->harga_stock_out_asli_this_month = Stock::select(DB::raw("SUM(stock_masters.price*stocks.amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('stocks.type', $type)
                                                ->first()
                                                ->total;

            $store->harga_stock_out_asli_last_month = Stock::select(DB::raw("SUM(stock_masters.price*stocks.amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                                ->whereMonth('stock_datetime', '<', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('stocks.type', $type)
                                                ->first()
                                                ->total;

            // $store->stock_out_this_month = $store->stock_out_this_month - $store->stock_out_this_month_nr; 
            // $store->price_stock_out_this_month = $store->price_stock_out_this_month - $store->price_stock_out_this_month_nr; 

            $store->stock_last_month = $store->stock_in_last_month - $store->stock_out_last_month + $store->barang_titip_last_month;
            $store->price_last_month = $store->price_stock_in_last_month - $store->harga_stock_out_asli_last_month + $store->price_barang_titip_last_month;
            $store->total_stock = $store->stock_last_month + ($store->stock_in_this_month - $store->stock_out_this_month) + $store->barang_titip_this_month;
            $store->total_price_this_month = $store->price_last_month + $store->price_stock_in_this_month - $store->harga_stock_out_asli_this_month;
            
            $store->date = Stock::select('created_at')
                                    ->orderBy('created_at', 'desc')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('created_at', $this_month)
                                    ->limit(1)
                                    ->first();

            $store->date = $store->date == null ? '-' : date('Y年m月d日', strtotime($store->date->created_at));

        }

        // return $stores;

        return view('admin.new_report_all', compact('stores'));
    }

    public function closeBookApi(Request $request)
    {
        $date = $request->has('date') ? $request->date : today();
        $total_price = 0;
        $total_amount = 0;
        $total_close_book = 0;
        $total_price_close_book = 0;

        if (auth()->user()->isCentral() && $request->has('store'))
            $store_code = $request->store;
        elseif (auth()->user()->isCentral() && !$request->has('store'))
            $store_code = \App\Store::first()->code;
        else 
            $store_code = auth()->user()->store_code;
        
        $store = Store::where('code', $store_code)->first();

        $stocks = StockMaster::select('brand', 'version', 'size', 'stock_masters.type', 'bs_stocks.jan_code', DB::raw("SUM(amount) as amount"), 'basic_price')
                                ->rightJoin('bs_stocks', 'bs_stocks.jan_code', '=', 'stock_masters.jan_code')
                                ->whereIn('type', ['オイル', 'バッテリー', 'ホイール', 'タイヤ'])
                                ->whereMonth('receipt_date', '<=', date('m', strtotime($date)))
                                ->where('client_code', $store->code_from_bs)
                                ->groupBy('bs_stocks.jan_code')
                                ->get();

        foreach ($stocks as $key => $stock) {
            $stock->stock_out = Stock::select(DB::raw("SUM(amount) as total"))
                                    ->whereMonth('stock_datetime', date('m', strtotime($date)))
                                    ->where('store_code', $store->code)
                                    ->where('jan_code', $stock->jan_code)
                                    ->where('arrow', 'out')
                                    ->groupBy('jan_code')
                                    ->first();

            $stock->close_book = Stock::select(DB::raw("SUM(amount) as total"))
                                    ->whereMonth('stock_datetime', date('m', strtotime($date)))
                                    ->where('store_code', $store->code)
                                    ->where('jan_code', $stock->jan_code)
                                    ->where('arrow', 'close')
                                    ->groupBy('jan_code')
                                    ->first();

            $stock->stock_out = $stock->stock_out == null ? 0 : $stock->stock_out->total;
            $stock->close_book = $stock->close_book == null ? 0 : $stock->close_book->total;
            $stock->total = $stock->amount - $stock->stock_out;

            $stock->price = StockMaster::select(DB::raw("SUM(price*'$stock->total') as total"))
                                        ->where('jan_code', $stock->jan_code)
                                        ->first();

            $stock->price = $stock->price == null ? 0 : $stock->price->total;
            $total_price = $total_price + $stock->price;
            $total_amount = $total_amount + $stock->total;
        }

        $total_close_book = Stock::select(DB::raw("SUM(amount) as total"))
                                    ->where('arrow', 'close')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('stock_datetime', '=', date('m', strtotime($date)))
                                    ->first()
                                    ->total;

        $total_price_close_book = Stock::select(DB::raw("SUM(amount*stock_masters.price) as total"))
                                    ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                                    ->where('arrow', 'close')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('stock_datetime', '=', date('m', strtotime($date)))
                                    ->first()
                                    ->total;

        $datatables = datatables()->of($stocks)->addIndexColumn()
                        ->editColumn('basic_price', function ($stock) {
                             return currency($stock->basic_price, 'jp');
                        })->editColumn('difference', function ($stock) {
                             return number_format($stock->total - $stock->close_book);
                        })->with([
                            'totalAmount' => number_format($total_amount),
                            'totalPrice' => currency($total_price, 'jp'),
                            'totalCloseBook' => number_format($total_close_book),
                            'totalPriceCloseBook' => currency($total_price_close_book, 'jp'),
                        ]);        

        return $datatables->make(true);
    }

}