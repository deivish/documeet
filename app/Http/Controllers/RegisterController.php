<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    //
    public function index() 
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {

        //Validation
        $request->validate([
            'tipo_documento'     => 'required|string|max:50',
            'numero_documento'   => 'required|numeric|digits_between:5,15|unique:users,numero_documento',
            'cargo'              => 'required|string|max:100',
            'organizacion'       => 'required|string|max:150',
            'name'            => 'required|string|max:100',
            'apellidos'          => 'required|string|max:100',
            'email'              => 'required|email|unique:users,email',
            'celular'            => 'required|numeric|digits_between:7,15',
            'password'           => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'tipo_documento'=> $request->tipo_documento,
            'numero_documento'=> $request->numero_documento,
            'cargo'=> $request->cargo,
            'organizacion'=> $request->organizacion,
            'name'=> $request->name,
            'apellidos'=> $request->apellidos,
            'email'=> $request->email,
            'celular'=> $request->celular,
            'password'=> $request->password,

        ]);

        //Autenticar un usuario
        Auth::attempt([
            'email' => $request->email, 
            'password' => $request->password
        ]);

        //Redireccionar
        return redirect()->route('post.index');
    }
}
