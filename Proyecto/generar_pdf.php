<?php
require_once "Utils/Html2pdf/Html2Pdf.php";
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

try {
    ob_start();
    $_GET['pdf'] = '1'; // Muy importante para que estadisticas.php use <page>
    include("estadisticas.php");
    $html = ob_get_clean();

    $html2pdf = new Html2Pdf();
    $html2pdf->writeHTML($html);
    $html2pdf->output("Estadisticas.pdf");
} catch (Html2PdfException $ex) {
    echo $ex;
    die();
}
