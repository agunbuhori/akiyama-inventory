<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BsStock extends Model
{
    public function stock_master()
    {
    	return $this->belongsTo(StockMaster::class, 'jan_code', 'jan_code');
    }
}
