<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    //Cerrar Sesión
    public function store()
    {
        Auth::logout();

        return redirect()-> route('login');
    }
}
