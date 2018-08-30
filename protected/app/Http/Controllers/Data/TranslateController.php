<?php

namespace App\Http\Controllers\Data;

use App\Translate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TranslateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $translates = Translate::select('id', 'key', 'english', 'indonesia', 'japanese');

        $datatables = datatables()->of($translates)->addIndexColumn();

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
            'key' => 'required',
            'english' => 'required',
            'indonesia' => 'required',
            'japanese' => 'required'
        ]);

        $translate = new Translate;

        $translate->key = $request->key;
        $translate->english = $request->english;
        $translate->indonesia = $request->indonesia;
        $translate->japanese = $request->japanese;

        $translate->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Translate  $translate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Translate $translate)
    {
        $this->validate($request,[
            'key' => 'required',
            'english' => 'required',
            'indonesia' => 'required',
            'japanese' => 'required'
        ]);

        $translate->key = $request->key;
        $translate->english = $request->english;
        $translate->indonesia = $request->indonesia;
        $translate->japanese = $request->japanese;

        $translate->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Translate  $translate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Translate $translate)
    {
        if (! request()->has('datas'))
            $translate->delete();
        else
            $translate->whereIn('id', request()->datas)->delete();
    }
}
