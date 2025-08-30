@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-4">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Historial de reuniones</h1>
        <a href="{{ route('post.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            ← Volver a Tucuenta
        </a>
    </div>

    @if($reunionesOrganizadas->isEmpty() && $reunionesInvitado->isEmpty())
        <div class="text-gray-600 text-center mt-20">
            No tienes reuniones pasadas en el historial.
        </div>
    @else
        {{-- Sección: Reuniones organizadas por mí --}}
        @if(!$reunionesOrganizadas->isEmpty())
            <h2 class="text-2xl font-semibold text-indigo-700 mb-4 mt-8">Reuniones organizadas por ti</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($reunionesOrganizadas as $reunion)
                    <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $reunion->titulo }}</h3>
                        <p class="text-sm text-gray-500 mb-1">Fecha: {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-700 mb-2">{{ Str::limit($reunion->descripcion, 100) }}</p>
                        <p class="text-sm text-gray-400 italic">Organizada por ti</p>
                        <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}" 
                           class="inline-block mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                            Ver detalles →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Sección: Reuniones donde fui invitado --}}
        @if(!$reunionesInvitado->isEmpty())
            <h2 class="text-2xl font-semibold text-indigo-700 mb-4 mt-12">Reuniones donde fuiste invitado</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($reunionesInvitado as $reunion)
                    <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $reunion->titulo }}</h3>
                        <p class="text-sm text-gray-500 mb-1">Fecha: {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-700 mb-2">{{ Str::limit($reunion->descripcion, 100) }}</p>
                        <p class="text-sm text-gray-400 italic">Organizada por: {{ $reunion->organizador->name }}</p>
                        <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}" 
                           class="inline-block mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                            Ver detalles →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
