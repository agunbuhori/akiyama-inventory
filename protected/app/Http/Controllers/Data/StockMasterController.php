<?php

namespace App\Http\Controllers\Data;

use App\StockMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stockMasters = StockMaster::select('id', 'jan_code', 'version', 'size', 'type', 'brand', 'code', 'section', 'series', 'rim', 'price');
        $datatables = datatables()->of($stockMasters)->addIndexColumn();

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
            'type' => 'required'
        ]);

        $stockMaster = new StockMaster;

        $stockMaster->jan_code = $request->jan_code;
        $stockMaster->code = $request->code;
        $stockMaster->brand = $request->brand;
        $stockMaster->version = $request->version;
        $stockMaster->size = $request->size;

        if (preg_match('/^[0-9]+\/[0-9]+[R][0-9]+/', $request->size)) {
            $explode = explode('/', $request->size);
            $stockMaster->section = $explode[0];
            
            $explode = explode('R', $explode[1]);
            $stockMaster->series = $explode[0];
            $stockMaster->rim = preg_replace('/\ [A-Z0-9]+/', '', $explode[1]);
        }

        $stockMaster->launch = $request->launch;
        $stockMaster->price = $request->price;
        $stockMaster->type = $request->type;

        $stockMaster->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StockMaster  $stockMaster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockMaster $stockMaster)
    {
        $this->validate($request,[
            'jan_code' => 'required',
            'type' => 'required'
        ]);

        $stockMaster->jan_code = $request->jan_code;
        $stockMaster->code = $request->code;
        $stockMaster->brand = $request->brand;
        $stockMaster->version = $request->version;
        $stockMaster->size = $request->size;
        
        if (preg_match('/^[0-9]+\/[0-9]+[R][0-9]+/', $request->size)) {
            $explode = explode('/', $request->size);
            $stockMaster->section = $explode[0];
            
            $explode = explode('R', $explode[1]);
            $stockMaster->series = $explode[0];
            $stockMaster->rim = preg_replace('/\ [A-Z0-9]+/', '', $explode[1]);
        }


        $stockMaster->launch = $request->launch;
        $stockMaster->price = $request->price;
        $stockMaster->type = $request->type;

        $stockMaster->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StockMaster  $stockMaster
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockMaster $stockMaster)
    {
        if (! request()->has('datas'))
            $stockMaster->delete();
        else
            $stockMaster->whereIn('id', request()->datas)->delete();
    }
}
