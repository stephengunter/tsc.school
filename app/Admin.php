<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $primaryKey = 'userId';

    protected $fillable = [ 'active', 'removed', 'updatedBy'];
       
     
     
    public function user()
    {
        return $this->belongsTo('App\User','userId');
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class,'center_admin','admin_id','center_id');
    }
   


}
