<?php
spl_autoload_register(function($clase) {
    $archivo = __DIR__ . "/" . str_replace("\\", "/", $clase) . "estadisticas.php";
    if (is_file($archivo)) {
        require_once $archivo;
    }
});

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

require_once "Utils/Html2pdf/Html2Pdf.php";

try {
    $html2pdf = new Html2Pdf();
    $html2pdf->writeHtml("<h1>Mi primer pdf</h1>");
    $html2pdf->output("Factura.pdf");
} catch (Html2PdfException $ex) {
    echo $ex;
    die();
}
?>
