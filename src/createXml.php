<?php
include "../vendor/autoload.php";
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
use Charles\CFDI\Node\Complemento\Nomina\Deduccion\DetalleDeduccion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\DetallePercepcion;


$json = file_get_contents("php://input");
//$json = file_get_contents('/Applications/XAMPP/htdocs/payroll_project/uploads/ejemplo.json');
$ruta = "../uploads/";

if($json) {
  $jsonData = json_decode($json, true);
  if($comprobante = $jsonData['comprobante'] && $jsonData['empresa']) {
    $comprobante = $jsonData['comprobante'];
    $empresa = $jsonData['empresa'];
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
    $num = rand(5, 999999);
    $nameXml = "{$rfc}_{$num}";

    if($cfdi){
      file_put_contents("{$ruta}{$empresa}/{$rfc}/{$nameXml}.xml", $cfdi);
      /* Generamos archivo PDF */
      $pdf = new FacturaPdf();
      $xml = json_decode($json);
      $xml = $xml->comprobante;
      $nomina = $xml->complemento->nomina12;
      $pdf->AddPage();
      $pdf->SetFont('Arial','B',16);
      $pdf->HeaderPay($xml);
      $pdf->HeaderEmisor($xml->emisor);
      $pdf->HeaderNomina($xml->receptor, $nomina);
      $pdf->percep_deducc($nomina->percepcion, $nomina->detallePercepcion, $nomina->deduccion, $nomina->detalleDeduccion);
      //$pdf->FooterNomina($selloCFD, $selloSAT, $cadenaOriginal, $image, $UUID, $noCertificadoSAT, $FechaTimbrado);
      $archivo = "{$ruta}{$empresa}//{$rfc}/{$nameXml}.pdf";
      $pdf->Output('F', $archivo);
      $ruta_xml = "http://159.89.38.133/payroll_project/uploads/{$empresa}/{$rfc}/{$nameXml}.xml";
      $ruta_pdf = "http://159.89.38.133/payroll_project/uploads/{$empresa}/{$rfc}/{$nameXml}.pdf";
      $responseFinal = ["status" => true, "message" => "Timbrado Exitoso", "url_xml" => $ruta_xml, "url_pdf" => $ruta_pdf];
      echo json_encode($responseFinal);
    } else {
      $responseFinal = ["status" => false, "message" => 'Fallo la generacion del XML'];
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

  if($nominaData['Incapacidades']){
    foreach ($nominaData['Incapacidades'] as $value) {
      $nomina->add(new Incapacidad($value));
    }
  }
  return $nomina;
}
