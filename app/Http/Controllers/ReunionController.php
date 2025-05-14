<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\InvitacionReunion;


class ReunionController extends Controller
{
    //
    public function index()
    {
        $user = User::with(['reunionesCreadas', 'reunionesInvitado'])->find(Auth::id());

        return view('reuniones.index', [
            'reunionesOrganizadas' => $user->reunionesCreadas,
            'reunionesInvitado' => $user->reunionesInvitado,
        ]);
    }

    public function create()
    {
        return view('reuniones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'required|date',
        ]);

        $reunion = Reunion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_hora' => $request->fecha_hora,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('reuniones.index')->with('success', 'Reunión creada con éxito.');
    }

    public function show(Reunion $reunion)
    {
        return view('reuniones.show', compact('reunion'));
    }

    public function invitados(Reunion $reunion)
    {
        $invitados = $reunion->invitados;
        $usuarios = User::where('id', '!=', $reunion->user_id)->get();

        return view('reuniones.invitados', compact('reunion', 'invitados', 'usuarios'));
    }

    // Agregar invitados a las reuniones programadas
    public function agregarInvitado(Request $request, Reunion $reunion)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors(['email' => 'Usuario no encontrado']);
        }
    
        // Evitar duplicados
        $reunion->invitados()->syncWithoutDetaching([$usuario->id]);
    
        // Enviar notificación
        $usuario->notify(new InvitacionReunion($reunion));

        return back()->with('success', 'Invitado agregado correctamente y notificado por correo.');
    }


}
