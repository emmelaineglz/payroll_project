<?php
include "/Applications/XAMPP/htdocs/payroll_project/vendor/autoload.php";
include "/Applications/XAMPP/htdocs/payroll_project/src/Certificate.php";
include "/Applications/XAMPP/htdocs/payroll_project/src/pdfNew.php";

use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Emisor;
use Charles\CFDI\Node\Receptor;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\Complemento\Nomina\Nomina;
use Charles\CFDI\Node\Complemento\Nomina\EmisorN;
use Charles\CFDI\Node\Complemento\Nomina\ReceptorN;
use Charles\CFDI\Node\Complemento\Nomina\Deduccion\Deduccion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\Percepcion;
use Charles\CFDI\Node\Complemento\Nomina\Deduccion\DetalleDeduccion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\DetallePercepcion;


//$json = file_get_contents("php://input");
$json = file_get_contents('/Applications/XAMPP/htdocs/payroll_project/uploads/ejemplo0.json');
$ruta = "../uploads/";
$file_cer = '/Applications/XAMPP/htdocs/payroll_project/uploads/AAA010101AAA/AAA010101AAA_C.pem';
/* Ruta del servicio de integracion*/
$ws = "https://cfdi33-pruebas.buzoncfdi.mx:1443/Timbrado.asmx?wsdl";
$response = '';

$cert = new Certificate();
$cert->getSerial($file_cer);

/*if($json){
  $jsonData = json_decode($json, true);
  foreach ($jsonData['data'] as $value) {
    var_dump($value, 'VALUE');
  }
}*/
