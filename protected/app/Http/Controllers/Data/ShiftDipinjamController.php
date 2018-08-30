<?php

namespace App\Http\Controllers\Data;

use App\Stock;
use App\Shift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShiftDipinjamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::select('shifts.*', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
                            ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
                            ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
                            ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
                            ->where('shifts.to_store', '=', auth()->user()->store_code)
                            ->whereIn('shifts.status', ['pending', 'done', 'fail'])
                            ->get();

        $datatables = datatables()->of($shifts)->addIndexColumn();

        return $datatables->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shift $shift)
    {
        if ($request->status == 'done') {
            Shift::where('id', $request->id)->update(['status' => 'done']);
            Stock::where('jan_code', $request->jan_code)
                        ->where('store_code', auth()->user()->store_code)
                        ->where('id', $request->code)
                        ->decrement('amount', $request->amount);
        }
        else {
            Shift::where('id', $request->id)->update(['status' => 'fail']);
        }

    }

}
