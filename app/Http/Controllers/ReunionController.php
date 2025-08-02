<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\InvitacionReunion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;




class ReunionController extends Controller
{
    //
    use AuthorizesRequests;
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
            'actividades' => 'nullable|array',
            'actividades.*.nombre' => 'required_with:actividades|string|max:255',
            'actividades.*.descripcion' => 'required_with:actividades|string',
            'actividades.*.responsable' => 'required_with:actividades|string|max:255',
            'actividades.*.fecha_entrega' => 'required_with:actividades|date',
        ]);

        $reunion = Reunion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_hora' => $request->fecha_hora,
            'user_id' => Auth::id(),
        ]);

        // Asocia al creador como "moderador" en la tabla pivote
        $reunion->invitados()->attach(Auth::id(), ['rol' => 'moderador']);

        // Guardar las actividades, si existen
        if ($request->has('actividades')) {
            foreach ($request->actividades as $actividad) {
                $reunion->actividades()->create($actividad);
            }
        }

        return redirect()->route('reuniones.index')->with('success', 'Reunión creada con éxito.');
    }

    public function show($id)
{
    $reunion = Reunion::find($id);

    if (!$reunion) {
        // Eliminar notificaciones relacionadas si la reunión no existe
        Auth::user()->notifications()
            ->where('data->reunion_id', $id)
            ->delete();

        // Redireccionar con mensaje de error (que se autodestruye gracias a Alpine.js)
        return redirect()->route('reuniones.index')
            ->with('error', 'La reunión ya no está disponible o fue eliminada.');
    }

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

    // Cargar actividades relacionadas
    $reunion->load('actividades');

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
        'actividades' => 'nullable|array',
        'actividades.*.nombre' => 'required_with:actividades|string|max:255',
        'actividades.*.descripcion' => 'required_with:actividades|string',
        'actividades.*.responsable' => 'required_with:actividades|string|max:255',
        'actividades.*.fecha_entrega' => 'required_with:actividades|date',
    ]);

    $reunion->update([
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion,
        'fecha_hora' => $request->fecha_hora,
    ]);

    // Procesar actividades nuevas y existentes
    if ($request->has('actividades')) {
        foreach ($request->actividades as $actividadData) {
            if (isset($actividadData['id'])) {
                $actividad = $reunion->actividades()->find($actividadData['id']);
                if ($actividad) {
                    $actividad->update($actividadData);
                }
            } else {
                $reunion->actividades()->create($actividadData);
            }
        }
    }

    // Procesar actividades eliminadas
    if ($request->has('actividades_eliminar')) {
        foreach ($request->actividades_eliminar as $id) {
            $actividad = $reunion->actividades()->find($id);
            if ($actividad) {
                $actividad->delete(); // soft delete
            }
        }
    }

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
        //Historias de reuniones
        public function historial()
    {
        $user = Auth::user();

        $reunionesOrganizadas = Reunion::withTrashed()
        ->where('user_id', $user->id)
        ->where('fecha_hora', '<', now())
        ->orderBy('fecha_hora', 'desc')
        ->get();

        $reunionesInvitado = Reunion::withTrashed()
            ->whereHas('invitados', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('user_id', '!=', $user->id) // para evitar duplicado si también es el creador
            ->where('fecha_hora', '<', now())
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('reuniones.history', compact('reunionesOrganizadas', 'reunionesInvitado'));
    }

    //Detalles de una reunion
    public function detalleHistorial(Reunion $reunion)
    {
        // $this->authorize('verReunionHistorial', $reunion); // opcional: política de acceso

        $invitados = $reunion->invitados()->get();

        // Cargar actividades que no han sido soft deleted
        $reunion->load(['actividades' => function ($query) {
            $query->whereNull('deleted_at');
        }]);

        return view('reuniones.detail_history', compact('reunion', 'invitados'));
    }

    //Actividades de una reunión
    public function storeActividad(Request $request, Reunion $reunion)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'responsable' => 'required|string|max:255',
        'fecha_entrega' => 'required|date|after_or_equal:today',
    ]);

    $reunion->actividades()->create($validated);

    return redirect()->route('reuniones.show', $reunion)->with('success', 'Actividad registrada correctamente.');
}


}
