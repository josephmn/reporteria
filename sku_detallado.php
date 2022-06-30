<?php

date_default_timezone_set("America/Lima");

$nombre_archivo = "src/doc/" . "ODVSKU_" . date("Ymd") . ".pdf"; // ODVP (Ordenes De Ventas Pendientes)

include_once("src/lib/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4-L', 7, 'Arial', 15, 15, 25, 20, 10, 10, 'L');

$mpdf->SetHTMLHeader('<table style="width:100%;"><tr><td><h1 style="text-align:center">FILL RATE DETALLADO POR ORDEN DE VENTA</h1></td><td align="right"><img src="src/img/banner.png" width="500" height="50" /></td></tr></table>');

$mpdf->SetHTMLFooter('Pag. {PAGENO} de {nb}');
$mpdf->SetTitle('ORDENES DE VENTAS - VERDUM PERÚ SAC - ' . date("Y"));

$mpdf->useActiveForms = true;

date_default_timezone_set("America/Lima");

$wsdl = 'http://localhost:81/WSREPORT/wsreporteria.asmx?WSDL';

$options = array(
    "uri" => $wsdl,
    "style" => SOAP_RPC,
    "use" => SOAP_ENCODED,
    "soap_version" => SOAP_1_1,
    "connection_timeout" => 300,
    "trace" => false,
    "encoding" => "UTF-8",
    "exceptions" => false,
);
$soap = new SoapClient($wsdl, $options);

$result = $soap->ListarFillRateXsku();
$fill_rate_sku = json_decode($result->ListarFillRateXskuResult, true);

$html = "";
$tabla = "";

/* ********************************************** TABLA POR PERIODO (BEGIN) ********************************************** */
$table_fillrate = "";
$table_fillrate .= "
    <br>
    <table cellspacing='1' cellpadding='5'>
        <thead>
            <tr ALIGN=center>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>ALMACEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>PERIODO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>SKU</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>NOMBRE PRODUCTO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>FAMILIA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>CANTIDAD ORDENADA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>CANTIDAD ATENDIDA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>% PORCENTAJE</th>
            </tr>
        </thead>
    ";
foreach ($fill_rate_sku as $orden) {

    if ($orden['PORCENTAJE'] != 0) {
        if (strval($orden['PORCENTAJE']) <= 75) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . strval($orden['PORCENTAJE']) . " %" . "</b></font></th>";
        } elseif (strval($orden['PORCENTAJE']) >= 76 && strval($orden['PORCENTAJE']) <= 99) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . strval($orden['PORCENTAJE']) . " %" . "</b></font></th>";
        }
    } else {
        $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . strval($orden['PORCEN_PENDIENTE']) . " %" . "</th>";
    }

    $table_fillrate .= "
        <tr>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['ALMACEN'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['PERIODO'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['SKU'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden['NOMBRE_PRODUCTO'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden['FAMILIA'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden['CANTIDAD_ORDEN'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden['CANTIDAD_LLEVA'] . "</th>
            " . $pendiente . "
        </tr>
        ";
}
$table_fillrate .= "</table>";
/* ********************************************** TABLA POR PERIODO (END) ********************************************** */


$mpdf->WriteHTML($table_fillrate);
$mpdf->Output($nombre_archivo . '.pdf', 'I');
// $mpdf->Output($nombre_archivo, 'F');
exit;
?>