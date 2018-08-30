<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public function bs_stock()
    {
    	return $this->belongsTo(BsStock::class, 'jan_code', 'jan_code');
    }

    public function stock_master()
    {
    	return $this->belongsTo(StockMaster::class, 'jan_code', 'jan_code');
    }
}
