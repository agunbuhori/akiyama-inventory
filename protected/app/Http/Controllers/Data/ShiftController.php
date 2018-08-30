<?php

namespace App\Http\Controllers\Data;

use App\Shift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->role === 1) {
            $date = $request->has('date') ? $request->date : today();
            $store_code = request()->has('store') ? $request->store : \App\Store::first()->code;

            $shifts = Shift::select('shifts.id', 'shifts.created_at', 'shifts.amount', 'shifts.status', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
                                ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
                                ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
                                ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
                                ->where('status', 'done')
                                ->where('shifts.to_store' , $store_code);

            switch ($request->filter) {
                case 'per_day':
                    $shifts = $shifts->whereDate('shifts.created_at', 'like', date('Y-m-d', strtotime($date)).'%');
                    break;
                case 'per_month':
                    $shifts = $shifts->where('shifts.created_at', 'like', date('Y-m', strtotime($date)).'%');
                    break;
                case 'per_year':
                    $shifts = $shifts->whereYear('shifts.created_at', date('Y', strtotime($date)));
                    break;
                default:
                    $shifts = $shifts->where('shifts.created_at', 'like', date('Y-m', strtotime($date)).'%');
            }

            $datatables = datatables()->of($shifts)->addIndexColumn();

            return $datatables->make(true);

        } else {
            $shifts = Shift::select('shifts.*', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
                                ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
                                ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
                                ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
                                ->where('shifts.from_store', '=', auth()->user()->store_code)
                                ->get();

            $datatables = datatables()->of($shifts)->addIndexColumn();

            return $datatables->make(true);
        }
        
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
            'amount' => 'required'
        ]);

        // $number = '0123456789';
        // $code = '';

        // for ($i=0;$i<6;$i++){
        //     $code.=$number[rand(0, 9)];
        // }

        $shift = new Shift;

        $shift->code = $request->id;
        $shift->jan_code = $request->jan_code;
        $shift->amount = $request->amount;
        $shift->from_store = auth()->user()->store_code;
        $shift->to_store = $request->to_store;
        $shift->status = 'pending';

        $shift->save();

        return back();
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
        if ($request->status) {
            $shift->status = $request->status;
        } else {
            $shift->amount = $request->amount;
        }

        $shift->save();
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift)
    {
        $shift->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function show($id) 
    {
        if (auth()->user()->role == 1)
            \App\Shift::where('id', $id)->update(['read' => 1]);
        else
            \App\Shift::where('id', $id)->update(['read_store' => 1]);

        return back();
    }
}
