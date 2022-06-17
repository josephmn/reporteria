<?php

date_default_timezone_set("America/Lima");

// DETALLE DE CORREO - ENVIO (SEND)
$envio_para = array(
    'programador.app02@verdum.com',
    'sistemas@verdum.com'
);

// DETALLE DE COOREO - EN COPIA (CC)
$envio_cc = array(
    // 'programador.app01@verdum.com',
    // 'programador.app03@verdum.com'
);

// DETALLE DE CORREO - COPIA OCULTA (CCO)
$envio_cco = array(
    'altomayo.info@cafealtomayo.com.pe'
);

// ARCHIVOS QUE SE ADJUNTARAN POR RUTA
$odvd = "src/doc/ODVD_" . date("Ymd") . ".pdf"; // orden de venta detallada

$rutas = array(
    $odvd,
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

// Ordenes de ventas cabecera
$param = array(
    "post" => 2, // Ordenes no completadas aun (pendientes por despachar)
);

// ordenes de ventas cabecera
$result1 = $soap2->ListarOrdenVentaCab($param);
$ordenes_ventas = json_decode($result1->ListarOrdenVentaCabResult, true);

// cantidad de embarques por ordenes
$param1 = array(
    "orden" => $ordenes_ventas[0]['ORDEN_VENTA'],
);
$result2 = $soap2->ListarEmbarquesXorden($param1);
$embarque_det = json_decode($result2->ListarEmbarquesXordenResult, true);

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

/* ***************************************** (BEGIN) TABLE ENVIADA EN CORREO ***************************************** */

$table_fillrate = "";
$table_fillrate .= "
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
                <th style='border: 1px solid black; border-collapse: collapse; border-color: black;' bgcolor='#8fce00'># DIAS TRANSCURRIDOS</th>
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

    $table_fillrate .= "
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
            <th style='border: 1px solid black; border-collapse: collapse; border-color: black; font-weight: normal' ALIGN=right>" . $orden['DIAS'] . " dia(s) desde OV.</th>
        </tr>
        ";
}
$table_fillrate .= "</table>";

/* ***************************************** (END) TABLE ENVIADA EN CORREO ***************************************** */


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
    Se envian las Ordenes de Ventas pendientes en el periodo " . date("Ym") . ".
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
