<?php
include "../vendor/autoload.php";
include "../config/config.php";
include "Certificate.php";
include "pdfNew.php";

use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Emisor;
use Charles\CFDI\Node\Receptor;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\Complemento\Nomina\Nomina;
use Charles\CFDI\Node\Complemento\Nomina\EmisorN;
use Charles\CFDI\Node\Complemento\Nomina\ReceptorN;
use Charles\CFDI\Node\Complemento\Nomina\Deduccion\Deduccion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\Percepcion;
use Charles\CFDI\Node\Complemento\Nomina\Incapacidad\Incapacidad;
use Charles\CFDI\Node\Complemento\Nomina\OtrosPagos\OtrosPagos;
use Charles\CFDI\Node\Complemento\Nomina\OtrosPagos\SubsidioAlEmpleo;
use Charles\CFDI\Node\Complemento\Nomina\OtrosPagos\CompensacionSaldosAFavor;
use Charles\CFDI\Node\Complemento\Nomina\Deduccion\DetalleDeduccion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\DetallePercepcion;

//$json = file_get_contents("php://input");
$json = file_get_contents('/Applications/XAMPP/htdocs/payroll_project/uploads/ejemploMulti.json');
$ruta = "../uploads/";

/* Ruta del servicio de integracion Pruebas*/
$ws = "https://cfdi33-pruebas.buzoncfdi.mx:1443/Timbrado.asmx?wsdl";
/* Ruta del servicio de integracion Productivo*/
//$ws = "https://timbracfdi33.mx:1443/Timbrado.asmx?wsdl";

if($json){
  $jsonData = json_decode($json, true);
  foreach ($jsonData['data'] as $value) {
    if($value['comprobante'] && $value['empresa']) {
      $comprobante = $value['comprobante'];
      $empresa = $value['empresa'];
      $comprobanteHeader = $comprobante['header'];
      $compobanteEmisor = $comprobante['emisor'];
      $compobanteReceptor = $comprobante['receptor'];
      $compobanteConceptos = $comprobante['conceptos'];
      $compobanteComplemento = $comprobante['complemento'];

      $rfc = $compobanteEmisor['Rfc'];
      $rutaCer = "{$ruta}{$empresa}/{$rfc}/{$rfc}_C.pem";
      $cerFile = file_get_contents($rutaCer);
      $keyFile = file_get_contents("{$ruta}{$empresa}/{$rfc}/{$rfc}_K.pem");
      $cert = new Certificate();
      $comprobanteHeader['NoCertificado'] = $cert->getSerial($rutaCer);

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
      /* El servicio recibe el comprobante (xml) codificado en Base64, el rfc del emisor deberá ser 'AAA010101AAA' para efecto de pruebas*/
      $base64Comprobante = base64_encode($cfdi);
      try {
        $params = array();
        /** Usuario Integrador para pruebas **/
        $params['usuarioIntegrador'] = 'mvpNUXmQfK8=';
        /** Usuario Integrador para Productivo **/
        //$params['usuarioIntegrador'] = '8E5CyvqyxsyGkK0DbKbA8g==';
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

        $xml_READ = simplexml_load_string($xmlTimbrado);
        $ns = $xml_READ->getNamespaces(true);
        $xml_READ->registerXPathNamespace('t', $ns['tfd']);
        $atributos = $xml_READ->xpath('//t:TimbreFiscalDigital');
        foreach ($atributos as $tfd) {
            $selloCFD = $tfd['SelloCFD'];
          	$FechaTimbrado = $tfd['FechaTimbrado'];
          	$UUID = $tfd['UUID'];
          	$noCertificadoSAT = $tfd['NoCertificadoSAT'];
          	$versionSAT = $tfd['Version'];
          	$selloSAT = $tfd['SelloSAT'];
        }

        $xml_READ->registerXPathNamespace('n', $ns['nomina12']);
        $receptor = $xml_READ->xpath('//n:Receptor');
        foreach ($receptor as $recep) {
            $numEmpleado = $recep['NumEmpleado'];
        }

        $header = $xml_READ->xpath('//n:Nomina');
        foreach ($header as $head) {
            $fechaFin = $head['FechaFinalPago'];
        }

        /*Guardamos comprobante timbrado*/
        file_put_contents("{$ruta}{$empresa}/{$rfc}/{$UUID}_{$numEmpleado}_{$fechaFin}.xml", $xmlTimbrado);
        /*Guardamos codigo qr*/
        file_put_contents("{$ruta}{$empresa}/{$rfc}/codigoQr_{$UUID}.jpg", $codigoQr);
        /*Guardamos cadena original del complemento de certificacion del SAT*/
        file_put_contents("{$ruta}{$empresa}/{$rfc}/cadenaOriginal_{$UUID}.txt", $cadenaOriginal);
        $image = "{$ruta}{$empresa}/{$rfc}/codigoQr_{$UUID}.jpg";

        /* Generamos archivo PDF */
        $pdf = new FacturaPdf();
        $xml = json_decode(json_encode($value));
        $xml = $xml->comprobante;
        $nomina = $xml->complemento->nomina12;
        $subsidio = (!empty($nomina->OtrosPagos))? $nomina->OtrosPagos[0]->subsidio->SubsidioCausado : '';

        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->HeaderPay($xml);
        $pdf->HeaderBon();
        $pdf->HeaderNomina($xml->receptor, $nomina);
        $pdf->percep_deducc($nomina->percepcion, $nomina->detallePercepcion, $nomina->deduccion, $nomina->detalleDeduccion, $nomina->header->NumDiasPagados, $subsidio);
        $pdf->Totales($xml);
        $pdf->FooterNomina($selloCFD, $selloSAT, $cadenaOriginal, $image, $UUID, $noCertificadoSAT, $FechaTimbrado);
        $archivo = "{$ruta}{$empresa}/{$rfc}/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf";
        $pdf->Output('F', $archivo);
        $ruta_xml = HOST."/payroll_project/uploads/{$empresa}/{$rfc}/{$UUID}_{$numEmpleado}_{$fechaFin}.xml";
        $ruta_pdf = HOST."/payroll_project/uploads/{$empresa}/{$rfc}/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf";
        $responseFinal = ["status" => true, "message" => "Timbrado Exitoso. ". $comprobanteHeader['Serie']. $comprobanteHeader['Folio'], "url_xml" => $ruta_xml, "url_pdf" => $ruta_pdf];
        echo json_encode($responseFinal)."/n";
      } else {
        $responseFinal = ["status" => false, "message" => $descripcionResultado ." ". $comprobanteHeader['Serie']. $comprobanteHeader['Folio']];
        echo json_encode($responseFinal)."/n";
      }
    } else {
      $responseFinal = ["status" => false, "message" => 'La estructura del Json, es incorrecta'];
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

  if(!empty($nominaData['Incapacidades'])){
    foreach ($nominaData['Incapacidades'] as $value) {
      $nomina->add(new Incapacidad($value));
    }
  }
  if(!empty($nominaData['OtrosPagos'])){
    foreach ($nominaData['OtrosPagos'] as $value) {
      $oPagos = new OtrosPagos($value['header']);
      if(!empty($value['subsidio'])){
        $oPagos->add(new SubsidioAlEmpleo($value['subsidio']));
      }
      if(!empty($value['compensacion'])){
        $oPagos->add(new CompensacionSaldosAFavor($value['compensacion']));
      }
      $nomina->add($oPagos);
    }
  }
  return $nomina;
}
