<?php

date_default_timezone_set("America/Lima");

$nombre_archivo = "src/doc/" . "FVDSKU_" . date("Ymd") . ".pdf"; // ODVP (Ordenes De Ventas Pendientes)

include_once("src/lib/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A3-L', 7, 'Arial', 15, 15, 25, 20, 10, 10, 'L');

$mpdf->SetHTMLHeader('<table style="width:100%;"><tr><td><h1 style="text-align:center"><u>FUERZA DE VENTA DIRECTA POR SKU</u></h1></td><td align="right"><img src="src/img/banner.png" width="500" height="50" /></td></tr></table>');

$mpdf->SetHTMLFooter('Pag. {PAGENO} de {nb}');
$mpdf->SetTitle('FUERZA DE VENTA DIRECTA - VERDUM PERÚ SAC - ' . date("Y"));

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

// Fill Rate por SKU
$param = array(
    "post"      => 2, // ejecutar 18:00
    "almacen"   => "", // Para listar y agruparlos
);

$result = $soap->ListarFVDXsku($param);
$fvd_sku = json_decode($result->ListarFVDXskuResult, true);

$html = "";
$tabla = "";

/* ********************************************** TABLA POR PERIODO (BEGIN) ********************************************** */
$table_fvd = "";
foreach ($fvd_sku as $orden) {

    if ($orden['GRUPO'] != NULL || $orden['GRUPO'] != "") {
        ($orden['ROW'] == 1) ? $grpalmacen = '<h1>' . $orden['GRUPO'] . "(" . $orden['COMENTARIO'] . ")" . '</h1>' : $grpalmacen = '<pagebreak><h1>' . $orden['GRUPO'] . "(" . $orden['COMENTARIO'] . ")" . '</h1>';
    } else {
        $grpalmacen = '';
    }

    if ($orden['GRUPO'] != NULL || $orden['GRUPO'] != "") {
        $mpdf->h2toc = array($orden['GRUPO'] => 0, $orden['ORDEN_VENTA'] => 1);
        $mpdf->h2bookmarks = array('H1' => 0, 'H2' => 1);
    };

    $table_fvd .= "
    <br>
    " . $grpalmacen . "
    <br>
    <table cellspacing='1' cellpadding='5'>
        <thead>
            <tr ALIGN=center>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>#</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>ALMACEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>PERIODO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>FECHA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>SKU</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>NOMBRE PRODUCTO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>FAMILIA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>CANTIDAD PEDIDA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>TOTAL S/</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>CANTIDAD COMPLETADA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>TOTAL COMPLETADA S/</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>CANTIDAD CANCELADA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>TOTAL CANCELADA S/</th>
            </tr>
        </thead>
    ";

    // Fill Rate por SKU
    $param2 = array(
        "post"      => 2, // ejecutar 18:00
        "almacen"   => $orden['GRUPO'], // Para detalle por SKU
    );

    $result2 = $soap->ListarFVDXsku($param2);
    $fvd_sku_det = json_decode($result2->ListarFVDXskuResult, true);

    foreach ($fvd_sku_det as $det) {
        // if (number_format($det['PORCENTAJE'], 0) != 0) {
        //     if (number_format($det['PORCENTAJE'], 2) <= 75) {
        //         $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . number_format($det['PORCENTAJE'], 2) . " %" . "</b></font></th>";
        //     } elseif (number_format($det['PORCENTAJE'], 2) > 75 && number_format($det['PORCENTAJE'], 2) <= 99) {
        //         $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . number_format($det['PORCENTAJE'], 2) . " %" . "</b></font></th>";
        //     } elseif (number_format($orden['PORCENTAJE'], 2) > 99) {
        //         $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#8fce00' ALIGN=right><font color='black'><b>" . number_format($orden['PORCENTAJE'], 2) . " %" . "</b></font></th>";
        //     }
        // } else {
        //     $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . number_format($orden['PORCENTAJE'], 2) . " %" . "</b></font></th>";
        // }

        $table_fvd .= "
            <tr>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $det['ROW'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $det['ALMACEN'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $det['PERIODO'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $det['FECHA'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $det['SKU'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $det['DESCRIPCION'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $det['FAMILIA'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($det['CANTIDAD_TOTAL'],0) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($det['MONTO'],2) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($det['VEN_QTY_PROCESADA'],0) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($det['VEN_TOT_PROCESADA'],2) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($det['VEN_QTY_CANCELADA'],0) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($det['VEN_TOT_CANCELADA'],2) . "</th>
            </tr>
            ";
    }

    $table_fvd .= "</table>";
}
/* ********************************************** TABLA POR PERIODO (END) ********************************************** */


$mpdf->WriteHTML($table_fvd);
// $mpdf->Output($nombre_archivo . '.pdf', 'I');
$mpdf->Output($nombre_archivo, 'F');
exit;
