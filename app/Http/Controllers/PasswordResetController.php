<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // Mostrar formulario — ingresa correo
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    // Generar código y mostrarlo
    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Ingresa un correo válido.',
            'email.exists'   => 'No encontramos una cuenta con ese correo.',
        ]);

        // Eliminar códigos anteriores del mismo correo
        DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->delete();

        // Generar código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar en BD con expiración de 15 minutos
        DB::table('password_reset_codes')->insert([
            'email'      => $request->email,
            'code'       => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Mostrar código en pantalla (desarrollo)
        // En producción: enviar por correo y quitar el ->with('code', $code)
        return back()->with([
            'success' => 'Código generado correctamente.',
            'code'    => $code,
            'email'   => $request->email,
        ]);
    }

    // Mostrar formulario — ingresa código + nueva contraseña
    public function showReset(Request $request)
    {
        return view('auth.reset-password', [
            'email' => $request->query('email', ''),
        ]);
    }

    // Validar código y cambiar contraseña
    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'code'     => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'code.required'     => 'El código es obligatorio.',
            'code.digits'       => 'El código debe tener 6 dígitos.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'Las contraseñas no coinciden.',
        ]);

        // Buscar código válido y no expirado
        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return back()->withErrors([
                'code' => 'El código es incorrecto o ya expiró.',
            ])->withInput();
        }

        // Actualizar contraseña
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Eliminar código usado
        DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('success', '¡Contraseña actualizada! Ya puedes iniciar sesión.');
    }
}