<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfController extends BaseController
{
    public function generar()
    {
        // 1. Configuración de opciones (opcional pero recomendado)
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Permite cargar imágenes externas
        
        $dompdf = new Dompdf($options);

        // 2. Cargar el HTML desde una vista
        $data = ['titulo' => 'Mi Primer Reporte 2026'];
        $html = view('reporte_view', $data);

        $dompdf->loadHtml($html);

        // 3. (Opcional) Configurar tamaño y orientación del papel
        $dompdf->setPaper('A4', 'portrait');

        // 4. Renderizar el HTML como PDF
        $dompdf->render();

        // 5. Salida al navegador (Stream)
        // 'Attachment' => false para previsualizar, true para descarga directa
        $dompdf->stream("reporte.pdf", ["Attachment" => false]);
    }
}