<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acta de Reunión — {{ $acta->reunion->titulo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #2c3e50;
            padding: 40px;
        }

        /* ── ENCABEZADO ── */
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 28px 32px;
            margin: -40px -40px 28px -40px;
        }
        .header h1 { font-size: 22pt; font-weight: bold; margin-bottom: 4px; }
        .header p  { font-size: 10pt; opacity: 0.85; }

        /* ── INFO GENERAL ── */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        .info-row  { display: table-row; }
        .info-label {
            display: table-cell;
            background: #f8fafc;
            color: #4f46e5;
            font-weight: bold;
            font-size: 10pt;
            padding: 8px 14px;
            width: 130px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-value {
            display: table-cell;
            padding: 8px 14px;
            font-size: 10pt;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ── SECCIONES ── */
        .section { margin: 24px 0; }
        .section-title {
            color: #4f46e5;
            font-size: 13pt;
            font-weight: bold;
            padding-bottom: 6px;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 14px;
        }

        /* ── ASISTENTES ── */
        .asistentes-list { padding-left: 0; list-style: none; }
        .asistentes-list li {
            display: inline-block;
            background: #ede9fe;
            color: #5b21b6;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9pt;
            margin: 3px;
        }

        /* ── RESUMEN ── */
        .resumen-box {
            background: #f0f4ff;
            border-left: 4px solid #4f46e5;
            padding: 16px 20px;
            border-radius: 0 6px 6px 0;
            font-size: 10.5pt;
            line-height: 1.8;
            text-align: justify;
        }
        .ai-badge {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: bold;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* ── DESARROLLO ── */
        .desarrollo-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            font-size: 10.5pt;
            line-height: 1.8;
            text-align: justify;
        }

        /* ── TABLAS ── */
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10pt; }
        th {
            background: #f1f5f9;
            color: #4f46e5;
            font-weight: bold;
            text-align: left;
            padding: 9px 12px;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
            text-transform: uppercase;
        }
        td {
            padding: 9px 12px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        tr:nth-child(even) td { background: #f8fafc; }

        /* ── BADGES ESTADO ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8.5pt;
            font-weight: bold;
        }
        .badge-pendiente  { background: #fef3c7; color: #92400e; }
        .badge-en_curso   { background: #dbeafe; color: #1e40af; }
        .badge-completada { background: #d1fae5; color: #065f46; }
        .badge-cumplido   { background: #d1fae5; color: #065f46; }
        .badge-vencido    { background: #fee2e2; color: #991b1b; }

        /* ── EMPTY STATE ── */
        .empty {
            text-align: center;
            padding: 18px;
            color: #94a3b8;
            font-style: italic;
            font-size: 10pt;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>ACTA DE REUNIÓN</h1>
        <p>{{ strtoupper($acta->reunion->titulo) }}</p>
    </div>

    {{-- INFORMACIÓN GENERAL --}}
    <div class="section">
        <div class="section-title">Información general</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Título</div>
                <div class="info-value">{{ $acta->reunion->titulo }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha y hora</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($acta->reunion->fecha_hora)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Organizador</div>
                <div class="info-value">{{ $acta->reunion->user->name ?? 'Sin especificar' }}</div>
            </div>
            @if($acta->reunion->descripcion)
            <div class="info-row">
                <div class="info-label">Descripción</div>
                <div class="info-value">{{ $acta->reunion->descripcion }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Estado del acta</div>
                <div class="info-value">{{ $acta->estado === 'final' ? '✓ Finalizada' : '⏳ Borrador' }}</div>
            </div>
        </div>
    </div>

    {{-- ASISTENTES --}}
    @php
        $asistentes = $acta->reunion->invitados->pluck('name')->prepend($acta->reunion->user->name . ' (Moderador)');
    @endphp
    <div class="section">
        <div class="section-title">Asistentes ({{ $asistentes->count() }})</div>
        <ul class="asistentes-list">
            @foreach($asistentes as $asistente)
                <li>{{ $asistente }}</li>
            @endforeach
        </ul>
    </div>

    {{-- RESUMEN EJECUTIVO --}}
    @if($acta->resumen)
    <div class="section">
        <div class="section-title">
            Resumen ejecutivo
            <span class="ai-badge">✨ Claude AI</span>
        </div>
        <div class="resumen-box">
            {!! nl2br(e($acta->resumen)) !!}
        </div>
    </div>
    @endif

    {{-- DESARROLLO --}}
    @if($acta->contenido || $acta->desarrollo)
    <div class="section">
        <div class="section-title">Desarrollo de la reunión</div>
        <div class="desarrollo-box">
            {{ $acta->contenido ?? $acta->desarrollo ?? 'Sin información registrada.' }}
        </div>
    </div>
    @endif

    {{-- ACTIVIDADES --}}
    <div class="section">
        <div class="section-title">Actividades asignadas</div>
        @php $actividades = $acta->reunion->actividades->whereNull('deleted_at'); @endphp
        @if($actividades->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Actividad</th>
                        <th>Responsable</th>
                        <th>Fecha límite</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($actividades as $i => $actividad)
                    <tr>
                        <td style="width:30px; text-align:center; color:#94a3b8;">{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $actividad->nombre }}</strong>
                            @if($actividad->descripcion)
                                <br><span style="color:#64748b; font-size:9.5pt;">{{ $actividad->descripcion }}</span>
                            @endif
                        </td>
                        <td>{{ $actividad->responsable ?? 'Sin asignar' }}</td>
                        <td>{{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}</td>
                        <td>
                            @php $est = $actividad->estado ?? 'pendiente'; @endphp
                            <span class="badge badge-{{ $est }}">
                                {{ $est === 'completada' ? '✓ Completada' : ($est === 'en_curso' ? '⟳ En curso' : '○ Pendiente') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No se registraron actividades en esta reunión.</div>
        @endif
    </div>

    {{-- COMPROMISOS --}}
    <div class="section">
        <div class="section-title">Compromisos establecidos</div>
        @if($acta->reunion->compromisos->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Compromiso</th>
                        <th>Responsable</th>
                        <th>Fecha</th>
                        <th>Resultado esperado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($acta->reunion->compromisos as $i => $compromiso)
                    <tr>
                        <td style="width:30px; text-align:center; color:#94a3b8;">{{ $i + 1 }}</td>
                        <td>{{ $compromiso->descripcion }}</td>
                        <td>{{ $compromiso->responsable ?? 'Sin asignar' }}</td>
                        <td>{{ \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $compromiso->resultado ?? 'N/A' }}</td>
                        <td>
                            @php $est = $compromiso->estado ?? 'pendiente'; @endphp
                            <span class="badge badge-{{ $est }}">
                                {{ $est === 'cumplido' ? '✓ Cumplido' : ($est === 'vencido' ? '✗ Vencido' : '○ Pendiente') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No se registraron compromisos en esta reunión.</div>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p><strong>Documento generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</strong></p>
        <p>Este documento es una representación oficial del acta de reunión — DocuMeet</p>
        @if($acta->resumen)
        <p style="margin-top: 8px; color: #6d28d9; font-style: italic;">
            ✨ Resumen ejecutivo generado con Claude AI (Anthropic)
        </p>
        @endif
    </div>

</body>
</html>