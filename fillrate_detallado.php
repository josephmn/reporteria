<?php

date_default_timezone_set("America/Lima");

$nombre_archivo = "src/doc/" . "ODVD_" . date("Ymd") . ".pdf"; // ODVP (Ordenes De Ventas Pendientes)

include_once("src/lib/mpdf/mpdf.php");

// require "src/lib/mpdf/mpdf.php";

// $css = file_get_contents('css/pdf.css');
$mpdf = new mPDF('utf-8', 'A3-L', 7, 'Arial', 15, 15, 25, 20, 10, 10, 'L');

// $mpdf->SetHTMLHeader('Verdum Perú SAC - ' . date("Y"));
// $mpdf->SetHTMLHeader('<img src="src/img/banner.png" width="500px"/>');
$mpdf->SetHTMLHeader('<table style="width:100%;"><tr><td><h1 style="text-align:center">FILL RATE DETALLADO POR ORDEN DE VENTA</h1></td><td align="right"><img src="src/img/banner.png" width="500" height="50" /></td></tr></table>');

$mpdf->SetHTMLFooter('Pag. {PAGENO} de {nb}');
$mpdf->SetTitle('ORDENES DE VENTAS - VERDUM PERÚ SAC - ' . date("Y"));

// $mpdf->writeHTML($css, 1);
// $mpdf->WriteHTML('<br/><br/><br/><h1 style="text-align:center "><u>FILL RATE - CONTROL DETALLADO</u></h1>');
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

// Ordenes de ventas cabecera
$param = array(
    "post" => 2, // Ordenes no completadas aun (pendientes por despachar)
);

$result = $soap->ListarOrdenVentaCab($param);
$ordenes_ventas = json_decode($result->ListarOrdenVentaCabResult, true);

$html = "";
$tabla = "";

foreach ($ordenes_ventas as $orden) {

    // cantidad de embarques por ordenes
    $param1 = array(
        "orden" => $orden['ORDEN_VENTA'],
    );
    $result1 = $soap->ListarEmbarquesXorden($param1);
    $embarque_det = json_decode($result1->ListarEmbarquesXordenResult, true);

    if ($orden['GRUPO'] != NULL || $orden['GRUPO'] != "") {
        ($orden['ROW'] == 1) ? $grpalmacen = '<h1>' . $orden['GRUPO'] . '</h1>' : $grpalmacen = '<pagebreak><h1>' . $orden['GRUPO'] . '</h1>';
    } else {
        $grpalmacen = '';
    }
    // COMENTARIO
    if ($orden['COMENTARIO'] != NULL || $orden['GRUPO'] != "") {
        $comentario = "<h2> > ODV - <mark>" . $orden['ORDEN_VENTA'] . "</mark> - <annotation content='" . $orden['COMENTARIO'] . "'/></h2>";
    } else {
        $grpalmacen = '';
    }

    // $grpalmacen = ($orden['GRUPO'] != NULL || $orden['GRUPO'] != "") ? '<annotation content=' . $orden['GRUPO'] . '/><h1>' . $orden['GRUPO'] . '</h1>' : ''; //anotacion sale como nota posit

    if ($orden['GRUPO'] != NULL || $orden['GRUPO'] != "") {
        $mpdf->h2toc = array($orden['GRUPO'] => 0, $orden['ORDEN_VENTA'] => 1);
        $mpdf->h2bookmarks = array('H1' => 0, 'H2' => 1);
    };

    // logica para la creacion de tabla en reporte
    // $tabla .= "<p>/=*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*= =*=*=*= verdum perú sac =*=*=*=/</p>";
    $tabla .= "
        <br>
        " . $grpalmacen . "
        " . $comentario . "
        <br>
        <table cellspacing='1' cellpadding='5'>
            <thead>
                <tr ALIGN=center>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>ORDEN VENTA</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>FECHA ORDEN</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>PERIODO</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>ALMACEN</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>RUC</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>RAZON</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>TOTAL ORDEN (S/)</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>% PENDIENTE</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'>% ATENCION</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'># EMBARQUES</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#a7a7a7'># DIAS TRANSCURRIDOS</th>
                </tr>
            </thead>
        ";

    if (number_format($orden['PORCEN_ATENCION'], 0) != 0) {
        if (number_format($orden['PORCEN_ATENCION'], 2) <= 75) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . number_format($orden['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
        } elseif (number_format($orden['PORCEN_ATENCION'], 2) > 75 && number_format($orden['PORCEN_ATENCION'], 2) <= 99) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . number_format($orden['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
        } elseif (number_format($orden['PORCEN_ATENCION'], 2) > 99) {
            $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#8fce00' ALIGN=right><font color='black'><b>" . number_format($orden['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
        }
    } else {
        $pendiente = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . number_format($orden['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
    }

    $tabla .= "
            <tr>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['ORDEN_VENTA'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['FECHA_ORDEN'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['PERIODO'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden['ALMACEN'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden['RUC'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden['RAZON'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden['MONTO_ORDEN'], 2) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden['PORCEN_PENDIENTE'], 2) . " %" . "</th>
                " . $pendiente . "
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . count($embarque_det) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden['DIAS'] . " dia(s) desde OV.</th>
            </tr>
            ";

    $tabla .= "</table>";

    // ordenes de ventas detalle
    $param2 = array(
        "orden" => $orden['ORDEN_VENTA'],
    );
    $result2 = $soap->ListarOrdenVentaDet($param2);
    $orden_det = json_decode($result2->ListarOrdenVentaDetResult, true);

    /* ************************************************************************************************************************************************************************************************** */
    $cab_embarques = "";
    $cab_embarques_det = "";
    $i = 1;
    if (count($embarque_det) > 0) {
        foreach ($embarque_det as $emb) {
            $cab_embarques .= "<th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>" . "(" . $i . ") " . $emb['EMBARQUE'] . "</th>";
            $cab_embarques_det .= "<th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>" . $emb['FACTURA'] . " - " . $emb['FECHA_FACTURA'] . "</th>";
            $i++;
        }
    } else {
        $cab_embarques = "";
        $cab_embarques_det = "";
    }

    $tabla .= "
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
                    <th></th>" . $cab_embarques . "<th></th>
                </tr>
                <tr ALIGN=center>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>ORDEN VENTA</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>#</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>SKU</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>DESCRIPCION SKU</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>PRECIO</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>QTY ORDEN</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>TOTAL (S/)</th>
                    " . $cab_embarques_det . "
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>QTY RESTANTE</th>
                </tr>
            </thead>
        ";

    // iteramos el detalle de la Orden de venta original
    foreach ($orden_det as $orden_det) {

        // iteramos la n cantidad de embaques que se generen para la atencion (Orden de venta)
        if (count($embarque_det) > 0) {

            $fila_lleva = "";
            $qty_lleva = 0;
            $qty_restante = 0;
            $lleva = 0;
            foreach ($embarque_det as $embq) {
                $param3 = array(
                    "orden"     => $orden_det['ORDEN_VENTA'],
                    "embarque"  => $embq['EMBARQUE'],
                    "sku"       => $orden_det['SKU'],
                );
                $result3 = $soap->ListarEmbarquesXsku($param3);
                $cntsku = json_decode($result3->ListarEmbarquesXskuResult, true);

                $lleva = $cntsku[0]['CANTIDAD_LLEVA'] == null || $cntsku[0]['CANTIDAD_LLEVA'] == "" || $cntsku[0]['CANTIDAD_LLEVA'] == 0 ? 0 : $cntsku[0]['CANTIDAD_LLEVA'];

                $fila_lleva .= "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $lleva . "</th>";

                $qty_lleva += $lleva;
            }

            $qty_restante = $orden_det['CANTIDAD_ORDEN'] - $qty_lleva;

            if ($qty_restante == 0) {
                $new_sku = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden_det['SKU'] . "</th>";
                $qty_orden = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden_det['CANTIDAD_ORDEN'] . "</th>";
                $restante = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $qty_restante . "</th>";
            } else {
                if (number_format($orden['PORCEN_ATENCION'], 2) <= 75) {
                    $new_sku = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . $orden_det['SKU'] . "</b></font></th>";
                    $qty_orden = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . $orden_det['CANTIDAD_ORDEN'] . "</b></font></th>";
                    $restante = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . $qty_restante . "</b></font></th>";
                } elseif (number_format($orden['PORCEN_ATENCION'], 2) > 75 && number_format($orden['PORCEN_ATENCION'], 2) <= 99) {
                    $new_sku = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . $orden_det['SKU'] . "</b></font></th>";
                    $qty_orden = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . $orden_det['CANTIDAD_ORDEN'] . "</b></font></th>";
                    $restante = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . $qty_restante . "</b></font></th>";
                }
            }

            $tabla .= "
                <tr>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_det['ORDEN_VENTA'] . "</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_det['FILA'] . "</th>
                    " . $new_sku . "
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden_det['NOMBRE_PRODUCTO'] . "</th>
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_det['PRECIO'], 2) . "</th>
                    " . $qty_orden . "
                    <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_det['MONTO_TOTAL'], 2) . "</th>
                    " . $fila_lleva . "
                    " . $restante . "
                </tr>
                ";
        } else {
            $fila_lleva = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>0</th>";
        }
    }

    $tabla .= "</table><hr style='height:2px;border:none;color:#000000;background-color:#000000;'>";
    // $tabla .= "<p>/*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/</p>";
    // $tabla .= "<p>/=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=/</p>";


    $html = $tabla;
}

$mpdf->WriteHTML($html);
// $mpdf->Output($nombre_archivo . '.pdf', 'I');
$mpdf->Output($nombre_archivo, 'F');
exit;
