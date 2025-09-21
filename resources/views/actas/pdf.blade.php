<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acta de Reunión</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        h1 { color: #2c3e50; margin-bottom: 10px; }
        h3 { margin-top: 20px; }
        p, li { margin-bottom: 8px; }
        ul { padding-left: 20px; }
        .section { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>📑 Acta de la Reunión</h1>

    <p><strong>Título:</strong> {{ $acta->reunion->titulo }}</p>
    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($acta->reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
    <p><strong>Descripción:</strong> {{ $acta->reunion->descripcion ?? 'Sin descripción' }}</p>

    <div class="section">
        <h3>📝 Desarrollo</h3>
        <p>{{ $acta->contenido ?? $acta->desarrollo ?? 'Sin información registrada' }}</p>
    </div>

    <div class="section">
        <h3>📌 Actividades</h3>
        <ul>
            @forelse($acta->reunion->actividades->whereNull('deleted_at') as $actividad)
                <li>
                    <strong>{{ $actividad->nombre }}</strong><br>
                    @if(!empty($actividad->descripcion))
                        <span>{{ $actividad->descripcion }}</span><br>
                    @endif
                    <strong>Responsable:</strong> {{ $actividad->responsable ?? 'Sin asignar' }}<br>
                    <strong>Fecha límite:</strong> {{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}
                </li>
            @empty
                <li>No hay actividades registradas</li>
            @endforelse
        </ul>
    </div>

    <div class="section">
        <h3>✅ Compromisos</h3>
        <ul>
            @forelse($acta->reunion->compromisos as $compromiso)
                <li>
                    <strong>{{ $compromiso->descripcion }}</strong><br>
                    <strong>Responsable:</strong> {{ $compromiso->responsable }}<br>
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y') }}<br>
                    <strong>Resultado esperado:</strong> {{ $compromiso->resultado ?? 'N/A' }}
                </li>
            @empty
                <li>No hay compromisos registrados</li>
            @endforelse
        </ul>
    </div>

    <p><em>Generada el {{ now()->format('d/m/Y H:i') }}</em></p>
</body>
</html>
