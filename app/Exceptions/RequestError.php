<?php

namespace App\Exceptions;

use Exception;

class RequestError extends Exception
{
   public function __construct( $key , $value)
   {
        $this->err = [
           'key'=> $key,
           'value' => $value
        ];
   }

   public function getError()
   {
      return $this->err;
   }
   public function getErrorKey()
   {
      return $this->err['key'];
   }
   public function getErrorMsg()
   {
      return $this->err['value'];
   }
    
}
