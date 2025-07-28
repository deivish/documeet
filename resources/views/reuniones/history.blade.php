@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Historial de reuniones</h2>

    {{-- Reuniones organizadas por mí --}}
    <h3 class="text-xl font-semibold mt-6 mb-2">Reuniones organizadas por mí</h3>
    @if($reunionesOrganizadas->isEmpty())
        <p class="text-gray-600">No has organizado ninguna reunión pasada.</p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($reunionesOrganizadas as $reunion)
                <li class="py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-lg font-semibold">{{ $reunion->titulo }}</h4>
                            <p class="text-sm text-gray-600">Fecha: {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}" class="text-blue-500 hover:underline">Ver detalles</a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Reuniones donde fui invitado --}}
    <h3 class="text-xl font-semibold mt-8 mb-2">Reuniones donde fui invitado</h3>
    @if($reunionesInvitado->isEmpty())
        <p class="text-gray-600">No has sido invitado a ninguna reunión pasada.</p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($reunionesInvitado as $reunion)
                <li class="py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-lg font-semibold">{{ $reunion->titulo }}</h4>
                            <p class="text-sm text-gray-600">Fecha: {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}" class="text-blue-500 hover:underline">Ver detalles</a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
