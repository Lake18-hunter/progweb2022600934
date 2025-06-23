<?php
require_once "Utils/Html2pdf/Html2Pdf.php";
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

try {
    if (!isset($_GET['archivo'])) {
        throw new Exception("No se especificó el archivo.");
    }

    $archivo = basename($_GET['archivo']); // Evita rutas inseguras
    $ruta = $archivo . ".php";

    if (!file_exists($ruta)) {
        throw new Exception("Archivo no encontrado.");
    }

    ob_start();
    $_GET['pdf'] = '1'; // Indicador para usar <page>
    include($ruta);     // Incluir archivo HTML/PHP
    $html = ob_get_clean();

    $html2pdf = new Html2Pdf('P', 'A4', 'es');
    $html2pdf->writeHTML($html);
    $html2pdf->output($archivo . ".pdf");
} catch (Exception $e) {
    echo "Error al generar PDF: " . $e->getMessage();
}
