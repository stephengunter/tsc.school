<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    public function centers()
    {
        return $this->belongsToMany(Discount::class,'discount_identity','identity_id','discount_id');
    }


    public function users()
    {
        return $this->belongsToMany(User::class,'identity_user','identity_id','user_id');
	}
}
