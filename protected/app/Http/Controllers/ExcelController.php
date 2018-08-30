<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use App\Store;
use App\Shift;
use App\Stock;
use App\BsStock;
use App\StoreGroup;
use App\StockMaster;
use Illuminate\Http\Request;

class ExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('central')->only(['importStockMaster', 'importBsStock', 'importStockMasterForm', 'importBsStockForm']);
    }

	public function importStockMasterForm()
	{
		return view('admin.import_stock_master');
	}

    public function importStockMaster(Request $request)
    {
    	$this->validate($request, [
    	    'csv' => 'required'
    	]);

        $stockMasters = Excel::load($request->csv, function($reader) { $reader->noHeading = true; }, 'UTF-8')->ignoreEmpty()->toArray();
        $userId = auth()->user()->id;
        $invalidMessage = __('trans.'.snake_case('excel file format is invalid or wrong'));

        unset($stockMasters[0]);
    	$data = [];
        $errors = 0;

        // return $stockMasters;

        if ($request->type === 'tire') {
            foreach ($stockMasters as $stockMaster) { 
                if (count($stockMaster) != 10 
                || is_null($stockMaster[8])
                || ! array_filter($stockMaster)
                || ! preg_match('/[0-9]+/', $stockMaster[8]))
                {
                    $errors++;
                    continue;
                }

                $data[] = [
                    'season' => $stockMaster[0],
                    'code' => $stockMaster[1],
                    'brand' => $stockMaster[2],
                    'version' => $stockMaster[3],
                    'size' => $stockMaster[4],
                    'section' => $stockMaster[5],
                    'series' => $stockMaster[6],
                    'rim' => $stockMaster[7],
                    'jan_code' => $stockMaster[8],
                    'price' => $stockMaster[9],
                    'type' => 'タイヤ',
                    'user_id' => $userId
                ];
            }
        } elseif ($request->type === 'battery') {
            foreach ($stockMasters as $stockMaster) { 
                if (count($stockMaster) != 6 || is_null($stockMaster[4]))
                    continue;

                $data[] = [
                    // 'season' => $stockMaster[0],
                    'code' => $stockMaster[0],
                    'brand' => $stockMaster[1],
                    'version' => $stockMaster[2],
                    'size' => $stockMaster[3],
                    // 'section' => $stockMaster[5],
                    // 'series' => $stockMaster[6],
                    // 'rim' => $stockMaster[7],
                    'jan_code' => $stockMaster[4],
                    'price' => $stockMaster[5],
                    'type' => 'バッテリー',
                    'user_id' => $userId
                ];
            }
        } elseif ($request->type === 'velg') {
            foreach ($stockMasters as $stockMaster) { 
                if (count($stockMaster) != 6 || is_null($stockMaster[4]))
                    continue;

                $data[] = [
                    // 'season' => $stockMaster[0],
                    'code' => $stockMaster[0],
                    'brand' => $stockMaster[1],
                    'version' => $stockMaster[2],
                    'size' => $stockMaster[3],
                    // 'section' => $stockMaster[5],
                    // 'series' => $stockMaster[6],
                    // 'rim' => $stockMaster[7],
                    'jan_code' => $stockMaster[4],
                    'price' => $stockMaster[5],
                    'type' => 'ホイール',
                    'user_id' => $userId
                ];
            }
        }

    	StockMaster::insert($data);

    	return back()->with('amount', count($data));
    }

    public function importBsStockForm()
    {
        return view('admin.import_bs_stock');
    }

    public function importBsStock(Request $request)
    {
        $bsStocks = Excel::load($request->csv, function($reader) { $reader->noHeading = true; }, 'UTF-8')->toArray();
        $userId = auth()->user()->id;
        $invalidMessage = __('trans.'.snake_case('excel file format is invalid or wrong'));

        $data = [];
        $errors = 0;

        // return $bsStocks;

        if ($request->titip == 'barang_titip') {
            foreach ($bsStocks as $bsStock) {
                if (count($bsStock) < 12 || $bsStock[11] == null) {
                    $errors++;
                    continue;
                }

                $data[] = [
                    'client_code' => $bsStock[1],
                    'company_name' => $bsStock[2],
                    'group' => $bsStock[5],
                    'stock_code' => $bsStock[6],
                    'stock_name' => $bsStock[7],
                    'receipt_date' => $bsStock[8],
                    'receipt_number' => $bsStock[9],
                    'amount' => $bsStock[10],
                    'jan_code' => $bsStock[11],
                    'titip' => 1,
                    'created_at' => now()
                ];
            }
        } else {
            foreach ($bsStocks as $bsStock) {
                if (count($bsStock) < 15 || $bsStock[8] == null) {
                    $errors++;
                    continue;
                }

                $data[] = [
                    'client_code' => $bsStock[0],
                    'company_name' => $bsStock[1],
                    'receipt_date' => $bsStock[3],
                    'receipt_number' => $bsStock[4],
                    'article' => $bsStock[5],
                    'group' => $bsStock[6],
                    'stock_code' => $bsStock[7],
                    'jan_code' => $bsStock[8],
                    'stock_name' => $bsStock[9],
                    'titip' => 0,
                    'amount' => $bsStock[10],
                    'sell_price' => $bsStock[11],
                    'basic_price' => $bsStock[12],
                    'memo' => $bsStock[13],
                    'titip' => 0,
                    'user_id' => $userId,
                    'created_at' => now()
                ];
            }
        }

        BsStock::insert($data);

        return back()->with('amount', count($data));
    }

    public function importStockForm()
    {
        return view('admin.import_stock');
    }

    public function importStock(Request $request)
    {
        $this->validate($request, [
            'csv' => 'required|mimes:csv,xls,xlsx,txt'
        ]);

        if ($request->csv)
            $rows = Excel::load($request->csv, function($reader) { $reader->noHeading = true; }, 'Shift-JIS')->toArray();


        if (count($rows) === 0 || count($rows[0]) === 0 || count($rows[0]) > 10 || count($rows[0]) < 9)
            return back()->with(['message_error' => 'Invalid format'], 422);

        if (preg_match('/[a-zA-Z]+/', $rows[0][0]))
            unset($rows[0]);

        $data = [];
        // $arrow = $request->has('arrow') ? $request->arrow : 'in';
        // $fileName = date('YmdHis_').$arrow.'.csv';
        $now = now();
        $store_code = auth()->user()->store_code;

        // return $rows;

        foreach ($rows as $row) {
            if (count($row) < 9 || ! preg_match('/[0-9]+/', $row[0]) || ! preg_match('/[0-9]+/', $row[6])) continue;

            $data[] = [
                'stock_datetime' => convert_date($row[0], $row[1], $row[2]),
                'jan_code' => trim($row[6], ' '), 
                'dealer' => $row[4],
                'type' => $row[5], 
                'amount' => $row[7], 
                'price' => $row[8],
                'arrow' => $request->arrow,
                'created_at' => $now,
                'store_code' => $store_code
            ];
        }

        Stock::insert($data);

        return back()->with('amount', count($data));
    }

    public function importCloseBookForm()
    {
        return view('admin.import_close_book');
    }

    public function importCloseBook(Request $request)
    {
        $this->validate($request, [
            'csv' => 'required|mimes:csv,xls,xlsx,txt'
        ]);

        if ($request->csv)
            $rows = Excel::load($request->csv, function($reader) { $reader->noHeading = true; }, 'Shift-JIS')->toArray();

        $data = [];
        $now = now();
        $store_code = auth()->user()->store_code;

        // return $rows;

        foreach ($rows as $row) {

            $data[] = [
                'stock_datetime' => convert_date($row[0], $row[1], $row[2]),
                'jan_code' => trim($row[5], ' '), 
                'type' => $row[4], 
                'amount' => $row[6], 
                'price' => 0,
                'arrow' => 'close',
                'created_at' => $now,
                'store_code' => $store_code
            ];
        }

        Stock::insert($data);

        return back()->with('amount', count($data));
    }

    public function exportShift() 
    {
        $shifts = Shift::select('shifts.*', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
                        ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
                        ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
                        ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
                        ->where('shifts.status', 'done')
                        ->get();
        $data = [];

        foreach ($shifts as $key => $shift) {
            $data[$key][] = date('Y年m月d日', strtotime($shift->created_at));
            $data[$key][] = $shift->to_store;
            $data[$key][] = $shift->from_store;
            $data[$key][] = $shift->type;
            $data[$key][] = $shift->brand;
            $data[$key][] = $shift->size;
            $data[$key][] = $shift->amount;
        }
        Excel::create('転送', function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                
                $sheet->fromArray($data);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue("日付");
                });

                $sheet->cell('B1', function($cell) {
                    $cell->setValue("転出店舗");
                });

                $sheet->cell('C1', function($cell) {
                    $cell->setValue("転受店舗");
                });

                $sheet->cell('D1', function($cell) {
                    $cell->setValue("種類");
                });

                $sheet->cell('E1', function($cell) {
                    $cell->setValue("ブランド");
                });

                $sheet->cell('F1', function($cell) {
                    $cell->setValue("サイズ");
                });

                $sheet->cell('G1', function($cell) {
                    $cell->setValue("数量");
                });

            });

        })->download('csv');
    }

    public function exportStock(Request $request) 
    {
        $stocks = Stock::select('stock_datetime', 'brand', 'version', 'size', 'stock_masters.type', DB::raw("SUM(amount) as amount"), DB::raw("SUM(stocks.price) as price"))
                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
                            ->where('store_code', $request->store)
                            ->where('arrow', 'out')
                            ->orderBy('stock_datetime', 'asc')
                            ->groupBy('stocks.jan_code');

        $date = $request->year.'-'.$request->month.'-'.$request->day;

        if ($request->filter == 'per_year') {
            $stocks = $stocks->whereYear('stock_datetime', $request->year);
        } else if ($request->filter == 'per_month') {
            $stocks = $stocks->whereYear('stock_datetime', $request->year)->whereMonth('stock_datetime', $request->month);
        } elseif ($request->filter == 'per_day') {
            $stocks = $stocks->whereDate('stock_datetime', $date);
        }

        $stocks = $stocks->get();

        $data = [];

        foreach ($stocks as $key => $stock) {
            $data[$key][] = date('Y年m月d日', strtotime($stock->stock_datetime));
            $data[$key][] = $stock->brand;
            $data[$key][] = $stock->version;
            $data[$key][] = $stock->size;
            $data[$key][] = $stock->type;
            $data[$key][] = $stock->amount;
            $data[$key][] = currency($stock->price, 'jp');
        }

        Excel::create('出荷処理', function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                
                $sheet->fromArray($data);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue("日付");
                });

                $sheet->cell('B1', function($cell) {
                    $cell->setValue("ブランド");
                });

                $sheet->cell('C1', function($cell) {
                    $cell->setValue("パタン");
                }); 

                $sheet->cell('D1', function($cell) {
                    $cell->setValue("サイズ");
                });

                $sheet->cell('E1', function($cell) {
                    $cell->setValue("区分");
                });

                $sheet->cell('F1', function($cell) {
                    $cell->setValue("数量");
                });

                $sheet->cell('G1', function($cell) {
                    $cell->setValue("売上金額");
                });

            });

        })->download('csv');
        
    }

    public function exportReport(Request $request) 
    {
        $client_code = Store::select('code_from_bs')->where('code', $request->store)->first()->code_from_bs;
        // $date = $request->year.'-'.$request->month.'-31';

        $date = '2018-'.$request->month.'-31';

        // return $date;

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

        $stocks = BsStock::select('type', 'brand', 'size', 'bs_stocks.jan_code', 'stock_masters.price as basic_price', 'memo', 'receipt_date')
                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date)))
                            ->where('client_code', $client_code)
                            ->whereIn('type', $type)
                            ->orderBy('receipt_date', 'desc')
                            ->groupBy('bs_stocks.jan_code')
                            ->get();

        foreach ($stocks as $stock) {
            $stock->stock_in_last_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('client_code', $client_code)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->where('titip', 0)
                                            ->whereIn('type', $type)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->barang_titip_last_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('client_code', $client_code)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('titip', 1)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->stock_out_last_month = Stock::select(DB::raw("SUM(amount) as total"))
                                            ->where('stock_datetime', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('store_code', $request->store)
                                            ->where('jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('arrow', 'out')
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->stock_in_this_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('client_code', $client_code)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('titip', 0)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->barang_titip_this_month = BsStock::select(DB::raw("SUM(amount) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('client_code', $client_code)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('titip', 1)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->stock_in_store = Stock::select(DB::raw("SUM(amount) as total"))
                                            ->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('store_code', $request->store)
                                            ->where('jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('arrow', 'in')
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->stock_out_this_month = Stock::select(DB::raw("SUM(amount) as total"))
                                            ->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('store_code', $request->store)
                                            ->where('jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->where('arrow', 'out')
                                            ->groupBy('jan_code')
                                            ->first();
            $stock->sell_price = Stock::select(DB::raw("SUM(price) as total"))
                                            ->where('stock_datetime', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('store_code', $request->store)
                                            ->where('jan_code', $stock->jan_code)
                                            ->where('arrow', 'out')
                                            ->whereIn('type', $type)
                                            ->groupBy('jan_code')
                                            ->first();
           $stock->price_in_last_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', '<=', date('Y-m-31', strtotime($date.'-1 month')))
                                            ->where('client_code', $client_code)
                                            ->where('bs_stocks.jan_code', $stock->jan_code)
                                            ->whereIn('type', $type)
                                            ->groupBy('bs_stocks.jan_code')
                                            ->first();
            $stock->price_in_this_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) as total"))
                                            ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                            ->where('receipt_date', 'like', date('Y-m', strtotime($date)).'%')
                                            ->where('client_code', $client_code)
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
            $stock->total_price_this_month = $stock->price_in_last_month + $stock->price_in_this_month - $stock->sell_price;
        }

        $data = [];

        foreach ($stocks as $key => $stock) {
            $data[$key][] = date('Y年m月d日', strtotime($stock->receipt_date));
            $data[$key][] = $stock->type;
            $data[$key][] = $stock->brand;
            $data[$key][] = $stock->size;
            $data[$key][] = number_format($stock->stock_last_month);
            $data[$key][] = number_format($stock->stock_in_this_month);
            $data[$key][] = number_format($stock->barang_titip_this_month);
            $data[$key][] = currency($stock->basic_price, 'jp');
            $data[$key][] = number_format($stock->stock_out_this_month);
            $data[$key][] = currency($stock->sell_price, 'jp');
            $data[$key][] = number_format($stock->stock_this_month);
            $data[$key][] = currency($stock->total_price_this_month, 'jp');
        }

        Excel::create('各店舗集計', function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                
                $sheet->fromArray($data);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue("日付");
                });

                $sheet->cell('B1', function($cell) {
                    $cell->setValue("区分");
                });

                $sheet->cell('C1', function($cell) {
                    $cell->setValue("ブランド");
                });

                $sheet->cell('D1', function($cell) {
                    $cell->setValue("サイズ");
                });

                $sheet->cell('E1', function($cell) {
                    $cell->setValue("前月在庫 本数");
                });

                $sheet->cell('F1', function($cell) {
                    $cell->setValue("入荷");
                });

                $sheet->cell('G1', function($cell) {
                    $cell->setValue("預かり");
                });

                $sheet->cell('H1', function($cell) {
                    $cell->setValue("単価");
                });

                $sheet->cell('I1', function($cell) {
                    $cell->setValue("出荷");
                });

                $sheet->cell('J1', function($cell) {
                    $cell->setValue("売上金額");
                });

                $sheet->cell('K1', function($cell) {
                    $cell->setValue("当月在庫本数");
                });

                $sheet->cell('L1', function($cell) {
                    $cell->setValue("当月在庫金額");
                });

            });

        })->download('csv');
    }

    public function exportReportAll(Request $request) 
    {

        $type = $request->type ? $request->type : 'all';
        $this_month = $request->month ? $request->month : date('m');
        $last_month = $this_month - 1;
        $this_year = '2018';

        if ($type == 'oli') {
            $type = ['オイル'];
        } elseif ($type == 'battery') {
            $type = ['バッテリー'];
        } elseif ($type == 'velg') {
            $type = ['ホイール'];
        } elseif ($type == 'tire')  {
            $type = ['タイヤ'];
        } else {
            $type = ['オイル', 'バッテリー', 'ホイール', 'タイヤ'];
        }

        $total_stock_last_month = 0;
        $total_price_last_month = 0;
        $total_stock_in = 0;
        $total_price_stock_in = 0;
        $total_stock_out = 0;
        $total_price_stock_out = 0;
        $total_stock_this_month = 0;
        $total_price_this_month = 0;

        $stores = Store::orderBy('store_group_code', 'asc')->get();

        $groups = StoreGroup::where('code', '!=', '2712')->get();

        foreach ($groups as $group) {
            $group->stock_in_last_month = BsStock::select(DB::raw("SUM(bs_stocks.amount) AS total"))
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('store_group_code', $group->code)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $group->stock_out_last_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_group_code', $group->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $group->stock_in_this_month = BsStock::select(DB::raw("SUM(bs_stocks.amount) AS total"))
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('store_group_code', $group->code)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $group->stock_out_this_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_group_code', $group->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $group->price_stock_in_last_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) AS total"))
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('store_group_code', $group->code)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $group->price_stock_out_last_month = Stock::select(DB::raw("SUM(price) AS total"))
                                                ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                                ->whereMonth('stock_datetime', '<=', $last_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_group_code', $group->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $group->price_stock_in_this_month = BsStock::select(DB::raw("SUM(amount*stock_masters.price) AS total"))
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('store_group_code', $group->code)
                                                ->where('titip', '0')
                                                ->first()
                                                ->total;

            $group->price_stock_out_this_month = Stock::select(DB::raw("SUM(price) AS total"))
                                                ->leftJoin('stores', 'stores.code', '=', 'stocks.store_code')
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_group_code', $group->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

            $group->barang_titip_this_month = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->where('store_group_code', $group->code)
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;
                                               
            $group->price_barang_titip_this_month = BsStock::select(DB::raw("SUM(bs_stocks.amount*price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->where('store_group_code', $group->code)
                                                ->whereMonth('receipt_date', $this_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('stock_masters.type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;

            $group->barang_titip_last_month = BsStock::select(DB::raw("SUM(amount) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->where('store_group_code', $group->code)
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;
                                               
            $group->price_barang_titip_last_month = BsStock::select(DB::raw("SUM(bs_stocks.amount*price) AS total"))
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->leftJoin('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                                ->where('store_group_code', $group->code)
                                                ->whereMonth('receipt_date', '<=', $last_month)
                                                ->whereYear('receipt_date', $this_year)
                                                ->whereIn('stock_masters.type', $type)
                                                ->where('titip', '1')
                                                ->first()
                                                ->total;

            $group->stock_last_month = $group->stock_in_last_month - $group->stock_out_last_month + $group->barang_titip_last_month;
            $group->price_last_month = $group->price_stock_in_last_month - $group->price_stock_out_last_month + $group->price_barang_titip_last_month;
            $group->total_stock = $group->stock_last_month + ($group->stock_in_this_month - $group->stock_out_this_month) + $group->barang_titip_this_month;
            $group->total_price_this_month = $group->price_last_month + $group->price_stock_in_this_month - $group->price_stock_out_this_month + $group->price_barang_titip_this_month;
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

            $store->stock_out_this_month = Stock::select(DB::raw("SUM(amount) AS total"))
                                                ->whereMonth('stock_datetime', $this_month)
                                                ->whereYear('stock_datetime', $this_year)
                                                ->where('arrow', 'out')
                                                ->where('store_code', $store->code)
                                                ->whereIn('type', $type)
                                                ->first()
                                                ->total;

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

            $store->stock_last_month = $store->stock_in_last_month - $store->stock_out_last_month + $store->barang_titip_last_month;
            $store->price_last_month = $store->price_stock_in_last_month - $store->price_stock_out_last_month + $store->price_barang_titip_last_month;
            $store->total_stock = $store->stock_last_month + ($store->stock_in_this_month - $store->stock_out_this_month) + $store->barang_titip_this_month;
            $store->total_price_this_month = $store->price_last_month + $store->price_stock_in_this_month - $store->price_stock_out_this_month + $store->price_barang_titip_this_month;

            $total_stock_last_month += $store->stock_last_month;
            $total_price_last_month += $store->price_last_month;
            $total_stock_in += $store->stock_in_this_month;
            $total_price_stock_in += $store->price_stock_in_this_month;
            $total_stock_out += $store->stock_out_this_month;
            $total_price_stock_out += $store->price_stock_out_this_month;
            $total_stock_this_month += $store->total_stock;
            $total_price_this_month += $store->total_price_this_month;

            $store->date = Stock::select('created_at')
                                    ->orderBy('created_at', 'desc')
                                    ->where('store_code', $store->code)
                                    ->whereMonth('created_at', $this_month)
                                    ->limit(1)
                                    ->first();
            $store->date = $store->date == null ? '-' : date('Y年m月d日', strtotime($store->date->created_at));

            // $store->date = Stock::select('created_at')->limit(1)->orderBy('created_at', 'desc')->first()->created_at;
        }

        $data = [];
        $data_group = [];

        foreach ($stores as $key => $store) {
            $data[$key][] = $store->date;
            $data[$key][] = $store->name;
            $data[$key][] = number_format($store->stock_last_month);
            $data[$key][] = currency($store->price_last_month, 'jp');
            $data[$key][] = number_format($store->stock_in_this_month);
            $data[$key][] = currency($store->price_stock_in_this_month, 'jp');
            $data[$key][] = number_format($store->stock_out_this_month);
            $data[$key][] = currency($store->price_stock_out_this_month, 'jp');
            $data[$key][] = number_format($store->barang_titip_this_month);
            $data[$key][] = currency($store->price_barang_titip_this_month, 'jp');
            $data[$key][] = number_format($store->total_stock);
            $data[$key][] = currency($store->total_price_this_month, 'jp');
        }

        foreach ($groups as $key => $group) {
            $data_group[$key][] = '';
            $data_group[$key][] = $group->name;
            $data_group[$key][] = number_format($group->stock_last_month);
            $data_group[$key][] = currency($group->price_last_month, 'jp');
            $data_group[$key][] = number_format($group->stock_in_this_month);
            $data_group[$key][] = currency($group->price_stock_in_this_month, 'jp');
            $data_group[$key][] = number_format($group->stock_out_this_month);
            $data_group[$key][] = currency($group->price_stock_out_this_month, 'jp');
            $data_group[$key][] = number_format($group->barang_titip_this_month);
            $data_group[$key][] = currency($group->price_barang_titip_this_month, 'jp');
            $data_group[$key][] = number_format($group->total_stock);
            $data_group[$key][] = currency($group->total_price_this_month, 'jp');
        }

        Excel::create('全店舗集計', function($excel) use($data, $data_group, $total_stock_last_month, $total_price_last_month, $total_stock_in, $total_stock_out, $total_price_stock_in, $total_price_stock_out, $total_stock_this_month, $total_price_this_month) {

            $excel->sheet('Sheetname', function($sheet) use($data, $data_group, $total_stock_last_month, $total_price_last_month, $total_stock_in, $total_stock_out, $total_price_stock_in, $total_price_stock_out, $total_stock_this_month, $total_price_this_month) {

                $sheet->fromArray($data);
                $sheet->fromArray($data_group);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue("日付");
                });

                $sheet->cell('B1', function($cell) {
                    $cell->setValue("店舗名");
                });

                $sheet->cell('C1', function($cell) {
                    $cell->setValue("前月在庫 本数");
                });

                $sheet->cell('D1', function($cell) {
                    $cell->setValue("前月在庫総 金額");
                });

                $sheet->cell('E1', function($cell) {
                    $cell->setValue("当月仕入れ本数");
                });

                $sheet->cell('F1', function($cell) {
                    $cell->setValue("当月仕入れ総金額");
                });

                $sheet->cell('G1', function($cell) {
                    $cell->setValue("当月売上本数");
                });

                $sheet->cell('H1', function($cell) {
                    $cell->setValue("当月売上総金額");
                });

                $sheet->cell('I1', function($cell) {
                    $cell->setValue("預かり");
                });

                $sheet->cell('J1', function($cell) {
                    $cell->setValue("預かり総金額");
                });

                $sheet->cell('K1', function($cell) {
                    $cell->setValue("当月在庫本数");
                });

                $sheet->cell('L1', function($cell) {
                    $cell->setValue("当月在庫総金額");
                });

                //TOTAL LAST MONTH

                $sheet->cell('N3', function($cell) {
                    $cell->setValue("前月在庫 本数");
                });

                $sheet->cell('N4', function($cell) {
                    $cell->setValue("前月在庫総 金額");
                });

                $sheet->cell('O3', function($cell) use($total_stock_last_month) {
                    $cell->setValue(number_format($total_stock_last_month));
                });

                $sheet->cell('O4', function($cell) use($total_price_last_month) {
                    $cell->setValue(currency($total_price_last_month, 'jp'));
                });

                //TOTAL STOCK IN

                $sheet->cell('N6', function($cell) {
                    $cell->setValue("当月仕入れ本数");
                });

                $sheet->cell('N7', function($cell) {
                    $cell->setValue("当月仕入総金額");
                });

                $sheet->cell('O6', function($cell) use($total_stock_in) {
                    $cell->setValue(number_format($total_stock_in));
                });

                $sheet->cell('O7', function($cell) use($total_price_stock_in) {
                    $cell->setValue(currency($total_price_stock_in, 'jp'));
                });

                //TOTAL STOCK OUT

                $sheet->cell('N9', function($cell)  {
                    $cell->setValue("当月売上本数");
                });

                $sheet->cell('N10', function($cell) {
                    $cell->setValue("当月売上総金額");
                });

                $sheet->cell('O9', function($cell) use($total_stock_out) {
                    $cell->setValue(number_format($total_stock_out));
                });

                $sheet->cell('O10', function($cell) use($total_price_stock_out) {
                    $cell->setValue(currency($total_price_stock_out, 'jp'));
                });

                //TOTAL THIS MONTH

                $sheet->cell('N12', function($cell) {
                    $cell->setValue("当月在庫本数");
                });

                $sheet->cell('N13', function($cell) {
                    $cell->setValue("当月在庫総金額");
                });

                $sheet->cell('O12', function($cell) use($total_stock_this_month) {
                    $cell->setValue(number_format($total_stock_this_month));
                });

                $sheet->cell('O13', function($cell) use($total_price_this_month) {
                    $cell->setValue(currency($total_price_this_month, 'jp'));
                });

                $sheet->cell('B23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('C23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('D23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('E23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('F23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('G23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('H23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('I23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('J23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('K23', function($cell) {
                    $cell->setValue("");
                });

                $sheet->cell('L23', function($cell) {
                    $cell->setValue("");
                });

            });

        })->download('csv');
    }

    public function unregister() {
        
        $unregisters = BsStock::select('bs_stocks.jan_code', 'stock_masters.brand', 'stores.name AS store_name')
                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=','bs_stocks.jan_code')
                                ->addSelect(\DB::raw("SUM(amount) as amounts"))
                                ->join('stores', 'stores.code_from_bs', '=', 'bs_stocks.client_code')
                                ->where('stock_masters.brand', NULL)
                                ->groupBy('jan_code')
                                ->get();

        $data = [];

        foreach ($unregisters as $key => $stock) {
            $data[$key][] = $stock->store_name;
            // $data[$key][] = $stock->type;
            $data[$key][] = $stock->jan_code;
            $data[$key][] = $stock->amounts;
        }

        Excel::create('商品', function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                
                $sheet->fromArray($data);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue("店舗名");
                });

                $sheet->cell('B1', function($cell) {
                    $cell->setValue("JANコード");
                });

                $sheet->cell('C1', function($cell) {
                    $cell->setValue("数量");
                });

            });

        })->download('csv');
    }

    public function exportReportDealer() {
        
        $unregisters = Store::all();

        $data = [];

        foreach ($unregisters as $key => $stock) {
            $data[$key][] = $stock->name;
        }

        Excel::create('車販部', function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                
                $sheet->fromArray($data);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue("店舗");
                });

                $sheet->cell('B1', function($cell) {
                    $cell->setValue("");
                });

            });

        })->download('csv');
    }

    public function exportCloseBook() {
        
        // $unregisters = Store::all();

        $data = [];

        // foreach ($unregisters as $key => $stock) {
        //     $data[$key][] = $stock->name;
        // }

        Excel::create('CLose Book', function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                
                // $sheet->fromArray($data);

                // $sheet->cell('A1', function($cell) {
                //     $cell->setValue("店舗");
                // });

                // $sheet->cell('B1', function($cell) {
                //     $cell->setValue("");
                // });

            });

        })->download('csv');
    }

}
