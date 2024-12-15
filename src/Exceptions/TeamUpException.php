<?php

Namespace Asantos88\TeamUpLaravel\Exceptions;

use Exception;

class TeamUpException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
