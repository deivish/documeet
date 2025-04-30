<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    //
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    //retorna el panel o dashboard del usuario
    public function index()
    {
        return view('dashboard');
    }
}
