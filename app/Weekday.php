<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Weekday extends Model
{
    public function toOption()
    {
        return [ 'text' => $this->title ,  'value' => $this->id  ];
    }

    public static function getChineseText(int $dayOfWeek, bool $withCurve=true)
    {
        $text = '';

        switch ($dayOfWeek)
        {
            case 0:
                $text= '日';
            break;
            case 1:
                $text= '一';
            break;
            case 2:
                $text= '二';
            break;
            case 3:
                $text= '三';
            break;

            case 4:
                $text= '四';
            break;
            case 5:
             $text= '五';
            break;
            case 6:
                $text= '六';
            break;
               
        }

        if($withCurve) return '(' . $text .')';

        return $text;
    }
}
