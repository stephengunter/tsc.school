<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Weekday extends Model
{
    public function toOption()
    {
        return [ 'text' => $this->title ,  'value' => $this->id  ];
    }
}
