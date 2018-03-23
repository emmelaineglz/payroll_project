<?php
include "../vendor/autoload.php";
include "Certificate.php";

$cer = $_FILES['certificate']['tmp_name'];
$cer_name = $_FILES['certificate']['name'];
$key = $_FILES['key']['tmp_name'];
$key_name = $_FILES['key']['name'];
$pass = $_POST['pass'];
$rfc = $_POST['rfc'];

$process = new Certificate ();
$process->makeKeyPem($key, $pass, $rfc);
$process->makeCerPem($cer, $rfc);
$val = 0;
if(file_exists("../uploads/{$rfc}/{$rfc}_K.pem")){
  if(copy($key, "../uploads/{$rfc}/{$key_name}")){
    $val = $val + 1;
  }
}
if(file_exists("../uploads/{$rfc}/{$rfc}_C.pem")){
  if(copy($cer, "../uploads/{$rfc}/{$cer_name}")){
    $val = $val + 1;
  }
}

$response['status'] = ($val > 0)? true : false;
$response['message'] = ($val > 0)? "Los archivos para el {$rfc} se han cargado correctamente!" : "Error al cargar los archivos";
echo json_encode($response);
