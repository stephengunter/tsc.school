<?php

namespace App\Exceptions;

use Exception;

class EmailUnconfirmed extends Exception
{
    public function __construct($email)
    {
        $this->email = $email;
    }
    
}
