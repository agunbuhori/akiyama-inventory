<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function  user()
    {
    	return $this->hasOne(User::class, 'store_code', 'code');
    }
}
