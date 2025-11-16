<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acta de Reunión</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #2c3e50;
            padding: 40px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            margin: -40px -40px 30px -40px;
            border-radius: 0 0 10px 10px;
        }
        
        .header h1 {
            font-size: 28pt;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header p {
            font-size: 10pt;
            opacity: 0.9;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .info-row {
            margin: 8px 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #667eea;
            display: inline-block;
            width: 120px;
        }
        
        .section {
            margin: 30px 0;
        }
        
        .section-title {
            color: #667eea;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }
        
        .section-icon {
            display: inline-block;
            width: 25px;
            text-align: center;
        }
        
        .resumen-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 11pt;
            line-height: 1.8;
            text-align: justify;
        }
        
        .ai-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            margin-left: 10px;
        }
        
        ul {
            list-style: none;
            padding: 0;
        }
        
        li {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 5px;
            padding: 12px 15px;
            margin: 10px 0;
        }
        
        li strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 12pt;
        }
        
        li span {
            color: #5a6c7d;
            font-size: 10pt;
            display: block;
            margin: 3px 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #95a5a6;
            font-style: italic;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e1e8ed;
            text-align: center;
            font-size: 9pt;
            color: #95a5a6;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        th {
            background: #f8f9fa;
            color: #667eea;
            font-weight: bold;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📑 Acta de Reunión</h1>
        <p>Documento oficial generado automáticamente</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Título:</span>
            <span>{{ $acta->reunion->titulo }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span>{{ \Carbon\Carbon::parse($acta->reunion->fecha_hora)->format('d/m/Y H:i') }}</span>
        </div>
        @if($acta->reunion->descripcion)
        <div class="info-row">
            <span class="info-label">Descripción:</span>
            <span>{{ $acta->reunion->descripcion }}</span>
        </div>
        @endif
    </div>

    {{-- ✅ SECCIÓN RESUMEN EJECUTIVO EN PDF --}}
    @if($acta->resumen)
    <div class="section">
        <h2 class="section-title">
            <span class="section-icon">🤖</span> Resumen Ejecutivo
            <span class="ai-badge">Generado con IA</span>
        </h2>
        <div class="resumen-box">
            {!! nl2br(e($acta->resumen)) !!}
        </div>
    </div>
    @endif

    {{-- Desarrollo de la reunión (si existe) --}}
    @if($acta->contenido || $acta->desarrollo)
    <div class="section">
        <h2 class="section-title"><span class="section-icon">📝</span> Desarrollo de la Reunión</h2>
        <div style="padding: 15px; background: white; border: 1px solid #e1e8ed; border-radius: 5px;">
            {{ $acta->contenido ?? $acta->desarrollo ?? 'Sin información registrada' }}
        </div>
    </div>
    @endif

    <div class="section">
        <h2 class="section-title"><span class="section-icon">📌</span> Actividades Asignadas</h2>
        @if($acta->reunion->actividades->whereNull('deleted_at')->count() > 0)
            <ul>
                @foreach($acta->reunion->actividades->whereNull('deleted_at') as $actividad)
                    <li>
                        <strong>{{ $actividad->nombre }}</strong>
                        @if(!empty($actividad->descripcion))
                            <span><strong>Descripción:</strong> {{ $actividad->descripcion }}</span>
                        @endif
                        <span><strong>Responsable:</strong> {{ $actividad->responsable ?? 'Sin asignar' }}</span>
                        <span><strong>Fecha límite:</strong> {{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="empty-state">No hay actividades registradas</div>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title"><span class="section-icon">✅</span> Compromisos Establecidos</h2>
        @if($acta->reunion->compromisos->count() > 0)
            <ul>
                @foreach($acta->reunion->compromisos as $compromiso)
                    <li>
                        <strong>{{ $compromiso->descripcion }}</strong>
                        <span><strong>Responsable:</strong> {{ $compromiso->responsable }}</span>
                        <span><strong>Fecha compromiso:</strong> {{ \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y') }}</span>
                        <span><strong>Resultado esperado:</strong> {{ $compromiso->resultado ?? 'N/A' }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="empty-state">No hay compromisos registrados</div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Documento generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</strong></p>
        <p>Este documento es una representación oficial del acta de reunión</p>
        @if($acta->resumen)
        <p style="margin-top: 10px; font-style: italic; color: #667eea;">
            ✨ Resumen ejecutivo generado con Claude AI (Anthropic)
        </p>
        @endif
    </div>
</body>
</html>