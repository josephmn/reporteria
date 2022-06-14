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

// ordenes de ventas cabecera
$result = $soap->ListarOrdenVentaCab();
$ordenes_ventas = json_decode($result->ListarOrdenVentaCabResult, true);

// cantidad de embarques por ordenes
$param1 = array(
    "orden" => $ordenes_ventas[0]['ORDEN_VENTA'],
);
$result1 = $soap->ListarEmbarquesXorden($param1);
$embarque_det = json_decode($result1->ListarEmbarquesXordenResult, true);

// ordenes de ventas detalle
$param2 = array(
    "orden" => $ordenes_ventas[0]['ORDEN_VENTA'],
);
$result2 = $soap->ListarOrdenVentaDet($param2);
$orden_det = json_decode($result2->ListarOrdenVentaDetResult, true);

/* ******************************************************************************************* */
$cab_embarques = "";
$cab_embarques_det = "";
$i = 1;
if (count($embarque_det) > 0) {
    foreach ($embarque_det as $emb) {
        $cab_embarques .= "<th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>" . "(" . $i . ") " . $emb['EMBARQUE'] . "</th>";
        $cab_embarques_det .= "<th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>" . $emb['FACTURA'] . " - " . $emb['FECHA_FACTURA'] . "</th>";
        $i++;
    }
} else {
    $cab_embarques = "";
    $cab_embarques_det = "";
}

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

    if ($orden['PORCEN_PENDIENTE'] !== 0) {
        $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . $orden['PORCEN_PENDIENTE'] . "</b></font></th>";
    } else {
        $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden['PORCEN_PENDIENTE'] . "</th>";
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


// tabla det
$tabla_det = "";
$tabla_det .= "
    <br>
    <br>
    <table cellspacing='1' cellpadding='5'>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>" . $cab_embarques . "
            </tr>
            <tr ALIGN=center>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>#</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>SKU</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>DESCRIPCION SKU</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>PRECIO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>QTY ORDEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>TOTAL (S/)</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'>QTY RESTANTE</th>" . $cab_embarques_det . "
            </tr>
        </thead>
    ";

// iteramos el detalle de la Orden de venta original
foreach ($orden_det as $orden_det) {

    // iteramos la n cantidad de embaques que se generen para la atencion (Orden de venta)
    if (count($embarque_det) > 0) {
        $cant_lleva = "";
        foreach ($embarque_det as $embq) {

            $param3 = array(
                "orden"     => $orden_det['ORDEN_VENTA'],
                "embarque"  => $embq['EMBARQUE'],
                "sku"       => $orden_det['SKU'],
            );
            $result3 = $soap->ListarEmbarquesXsku($param3);
            $cntsku = json_decode($result3->ListarEmbarquesXskuResult, true);

            $lleva = $cntsku[0]['CANTIDAD_LLEVA'] == null || $cntsku[0]['CANTIDAD_LLEVA'] == "" || $cntsku[0]['CANTIDAD_LLEVA'] == 0 ? 0 : $cntsku[0]['CANTIDAD_LLEVA'];

            $cant_lleva .= "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $lleva . "</th>";
        }

        if ($orden_det['MONTO_RESTANTE'] > 0) {
            $restante = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . $orden_det['MONTO_RESTANTE'] . "</b></font></th>";
        } else {
            $restante = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden_det['MONTO_RESTANTE'] . "</th>";
        }

        $tabla_det .= "
        <tr>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_det['FILA'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden_det['SKU'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden_det['NOMBRE_PRODUCTO'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_det['PRECIO'], 2) . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden_det['CANTIDAD_ORDEN'] . "</th>
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_det['MONTO_TOTAL'], 2) . "</th>
            " . $restante . "
            " . $cant_lleva . "
        </tr>
        ";
    } else {
        $cant_lleva = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>0</th>";
    }
}

$tabla_det .= "</table>";


$html = $tabla_cab . $tabla_det;

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
