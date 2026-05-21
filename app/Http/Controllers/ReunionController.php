<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\InvitacionReunion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\AuditoriaHelper;

class ReunionController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = User::with(['reunionesCreadas', 'reunionesInvitado'])->find(Auth::id());

        $reunionesInvitado = $user->reunionesInvitado->filter(function ($reunion) use ($user) {
            return $reunion->user_id !== $user->id;
        });

        return view('reuniones.index', [
            'reunionesOrganizadas' => $user->reunionesCreadas,
            'reunionesInvitado'    => $reunionesInvitado,
        ]);
    }

    public function create()
    {
        $reunionesAnteriores = Reunion::where('user_id', Auth::id())
            ->orderBy('fecha_hora', 'desc')
            ->get(['id', 'titulo', 'fecha_hora']);

        return view('reuniones.create', compact('reunionesAnteriores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'                       => 'required|string|max:255',
            'descripcion'                  => 'nullable|string',
            'fecha_hora'                   => 'required|date',
            'actividades'                  => 'nullable|array',
            'actividades.*.nombre'         => 'required_with:actividades|string|max:255',
            'actividades.*.descripcion'    => 'required_with:actividades|string',
            'actividades.*.responsable'    => 'required_with:actividades|string|max:255',
            'actividades.*.fecha_entrega'  => 'required_with:actividades|date',
            'reunion_padre_id'             => 'nullable|exists:reunions,id',
        ]);

        $reunion = Reunion::create([
            'titulo'           => $request->titulo,
            'descripcion'      => $request->descripcion,
            'fecha_hora'       => $request->fecha_hora,
            'user_id'          => Auth::id(),
            'reunion_padre_id' => $request->reunion_padre_id ?: null,
        ]);

        AuditoriaHelper::registrar(
            'Creó la reunión: ' . $reunion->titulo,
            'Reunion',
            $reunion->id,
            $reunion->id,
            null,
            ['titulo' => $reunion->titulo, 'fecha_hora' => $reunion->fecha_hora]
        );

        $reunion->invitados()->attach(Auth::id(), ['rol' => 'moderador']);

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
            Auth::user()->notifications()
                ->where('data->reunion_id', $id)
                ->delete();

            return redirect()->route('reuniones.index')
                ->with('error', 'La reunión ya no está disponible o fue eliminada.');
        }

        Auth::user()->unreadNotifications
            ->where('data.reunion_id', $reunion->id)
            ->each->markAsRead();

        return view('reuniones.show', compact('reunion'));
    }

    public function edit(Reunion $reunion)
    {
        if ($reunion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta reunión.');
        }

        $reunion->load('actividades');

        $reunionesAnteriores = Reunion::where('user_id', Auth::id())
            ->where('id', '!=', $reunion->id)
            ->whereNull('reunion_padre_id')
            ->orWhere(function ($q) use ($reunion) {
                $q->where('user_id', Auth::id())
                    ->where('id', '!=', $reunion->id)
                    ->where('reunion_padre_id', '!=', $reunion->id);
            })
            ->orderBy('fecha_hora', 'desc')
            ->get(['id', 'titulo', 'fecha_hora']);

        return view('reuniones.create', compact('reunion', 'reunionesAnteriores'));
    }

    public function update(Request $request, Reunion $reunion)
    {
        if ($reunion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta reunión.');
        }

        $request->validate([
            'titulo'                       => 'required|string|max:255',
            'descripcion'                  => 'nullable|string',
            'fecha_hora'                   => 'required|date',
            'actividades'                  => 'nullable|array',
            'actividades.*.nombre'         => 'required_with:actividades|string|max:255',
            'actividades.*.descripcion'    => 'required_with:actividades|string',
            'actividades.*.responsable'    => 'required_with:actividades|string|max:255',
            'actividades.*.fecha_entrega'  => 'required_with:actividades|date',
            'reunion_padre_id'             => 'nullable|exists:reunions,id',
        ]);

        // Guardar valores ANTES de actualizar
        $fechaHoraAnterior = $reunion->fecha_hora;
        $fechaHoraCambio   = $request->fecha_hora != $fechaHoraAnterior->format('Y-m-d H:i:s');

        $originales = [
            'titulo'      => $reunion->titulo,
            'descripcion' => $reunion->descripcion,
            'fecha_hora'  => \Carbon\Carbon::parse($reunion->fecha_hora)->format('Y-m-d H:i:s'),
        ];

        $reunion->update([
            'titulo'           => $request->titulo,
            'descripcion'      => $request->descripcion,
            'fecha_hora'       => $request->fecha_hora,
            'reunion_padre_id' => $request->reunion_padre_id ?: null,
        ]);

        // Solo registrar auditoría si algo cambió realmente
        $nuevos = [
            'titulo'      => $reunion->titulo,
            'descripcion' => $reunion->descripcion,
            'fecha_hora'  => \Carbon\Carbon::parse($reunion->fecha_hora)->format('Y-m-d H:i:s'),
        ];

        $camposModificados = array_filter(
            $nuevos,
            fn($v, $k) => $v !== $originales[$k],
            ARRAY_FILTER_USE_BOTH
        );

        if (!empty($camposModificados)) {
            $anterioresFiltrados = array_intersect_key($originales, $camposModificados);
            AuditoriaHelper::registrar(
                'Editó la reunión',
                'Reunion',
                $reunion->id,
                $reunion->id,
                $anterioresFiltrados,
                $camposModificados
            );
        }

        // Notificar cambio de fecha/hora — con try/catch para no romper el flujo
        if ($fechaHoraCambio) {
            foreach ($reunion->invitados as $invitado) {
                if ($invitado->id !== $reunion->user_id) {
                    try {
                        $invitado->notify(new \App\Notifications\CambioHoraReunion($reunion, $fechaHoraAnterior));
                    } catch (\Exception $e) {
                        \Log::warning('Email no enviado a ' . $invitado->email . ': ' . $e->getMessage());
                    }
                }
            }
        }

        // Procesar actividades nuevas y existentes
        if ($request->has('actividades')) {
            foreach ($request->actividades as $actividadData) {
                if (isset($actividadData['id'])) {
                    $actividad = $reunion->actividades()->find($actividadData['id']);
                    if ($actividad) {
                        $actividad->update($actividadData);
                    }
                } else {
                    $actividad = $reunion->actividades()->create($actividadData);  // ← capturar
                    AuditoriaHelper::registrar(
                        'Agregó actividad: ' . $actividadData['nombre'],
                        'Actividad',
                        $actividad->id,    // ← ahora sí existe
                        $reunion->id,
                        null,
                        ['nombre' => $actividadData['nombre'], 'responsable' => $actividadData['responsable'] ?? 'Sin asignar']
                    );
                }
            }
        }

        // Procesar actividades eliminadas
        if ($request->has('actividades_eliminar')) {
            foreach ($request->actividades_eliminar as $id) {
                $actividad = $reunion->actividades()->find($id);
                if ($actividad) {
                    AuditoriaHelper::registrar(
                        'Eliminó actividad: ' . $actividad->nombre,
                        'Actividad',
                        $actividad->id,
                        $reunion->id,
                        ['nombre' => $actividad->nombre, 'responsable' => $actividad->responsable],
                        null
                    );
                    $actividad->delete();
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

        AuditoriaHelper::registrar(
            'Eliminó la reunión: ' . $reunion->titulo,
            'Reunion',
            $reunion->id,
            $reunion->id,
            ['titulo' => $reunion->titulo],
            null
        );

        $reunion->delete();

        return redirect()->route('reuniones.index')->with('success', 'Reunión eliminada.');
    }

    public function invitados(Reunion $reunion)
    {
        $invitados = $reunion->invitados;
        $usuarios  = User::where('id', '!=', $reunion->user_id)->get();

        return view('reuniones.invitados', compact('reunion', 'invitados', 'usuarios'));
    }

    public function agregarInvitado(Request $request, Reunion $reunion)
    {
        if ($reunion->user_id !== Auth::id()) {
            abort(403, 'Solo el moderador puede agregar invitados.');
        }

        $request->validate(['email' => 'required|email']);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors(['email' => 'Usuario no encontrado']);
        }

        $yaInvitado = $reunion->invitados()->where('user_id', $usuario->id)->exists();
        if ($yaInvitado) {
            return back()->withErrors(['email' => 'Este usuario ya ha sido invitado a la reunión.']);
        }

        $reunion->invitados()->attach($usuario->id, ['rol' => 'invitado']);
        $reunion->invitados()->syncWithoutDetaching([$usuario->id]);

        try {
            $usuario->notify(new InvitacionReunion($reunion));
            // broadcast(new InvitacionReunion($reunion, $usuario->id))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Notificación no enviada: ' . $e->getMessage());
        }

        return back()->with('success', 'Invitado agregado correctamente.');
    }

    public function historial()
{
    $user = Auth::user();

    $reunionesOrganizadas = Reunion::withTrashed()
        ->where('user_id', $user->id)
        ->where('fecha_hora', '<', now())
        ->with([
            'actividades' => fn($q) => $q->whereNull('deleted_at'),
            'compromisos',
            'invitados',
        ])
        ->orderBy('fecha_hora', 'desc')
        ->get();

    $reunionesInvitado = Reunion::withTrashed()
        ->whereHas('invitados', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('user_id', '!=', $user->id)
        ->where('fecha_hora', '<', now())
        ->with([
            'actividades' => fn($q) => $q->whereNull('deleted_at'),
            'compromisos',
            'invitados',
        ])
        ->orderBy('fecha_hora', 'desc')
        ->get();

    return view('reuniones.history', compact('reunionesOrganizadas', 'reunionesInvitado'));
}

    public function detalleHistorial($id)
{
    $reunion = Reunion::withTrashed()->findOrFail($id);
    $user    = Auth::user();

    $esOrganizador = $reunion->user_id === $user->id;
    $esInvitado    = $reunion->invitados()->where('user_id', $user->id)->exists();

    if (!$esOrganizador && !$esInvitado) {
        abort(403, 'No tienes permiso para ver esta reunión.');
    }

    $invitados = $reunion->invitados()->get();

    $reunion->load([
        'actividades' => fn($q) => $q->whereNull('deleted_at'),
        'compromisos',
        'acta',
        'user',
    ]);

    return view('reuniones.detail_history', compact('reunion', 'invitados'));
}

    public function storeActividad(Request $request, Reunion $reunion)
    {
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'responsable'   => 'required|string|max:255',
            'fecha_entrega' => 'required|date|after_or_equal:today',
        ]);

        $actividad = $reunion->actividades()->create($validated);

        AuditoriaHelper::registrar(
            'Agregó actividad: ' . $validated['nombre'],
            'Actividad',
            $actividad->id,
            $reunion->id,
            null,
            ['nombre' => $validated['nombre'], 'responsable' => $validated['responsable']]
        );

        return redirect()->route('reuniones.show', $reunion)->with('success', 'Actividad registrada correctamente.');
    }
}