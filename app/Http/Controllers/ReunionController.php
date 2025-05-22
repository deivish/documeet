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

        // Filtrar reuniones donde fue invitado, pero no las que él mismo creó
        $reunionesInvitado = $user->reunionesInvitado->filter(function ($reunion) use ($user) {
            return $reunion->user_id !== $user->id;
        });

        return view('reuniones.index', [
            'reunionesOrganizadas' => $user->reunionesCreadas,
            'reunionesInvitado' => $reunionesInvitado,
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

        // Asocia al creador como "moderador" en la tabla pivote
        $reunion->invitados()->attach(Auth::id(), ['rol' => 'moderador']);

        return redirect()->route('reuniones.index')->with('success', 'Reunión creada con éxito.');
    }

    public function show(Reunion $reunion)
    {
        // Marcar como leídas las notificaciones relacionadas con esta reunión
        Auth::user()->unreadNotifications
        ->where('data.reunion_id', $reunion->id)
        ->each->markAsRead();

        return view('reuniones.show', compact('reunion'));
    }

    public function edit(Reunion $reunion)
{
    // Verificar si el usuario actual es el moderador
    if ($reunion->user_id !== Auth::id()) {
        abort(403, 'No tienes permiso para editar esta reunión.');
    }

    return view('reuniones.create', compact('reunion'));
}

public function update(Request $request, Reunion $reunion)
{
    if ($reunion->user_id !== Auth::id()) {
        abort(403, 'No tienes permiso para modificar esta reunión.');
    }

    $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'fecha_hora' => 'required|date',
    ]);

    $reunion->update([
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion,
        'fecha_hora' => $request->fecha_hora,
    ]);

    return redirect()->route('reuniones.index')->with('success', 'Reunión actualizada con éxito.');
}

public function destroy(Reunion $reunion)
{
    if ($reunion->user_id !== Auth::id()) {
        abort(403, 'No tienes permiso para eliminar esta reunión.');
    }

    $reunion->delete();

    return redirect()->route('reuniones.index')->with('success', 'Reunión eliminada.');
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
        // Verificar que el usuario autenticado es el moderador
        if ($reunion->user_id !== Auth::id()) {
            abort(403, 'Solo el moderador puede agregar invitados.');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors(['email' => 'Usuario no encontrado']);
        }

        // Verificar si ya está invitado
        $yaInvitado = $reunion->invitados()->where('user_id', $usuario->id)->exists();
        if ($yaInvitado) {
            return back()->withErrors(['email' => 'Este usuario ya ha sido invitado a la reunión.']);
        }

        // Agregar al usuario como invitado con rol
        $reunion->invitados()->attach($usuario->id, ['rol' => 'invitado']);

    
        // Evitar duplicados
        $reunion->invitados()->syncWithoutDetaching([$usuario->id]);
    
        // Enviar notificación
        $usuario->notify(new InvitacionReunion($reunion));

        broadcast(new InvitacionReunion($reunion, $usuario->id))->toOthers();

        return back()->with('success', 'Invitado agregado correctamente y notificado por correo.');
    }


}
