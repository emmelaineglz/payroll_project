<?php
include "../vendor/autoload.php";
include "Certificate.php";

$cer = $_FILES['certificate']['tmp_name'];
$cer_name = $_FILES['certificate']['name'];
$key = $_FILES['key']['tmp_name'];
$key_name = $_FILES['key']['name'];
$pass = $_POST['pass'];
$rfc = $_POST['rfc'];
$empresa = $_POST['empresa'];
/*$cer = '../uploads/00001000000307490701_BON150210EN4.cer';
$cer_name = '00001000000307490701_BON150210EN4.cer';
$key = '../uploads/CSD_Unidas_BON150210EN4_20171201_180455.key';
$key_name = 'CSD_Unidas_BON150210EN4_20171201_180455.key';
$pass = 'Bony2015';
$rfc = 'BON150210EN4';
$empresa = '1';*/
$registro = 0;

/* Ruta del servicio de integracion Pruebas*/
$ws = "https://cfdi33-pruebas.buzoncfdi.mx:1443/Timbrado.asmx?wsdl";
/* Ruta del servicio de integracion Productivo*/
//$ws = "https://timbracfdi33.mx:1443/Timbrado.asmx?wsdl";


$process = new Certificate ();
$process->makeKeyPem($key, $pass, $rfc, $empresa);
$process->makeCerPem($cer, $rfc, $empresa);
$val = 0;
if(file_exists("../uploads/{$empresa}/{$rfc}/{$rfc}_K.pem")){
  if(copy($key, "../uploads/{$empresa}/{$rfc}/{$key_name}")){
    $val = $val + 1;
  }
}
if(file_exists("../uploads/{$empresa}/{$rfc}/{$rfc}_C.pem")){
  if(copy($cer, "../uploads/{$empresa}/{$rfc}/{$cer_name}")){
    $val = $val + 1;
  }
}
if($val > 0){
  /*Archivo .cer base64*/
  $rutaCer = "../uploads/{$empresa}/{$rfc}/{$cer_name}";
  $base64Cer = file_get_contents($rutaCer);
  $base64Cer = base64_encode($base64Cer);

  /*Archivo .key base64*/
  $rutaKey = "../uploads/{$empresa}/{$rfc}/{$key_name}";
  $base64Key = file_get_contents($rutaKey);
  $base64Key = base64_encode($base64Key);
  try {
    $params = array();
    /*Nombre del usuario integrador asignado, para efecto de pruebas utilizaremos 'mvpNUXmQfK8='*/
    $params['usuarioIntegrador'] = 'mvpNUXmQfK8=';

    //$params['usuarioIntegrador'] = '8E5CyvqyxsyGkK0DbKbA8g==';
    /* Rfc emisor a registrar 64*/
    $params['rfcEmisor'] = $rfc;
    /*Archivo .cer en base 64, sello digital del emisor*/
    $params['base64Cer'] = $base64Cer;
    /*Archivo .key en base 64, sello digital del emisor*/
    $params['base64Key'] = $base64Key;
    /*Contraseña, sello digital del emisor*/
    $params['contrasena'] = $pass;

    $client = new SoapClient($ws,$params);
    $respon = $client->__soapCall('RegistraEmisor', array('parameters' => $params));
  }
  catch (SoapFault $fault){
    echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
  }

  $numeroExcepcion = $respon->RegistraEmisorResult->anyType[1];
  $descripcionResultado = $respon->RegistraEmisorResult->anyType[2];

  if($numeroExcepcion == "0"){
    $registro = 1;
    /*El emisor fue registrado correctamente*/
  }
}
$response['status'] = ($val > 0 && $registro == 1)? true : false;
$response['message'] = ($val > 0 && $registro == 1)? "Los archivos para la empresa {$empresa} con rfc {$rfc} se han cargado correctamente!" : "Error al cargar los archivos. ".$descripcionResultado;
echo json_encode($response);
