<?php

namespace App\Http\Controllers\Data;

use App\User;
use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::select('stores.id', 'stores.code', 'stores.code_from_bs', 'stores.name', 'stores.email', 'stores.contact', 'stores.address', 'store_groups.name as group', 'store_group_code')
                            ->join('store_groups', 'stores.store_group_code', '=', 'store_groups.code');
                        // ->where('users', 'users.store_code', '=', 'stores.code')->get();

        $datatables = datatables()->of($stores)->addIndexColumn()->addColumn('username', function ($store) {
            return $store->user ? $store->user->name : null;
        });

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
            'name' => 'required',
            'email' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'user_id' => 'required',
            'store_code' => 'required',
            'store_group_code' => 'required'
        ]);

        $store = new Store;

        $store->name = $request->name;
        $store->email = $request->email;
        $store->contact = $request->contact;
        $store->address = $request->address;
        $store->code = $request->store_code;
        $store->code_from_bs = $request->code_from_bs;
        $store->store_group_code = $request->store_group_code;

        $store->save();

        $user = new User;

        $user->fullname = $request->name;
        $user->name = $request->user_id;
        $user->email = $request->email;
        $user->role = 2;
        $user->store_code = $request->store_code;

        $user->save();

        return $request;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        if ($request->password) {
            \App\User::where('store_code', $request->store_code)->update(['password' => bcrypt($request->password) ]);
        } else {
            $this->validate($request,[
                'name' => 'required',
                'email' => 'required',
                'contact' => 'required',
                'store_code' => 'required',
                'code_from_bs' => 'required',
                // 'address' => 'required',
            ]);

            $store->name = $request->name;
            $store->email = $request->email;
            $store->contact = $request->contact;
            $store->address = $request->address;
            $store->code = $request->store_code;
            $store->code_from_bs = $request->code_from_bs;

            if ($request->user_id) 
                \App\User::where('store_code', $request->store_code)->update(['name' => $request->user_id ]);

            $store->save();
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        $store->delete();
    }
}
