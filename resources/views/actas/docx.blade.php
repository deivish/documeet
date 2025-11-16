<?php
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(11);

$section = $phpWord->addSection([
    'marginTop' => Converter::cmToTwip(2),
    'marginBottom' => Converter::cmToTwip(2),
    'marginLeft' => Converter::cmToTwip(2.5),
    'marginRight' => Converter::cmToTwip(2.5),
]);

// ========== TÍTULO ==========
$section->addText(
    '📑 ACTA DE REUNIÓN',
    ['bold' => true, 'size' => 18, 'color' => '667eea'],
    ['alignment' => 'center', 'spaceAfter' => 240]
);

// ========== INFORMACIÓN BÁSICA ==========
$section->addText('Información de la Reunión', ['bold' => true, 'size' => 14, 'color' => '667eea']);
$section->addTextBreak(1);

$table = $section->addTable([
    'borderSize' => 6,
    'borderColor' => 'e1e8ed',
    'cellMargin' => 80,
]);

$table->addRow();
$table->addCell(3000)->addText('Título:', ['bold' => true]);
$table->addCell(6000)->addText($acta->reunion->titulo);

$table->addRow();
$table->addCell(3000)->addText('Fecha:', ['bold' => true]);
$table->addCell(6000)->addText(\Carbon\Carbon::parse($acta->reunion->fecha_hora)->format('d/m/Y H:i'));

if ($acta->reunion->descripcion) {
    $table->addRow();
    $table->addCell(3000)->addText('Descripción:', ['bold' => true]);
    $table->addCell(6000)->addText($acta->reunion->descripcion);
}

$section->addTextBreak(2);

// ========== RESUMEN EJECUTIVO ==========
if ($acta->resumen) {
    $section->addText(
        '🤖 Resumen Ejecutivo',
        ['bold' => true, 'size' => 14, 'color' => '667eea']
    );
    $section->addText(
        '✨ Generado con Claude AI',
        ['italic' => true, 'size' => 9, 'color' => '764ba2']
    );
    $section->addTextBreak(1);

    $parrafos = explode("\n", $acta->resumen);
    foreach ($parrafos as $parrafo) {
        if (trim($parrafo)) {
            $section->addText(
                trim($parrafo),
                ['size' => 11],
                ['alignment' => 'both', 'spaceAfter' => 120]
            );
        }
    }

    $section->addTextBreak(2);
}

// ========== DESARROLLO ==========
if ($acta->contenido || $acta->desarrollo) {
    $section->addText(
        '📝 Desarrollo de la Reunión',
        ['bold' => true, 'size' => 14, 'color' => '667eea']
    );
    $section->addTextBreak(1);
    
    $desarrollo = $acta->contenido ?? $acta->desarrollo ?? 'Sin información registrada';
    $section->addText($desarrollo, ['size' => 11], ['alignment' => 'both']);
    
    $section->addTextBreak(2);
}

// ========== ACTIVIDADES ==========
$section->addText(
    '📌 Actividades Asignadas',
    ['bold' => true, 'size' => 14, 'color' => '667eea']
);
$section->addTextBreak(1);

$actividades = $acta->reunion->actividades->whereNull('deleted_at');

if ($actividades->count() > 0) {
    foreach ($actividades as $index => $actividad) {
        $section->addText(
            ($index + 1) . '. ' . $actividad->nombre,
            ['bold' => true, 'size' => 12]
        );
        
        if ($actividad->descripcion) {
            $section->addText(
                '   Descripción: ' . $actividad->descripcion,
                ['size' => 10, 'color' => '5a6c7d']
            );
        }
        
        $section->addText(
            '   Responsable: ' . ($actividad->responsable ?? 'Sin asignar'),
            ['size' => 10, 'color' => '5a6c7d']
        );
        
        $section->addText(
            '   Fecha límite: ' . \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y'),
            ['size' => 10, 'color' => '5a6c7d']
        );
        
        $section->addTextBreak(1);
    }
} else {
    $section->addText(
        'No hay actividades registradas',
        ['italic' => true, 'color' => '95a5a6'],
        ['alignment' => 'center']
    );
}

$section->addTextBreak(2);

// ========== COMPROMISOS ==========
$section->addText(
    '✅ Compromisos Establecidos',
    ['bold' => true, 'size' => 14, 'color' => '667eea']
);
$section->addTextBreak(1);

$compromisos = $acta->reunion->compromisos;

if ($compromisos->count() > 0) {
    foreach ($compromisos as $index => $compromiso) {
        $section->addText(
            ($index + 1) . '. ' . $compromiso->descripcion,
            ['bold' => true, 'size' => 12]
        );
        
        $section->addText(
            '   Responsable: ' . $compromiso->responsable,
            ['size' => 10, 'color' => '5a6c7d']
        );
        
        $section->addText(
            '   Fecha compromiso: ' . \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y'),
            ['size' => 10, 'color' => '5a6c7d']
        );
        
        $section->addText(
            '   Resultado esperado: ' . ($compromiso->resultado ?? 'N/A'),
            ['size' => 10, 'color' => '5a6c7d']
        );
        
        $section->addTextBreak(1);
    }
} else {
    $section->addText(
        'No hay compromisos registrados',
        ['italic' => true, 'color' => '95a5a6'],
        ['alignment' => 'center']
    );
}

$section->addTextBreak(3);

// ========== FOOTER ==========
$section->addText(
    '_______________________________________________',
    ['color' => 'e1e8ed'],
    ['alignment' => 'center']
);
$section->addTextBreak(1);

$section->addText(
    'Documento generado el ' . now()->format('d/m/Y') . ' a las ' . now()->format('H:i'),
    ['size' => 9, 'color' => '95a5a6'],
    ['alignment' => 'center']
);

$section->addText(
    'Este documento es una representación oficial del acta de reunión',
    ['size' => 9, 'color' => '95a5a6'],
    ['alignment' => 'center']
);

if ($acta->resumen) {
    $section->addTextBreak(1);
    $section->addText(
        '✨ Resumen ejecutivo generado con Claude AI (Anthropic)',
        ['size' => 9, 'italic' => true, 'color' => '667eea'],
        ['alignment' => 'center']
    );
}

// Guardar y descargar
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');

// Guardar en memoria temporal
$tempFile = tempnam(sys_get_temp_dir(), 'acta_');
$objWriter->save($tempFile);

// Enviar archivo
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="acta-' . $acta->id . '.docx"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($tempFile));

readfile($tempFile);
unlink($tempFile);
exit;
?>