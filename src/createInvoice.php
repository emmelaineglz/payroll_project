<?php
include "../vendor/autoload.php";
include "Certificate.php";

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


$json = file_get_contents("php://input");
//$json = file_get_contents('/Applications/XAMPP/htdocs/payroll_project/uploads/ejemplo.json');
$ruta = "../uploads/";
/* Ruta del servicio de integracion*/
$ws = "https://cfdi33-pruebas.buzoncfdi.mx:1443/Timbrado.asmx?wsdl";
$response = '';

if($json) {
  $jsonData = json_decode($json, true);
  if($comprobante = $jsonData['comprobante']) {
    $comprobanteHeader = $comprobante['header'];
    $compobanteEmisor = $comprobante['emisor'];
    $compobanteReceptor = $comprobante['receptor'];
    $compobanteConceptos = $comprobante['conceptos'];
    $compobanteComplemento = $comprobante['complemento'];

    $rfc = $compobanteEmisor['Rfc'];

    $cerFile = file_get_contents("{$ruta}{$rfc}/{$rfc}_C.pem");
    $keyFile = file_get_contents("{$ruta}{$rfc}/{$rfc}_K.pem");

    $cfdi = new CFDI($comprobanteHeader, $cerFile, $keyFile);
    $cfdi->add(new Emisor($compobanteEmisor));
    $cfdi->add(new Receptor($compobanteReceptor));
    foreach ($compobanteConceptos as $concepto) {
      $cfdi->add(new Concepto($concepto));
    }
    if($compobanteComplemento) {
      $nomina = complementoNomina($compobanteComplemento['nomina12']);
      $cfdi->add($nomina);
    }
  //  echo $cfdi;
    /* El servicio recibe el comprobante (xml) codificado en Base64, el rfc del emisor deberá ser 'AAA010101AAA' para efecto de pruebas*/
    $base64Comprobante = base64_encode($cfdi);
    try {
      $params = array();
      $params['usuarioIntegrador'] = 'mvpNUXmQfK8=';
      $params['xmlComprobanteBase64'] = $base64Comprobante;
      $params['idComprobante'] = rand(5, 999999);
      $client = new SoapClient($ws,$params);
      $response = $client->__soapCall('TimbraCFDI', array('parameters' => $params));
    } catch (SoapFault $fault){
      echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
    }

    $tipoExcepcion = $response->TimbraCFDIResult->anyType[0];
    $numeroExcepcion = $response->TimbraCFDIResult->anyType[1];
    $descripcionResultado = $response->TimbraCFDIResult->anyType[2];
    $xmlTimbrado = $response->TimbraCFDIResult->anyType[3];
    $codigoQr = $response->TimbraCFDIResult->anyType[4];
    $cadenaOriginal = $response->TimbraCFDIResult->anyType[5];

    if($xmlTimbrado != ''){
      /*Guardamos comprobante timbrado*/
      file_put_contents("/Applications/XAMPP/htdocs/payroll_project/uploads/{$rfc}/comprobanteTimbrado.xml", $xmlTimbrado);
      /*Guardamos codigo qr*/
      file_put_contents("/Applications/XAMPP/htdocs/payroll_project/uploads/{$rfc}/codigoQr.jpg", $codigoQr);
      /*Guardamos cadena original del complemento de certificacion del SAT*/
      file_put_contents("/Applications/XAMPP/htdocs/payroll_project/uploads/{$rfc}/cadenaOriginal.txt", $cadenaOriginal);

      $responseFinal = ["status" => true, "message" => "Timbrado Exitoso", "data" => (string)$xmlTimbrado];
      echo json_encode($responseFinal);
    } else {
      $responseFinal = ["status" => false, "message" => $descripcionResultado];
      echo json_encode($responseFinal);
    }
  }
}

function complementoNomina($nominaData) {
  $nominaHeader = $nominaData['header'];
  $nominaEmisor = $nominaData['emisor'];
  $nominaReceptor = $nominaData['receptor'];
  $nominaDeduccion = $nominaData['deduccion'];
  $nominaDetalleDeduccion = $nominaData['detalleDeduccion'];
  $nominaPercepcion = $nominaData['percepcion'];
  $nominaDetallePercepcion = $nominaData['detallePercepcion'];

  $nomina = new Nomina($nominaHeader);
  $nomina->add(new EmisorN($nominaEmisor));
  $nomina->add(new ReceptorN($nominaReceptor));

  $nomina->add(new Percepcion($nominaPercepcion));
  foreach ($nominaDetallePercepcion as $percepcion) {
    $nomina->add(new DetallePercepcion($percepcion));
  }

  $nomina->add(new Deduccion($nominaDeduccion));
  foreach ($nominaDetalleDeduccion as $deduccion) {
    $nomina->add(new DetalleDeduccion($deduccion));
  }
  return $nomina;
}
