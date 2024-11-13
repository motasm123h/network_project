<?php

namespace App\Classes\Auth;

use Illuminate\Support\Facades\Request;

abstract class Register
{
    public abstract function register(Request $request);
    public abstract function FaceBockregister(Request $request);
    public abstract function Googleregister(Request $request);
    public abstract function LinkedInregister(Request $request);
}
