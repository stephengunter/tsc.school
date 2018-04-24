<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankPost extends Model
{
    protected $table = 'bankPosts';
    protected $fillable = [ 'date', 'from', 'serial' , 
                            'code', 'amount','payAt' , 'text'
                          ];
                        
                             
	
}
