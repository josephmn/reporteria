<?php

date_default_timezone_set("America/Lima");

$wsdl = 'http://localhost:81/WSREPORT/wsreporteria.asmx?WSDL';

$options = array(
    "uri" => $wsdl,
    "style" => SOAP_RPC,
    "use" => SOAP_ENCODED,
    "soap_version" => SOAP_1_1,
    "connection_timeout" => 60,
    "trace" => false,
    "encoding" => "UTF-8",
    "exceptions" => false,
);
$soap = new SoapClient($wsdl, $options);

// Ordenes de ventas cabecera
$param = array(
    "post" => 2, // Ordenes no completadas aun (pendientes por despachar)
);

// ordenes de ventas cabecera
$result = $soap->ListarOrdenVentaCab($param);
$ordenes_ventas = json_decode($result->ListarOrdenVentaCabResult, true);

// cantidad de embarques por ordenes
$param1 = array(
    "orden" => $ordenes_ventas[0]['ORDEN_VENTA'],
);
$result1 = $soap->ListarEmbarquesXorden($param1);
$embarque_det = json_decode($result1->ListarEmbarquesXordenResult, true);

/* ******************************************************************************************* */
// logica para la creacion de tabla en reporte
$tabla_cab = "";
$tabla_cab .= "
    <br>
    <table cellspacing='1' cellpadding='5'>
        <thead>
            <tr ALIGN=center>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>ORDEN VENTA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>FECHA ORDEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>PERIODO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>ALMACEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>RUC</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>RAZON</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>TOTAL ORDEN (S/)</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>% PENDIENTE</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>% ATENCION</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'># EMBARQUES</th>
            </tr>
        </thead>
    ";
foreach ($ordenes_ventas as $orden) {

    if ($orden['PORCEN_PENDIENTE'] != 0) {
        if (strval($orden['PORCEN_PENDIENTE']) <= 75) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . strval($orden['PORCEN_PENDIENTE']) . " %" . "</b></font></th>";
        } elseif (strval($orden['PORCEN_PENDIENTE']) >= 76 && strval($orden['PORCEN_PENDIENTE']) <= 99) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . strval($orden['PORCEN_PENDIENTE']) . " %" . "</b></font></th>";
        }
    } else {
        $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . strval($orden['PORCEN_PENDIENTE']) . " %" . "</th>";
    }

    $tabla_cab .= "
        <tr>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['ORDEN_VENTA'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['FECHA_ORDEN'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['PERIODO'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['ALMACEN'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden['RUC'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden['RAZON'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden['MONTO_ORDEN'], 2) . "</th>
            " . $pendiente . "
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden['PORCEN_ATENCION'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . count($embarque_det) . "</th>
        </tr>
        ";
}
$tabla_cab .= "</table>";

$html = $tabla_cab;

$nombre_archivo = "ODV_" . date("Ymd");

include_once("src/lib/mpdf/mpdf.php");
$css = file_get_contents('css/pdf.css');
$mpdf = new mPDF('P', 'A2', 7, 'Arial');

// $mpdf->SetHTMLHeader('Verdum Perú SAC - ' . date("Y"));
// $mpdf->SetHTMLHeader('<img src="src/img/banner.png" width="500px"/>');
$mpdf->SetHTMLHeader('<table style="width:100%;"><tr><td align="right"><img src="src/img/banner.png" width="700" height="70" /></td></tr></table>');

$mpdf->SetHTMLFooter('Pag. {PAGENO} de {nb}');
$mpdf->SetTitle('ORDENES DE VENTAS - VERDUM PERÚ SAC - ' . date("Y"));

// $mpdf->writeHTML($css, 1);
$mpdf->WriteHTML('<br/><br/><br/><h1 style="text-align:center "><u>FILL RATE - CONTROL DETALLADO</u></h1>');
$mpdf->WriteHTML($html);
$mpdf->Output($nombre_archivo . '.pdf', 'I');
exit;
