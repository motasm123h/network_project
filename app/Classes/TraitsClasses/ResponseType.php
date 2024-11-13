<?php

namespace App\Classes\TraitsClasses;

abstract class ResponseType
{
    public abstract function apiResponse($message = null, $data = null, $statuscode = 200);
}
