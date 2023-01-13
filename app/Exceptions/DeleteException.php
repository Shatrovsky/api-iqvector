<?php


namespace App\Exceptions;

use Exception;

class DeleteException extends Exception
{
    public $errors;

    public function __construct(string $message = null, $errors = null)
    {
        $this->errors = $errors;
        $this->message = $message;
    }
}
