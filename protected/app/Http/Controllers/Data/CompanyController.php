<?php

namespace App\Http\Controllers\Data;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::select('id', 'code', 'name', 'contact_name', 'email', 'contact', 'address');

        $datatables = datatables()->of($companies)->addIndexColumn();

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
            'contact_name' => 'required',
            'code' => 'required',
            'email' => 'required',
            'contact' => 'required',
            'address' => 'required'
        ]);

        $company = new Company;

        $company->name = $request->name;
        $company->contact_name = $request->contact_name;
        $company->code = $request->code;
        $company->email = $request->email;
        $company->contact = $request->contact;
        $company->address = $request->address;

        $company->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $this->validate($request,[
            'name' => 'required',
            'contact_name' => 'required',
            'code' => 'required',
            'email' => 'required',
            'contact' => 'required',
            'address' => 'required'
        ]);

        $company->name = $request->name;
        $company->contact_name = $request->contact_name;
        $company->code = $request->code;
        $company->email = $request->email;
        $company->contact = $request->contact;
        $company->address = $request->address;

        $company->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
    }
}
