<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResetController extends Controller
{
    public function resetData(Request $request)
    {
    	\DB::table('resets')->insert(['store_code' => 101, 'date' => today() ]);

        return back();
    }
}
