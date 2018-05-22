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
/*$cer = '../uploads/CSD01_AAA010101AAA.cer';
$cer_name = 'CSD01_AAA010101AAA.cer';
$key = '../uploads/CSD01_AAA010101AAA.key';
$key_name = 'CSD01_AAA010101AAA.key';
$pass = '12345678a';
$rfc = 'AAA010101AAA';
$empresa = '9999';*/


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

$response['status'] = ($val > 0)? true : false;
$response['message'] = ($val > 0)? "Los archivos para la empresa {$empresa} con rfc {$rfc} se han cargado correctamente!" : "Error al cargar los archivos";
echo json_encode($response);
