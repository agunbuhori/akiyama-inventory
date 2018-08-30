<?php

namespace App\Http\Controllers\Data;

use DB;
use App\StoreGroup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'name' => 'required'
        ]);

        $storeGroup = new StoreGroup;

        $storeGroup->code = $request->code;
        $storeGroup->name = $request->name;

        $storeGroup->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreGroup  $storeGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreGroup $storeGroup)
    {
        \App\StoreGroup::where('id', $request->id)->update(['name' => $request->name, 'code' => $request->code]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StoreGroup  $storeGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\StoreGroup::find($id)->delete();
    }
}
