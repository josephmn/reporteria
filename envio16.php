<?php

date_default_timezone_set("America/Lima");

// DETALLE DE CORREO - ENVIO (SEND)
$envio_para = array(
    'juan.grandez@verdum.com',
    'ventas.geografico@verdum.com',
    'operaciones@verdum.com',
    'ventas.tradicional@verdum.com',
    'facturador.callao@verdum.com',
    'tesoreria.callao@verdum.com',
    'vmantilla@verdum.com',
    'planeamiento@verdum.com',
    'controller@ssghp.com',
);

// DETALLE DE COOREO - EN COPIA (CC)
$envio_cc = array(
    'programador.app02@verdum.com',
    'programador.app03@verdum.com',
    'sistemas@verdum.com',
    'ventas.moderno@verdum.com',
    'backoffice02@verdum.com',
    'analista.operaciones@verdum.com',
);

// DETALLE DE CORREO - COPIA OCULTA (CCO)
$envio_cco = array(
    'reportes@cafealtomayo.com',
);

// ARCHIVOS QUE SE ADJUNTARAN POR RUTA
$odvd = "src/doc/ODVD_" . date("Ymd") . ".pdf"; // orden de venta detallada
$odvdsku = "src/doc/ODVSKU_" . date("Ymd") . ".pdf"; // FILL RATE por SKU

$rutas = array(
    $odvd,
    $odvdsku,
);

$envio_asunto = 'ALERTA FILL RATE - ' . date("Y");

// WS de produccion desde recursoshumanos para consultar credenciales de envio de correo (altomayo.info@cafealtomayo.com.pe)
$wsdl = 'http://localhost:81/PAWEB/WSRecursos.asmx?WSDL';

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

$result = $soap->ConfiguracionCorreo();
$conficorreo = json_decode($result->ConfiguracionCorreoResult, true);

// WS para obtener cuadro de Ordenes de Ventas para FILL RATE
$wsdl2 = 'http://localhost:81/WSREPORT/wsreporteria.asmx?WSDL';

$options2 = array(
    "uri" => $wsdl,
    "style" => SOAP_RPC,
    "use" => SOAP_ENCODED,
    "soap_version" => SOAP_1_1,
    "connection_timeout" => 60,
    "trace" => false,
    "encoding" => "UTF-8",
    "exceptions" => false,
);
$soap2 = new SoapClient($wsdl2, $options2);

// Ordenes de Ventas del Día (04:00 PM)
$param3 = array(
    "post" => 4, // Ordenes de ventas del dia anterior
);

// Ordenes de Ventas del Día (04:00 PM)
$result3 = $soap2->ListarOrdenVentaCab($param3);
$ordenes_anterior = json_decode($result3->ListarOrdenVentaCabResult, true);

// Ordenes de ventas cabecera
$param = array(
    "post" => 2, // Ordenes no completadas aun (pendientes por despachar)
);

// ordenes de ventas cabecera
$result = $soap2->ListarOrdenVentaCab($param);
$ordenes_ventas = json_decode($result->ListarOrdenVentaCabResult, true);

// cantidad de embarques por ordenes
$param1 = array(
    "orden" => $ordenes_ventas[0]['ORDEN_VENTA'],
);
$result1 = $soap2->ListarEmbarquesXorden($param1);
$embarque_det = json_decode($result1->ListarEmbarquesXordenResult, true);

// ENVIO DE CORREO
include_once("src/lib/phpmaileradd/class.phpmailer.php");
include_once("src/lib/phpmaileradd/PHPMailerAutoload.php");

$mail = new PHPMailer;

$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->SMTPAuth = true;
// $mail->SMTPSecure = 'tls';
$mail->Mailer = 'smtp';
$mail->Host = $conficorreo[0]['v_servidor_entrante']; //mail.cafealtomayo.com.pe
$mail->Username = $conficorreo[0]['v_correo_salida']; //reportes@cafealtomayo.com.pe
$mail->Password = $conficorreo[0]['v_password']; //rpt4m2020
$mail->Port = $conficorreo[0]['i_puerto']; //25

$mail->From = ($conficorreo[0]['v_correo_salida']); //reportes@cafealtomayo.com.pe
$mail->FromName = $conficorreo[0]['v_nombre_salida']; // VERDUM PERÚ SAC

// iteramos cantidad de correos en copia (CC)
if (!empty($envio_para)) {
    foreach ($envio_para as $snd) {
        $mail->addAddress($snd);
    }
}

// iteramos cantidad de correos en copia (CC)
if (!empty($envio_cc)) {
    foreach ($envio_cc as $cc) {
        $mail->addCC($cc);
    }
}

// iteramos cantidad de correos en copia oculta (CCO)
if (!empty($envio_cco)) {
    foreach ($envio_cco as $cco) {
        $mail->addCC($cco);
    }
}

/* ********************************************** TABLA ODV DIA (04:00 PM) (BEGIN) ********************************************** */
$tabla_ant = "";
$tabla_ant .= "
    <br>
    <table cellspacing='1' cellpadding='5'>
        <thead>
            <tr ALIGN=center>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>ORDEN VENTA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>FECHA ORDEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>PERIODO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>ALMACEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>RUC</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>RAZON</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>TOTAL ORDEN (S/)</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>CANTIDAD BRUTO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>% PENDIENTE</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>% ATENCION</th>
            </tr>
        </thead>
    ";
foreach ($ordenes_anterior as $orden_an) {

    if (number_format($orden_an['PORCEN_ATENCION'], 0) != 0) {
        if (number_format($orden_an['PORCEN_ATENCION'], 2) <= 75) {
            $pendiente_ant = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . number_format($orden_an['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
        } elseif (number_format($orden_an['PORCEN_ATENCION'], 2) > 75 && number_format($orden_an['PORCEN_ATENCION'], 2) <= 99) {
            $pendiente_ant = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ffff00' ALIGN=right><font color='black'><b>" . number_format($orden_an['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
        } elseif (number_format($orden_an['PORCEN_ATENCION'], 2) > 99) {
            $pendiente_ant = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#8fce00' ALIGN=right><font color='black'><b>" . number_format($orden_an['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
        }
    } else {
        $pendiente_ant = "<th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' bgcolor='#ff0000' ALIGN=right><font color='white'><b>" . number_format($orden_an['PORCEN_ATENCION'], 2) . " %" . "</b></font></th>";
    }

    $tabla_ant .= "
            <tr>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_an['ORDEN_VENTA'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_an['FECHA_ORDEN'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_an['PERIODO'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=center>" . $orden_an['ALMACEN'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden_an['RUC'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=left>" . $orden_an['RAZON'] . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_an['MONTO_ORDEN'], 2) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_an['QTYBRUTO'], 0) . "</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . number_format($orden_an['PORCEN_PENDIENTE'], 2) . " %" . "</th>
                " . $pendiente_ant . "
            </tr>
            ";
}
$tabla_ant .= "</table>";
/* ********************************************** TABLA ODV DIA (04:00 PM) (END) ********************************************** */

/* ********************************************** TABLA POR PERIODO (BEGIN) ********************************************** */
$table_fillrate = "";
$table_fillrate .= "
    <br>
    <table cellspacing='1' cellpadding='5'>
        <thead>
            <tr ALIGN=center>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>ORDEN VENTA</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>FECHA ORDEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>PERIODO</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>ALMACEN</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>RUC</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>RAZON</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>TOTAL ORDEN (S/)</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>% PENDIENTE</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'>% ATENCION</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'># EMBARQUES</th>
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#D0D0D0'># DIAS TRANSCURRIDOS</th>
            </tr>
        </thead>
    ";
foreach ($ordenes_ventas as $orden) {

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

    $table_fillrate .= "
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
}
$table_fillrate .= "</table>";
/* ********************************************** TABLA POR PERIODO (END) ********************************************** */

$saludo = "";
$hora =  date("H");

if ($hora >= 0 && $hora <= 11) {
    $saludo = 'buenos días,';
} else if ($hora >= 12 && $hora <= 18) {
    $saludo = 'buenas tardes,';
} else if ($hora >= 19 && $hora <= 23) {
    $saludo = 'buenas noches,';
}

$mail->isHTML(true);

// iteramos rutas de archivos que se enviaran por correos
if (!empty($rutas)) {
    foreach ($rutas as $rutas) {
        $mail->AddAttachment($rutas);
    }
}

$mail->CharSet = "utf-8";
$mail->Subject = $envio_asunto;

// cuerpo de correo
$mail->Body = "
    Hola " . $saludo . "</b>
    <br>
    <br>
    Se envian las Ordenes de Ventas del dia: " . date('d-m-Y') . ".
    <br>
    " . $tabla_ant . "
    <br>
    <br>
    Se envian las Ordenes de Ventas pendientes al periodo " . date("Ym") . ".
    <br>
    " . $table_fillrate . "
    <br>
    <br>
    Saludos,
    <br>
    <br>
    VERDUM - ALERTA DIARIA DE FILLRATE
    <br>
    <br>
    <img src='https://verdum.com/recursoshumanos/public/dist/img/footer_verdum2.png'>";

if (!$mail->send()) {
    $output = 2; //	ERROR AL ENVIAR CORREO
} else {
    $output = 1; // SE ENVIO CORRECTAMENTE
}
exit;
