<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    //
    public function index() 
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->with('mensaje', 'Credenciales incorrectas');
        }

        return redirect()->route('post.index');
    }
}
