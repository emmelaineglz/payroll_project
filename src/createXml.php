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
use Charles\CFDI\Node\Complemento\Nomina\OtrosPagos\OtrosPagos;
use Charles\CFDI\Node\Complemento\Nomina\OtrosPagos\SubsidioAlEmpleo;
use Charles\CFDI\Node\Complemento\Nomina\OtrosPagos\CompensacionSaldosAFavor;
use Charles\CFDI\Node\Complemento\Nomina\Deduccion\DetalleDeduccion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\DetallePercepcion;
use Charles\CFDI\Node\Complemento\Nomina\Percepcion\HorasExtras;


$json = file_get_contents("php://input");
//$json = file_get_contents('/Applications/XAMPP/htdocs/payroll_project/uploads/ejemplo.json');
$ruta = "../uploads/";

if($json) {
  $jsonData = json_decode($json, true);
  if($jsonData['comprobante'] && $jsonData['empresa']) {
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
      $complementoValidado = validarNodos($compobanteComplemento['nomina12']);
      $nomina = complementoNomina($complementoValidado);
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
      $subsidio = (!empty($nomina->OtrosPagos))? $nomina->OtrosPagos[0]->subsidio->SubsidioCausado : '';

      $pdf->AddPage();
      $pdf->SetFont('Arial','B',16);
      $pdf->HeaderPay($xml);
      if($rfc === "MIN120828HI0"){
        $pdf->HeaderMin();
      }elseif($rfc === "BON150210EN4"){
        $pdf->HeaderBon();
      }
      $pdf->HeaderNomina($xml->receptor, $nomina);
      $pdf->percep_deducc($nomina->percepcion, $nomina->detallePercepcion, $nomina->deduccion, $nomina->detalleDeduccion, $nomina->header->NumDiasPagados, $subsidio);
      $pdf->Totales($xml);
      //$pdf->FooterNomina($selloCFD, $selloSAT, $cadenaOriginal, $image, $UUID, $noCertificadoSAT, $FechaTimbrado);
      $archivo = "{$ruta}{$empresa}//{$rfc}/{$nameXml}.pdf";
      $pdf->Output('F', $archivo);
      $ruta_xml = "http://159.89.38.133/payroll_project/uploads/{$empresa}/{$rfc}/{$nameXml}.xml";
      $ruta_pdf = "http://159.89.38.133/payroll_project/uploads/{$empresa}/{$rfc}/{$nameXml}.pdf";

      $mesage = ($comprobanteHeader['Total'] == "0.00" || $comprobanteHeader['Total'] == "0")? "Este comprobante no se timbra por valores en cero" : "Timbrado Exitoso";
      $responseFinal = ["status" => true, "message" => $mesage, "url_xml" => $ruta_xml];
      echo json_encode($responseFinal);
    } else {
      $responseFinal = ["status" => false, "message" => 'Fallo la generacion del XML'];
      echo json_encode($responseFinal);
    }
  } else {
    $responseFinal = ["status" => false, "message" => 'La estructura del Json, es incorrecta'];
      echo json_encode($responseFinal);
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
    if(($percepcion['ImporteGravado'] != "0.00" && $percepcion['ImporteExento'] === "0.00") || ($percepcion['ImporteGravado'] === "0.00" && $percepcion['ImporteExento'] != "0.00") || ($percepcion['ImporteGravado'] != "0.00" && $percepcion['ImporteExento'] != "0.00")){
      if($percepcion['TipoPercepcion'] === '019' && isset($percepcion['HorasExtras'])) {
        $percepcionHorasExtra = generarNodoHorasExtras($percepcion);
        $nomina->add($percepcionHorasExtra);
      } else {
        $nomina->add(new DetallePercepcion($percepcion));
      }
    }
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

  if(!empty($nominaData['Incapacidades'])){
    foreach ($nominaData['Incapacidades'] as $value) {
      $nomina->add(new Incapacidad($value));
    }
  }
  return $nomina;
}

function validarNodos($nominaData) {
  $parsedData = removerAtributoVacio($nominaData, 'deduccion', 'TotalImpuestosRetenidos');
  return $parsedData;
}

function removerAtributoVacio($data, $nodo, $attr) {
  if(
    isset($data[$nodo][$attr]) && (
      $data[$nodo][$attr] == '' ||
      $data[$nodo][$attr] == '0.00' ||
      $data[$nodo][$attr] == '0.0' ||
      $data[$nodo][$attr] == '0'
    )
  ) {
    unset($data[$nodo][$attr]);
  }
  return $data;
}

function generarNodoHorasExtras($percepcion) {
  $percepcionHorasExtra['detallePercepcion'] = [
    "TipoPercepcion" => $percepcion['TipoPercepcion'],
    "Clave" => $percepcion['Clave'],
    "Concepto" => $percepcion['Concepto'],
    "ImporteGravado" => $percepcion['ImporteGravado'],
    "ImporteExento" => $percepcion['ImporteExento'],
  ];

  $percepcionHorasExtra['horasExtra'] = [
    "Dias" => $percepcion['HorasExtras']['Dias'],
    "TipoHoras" => $percepcion['HorasExtras']['TipoHoras'],
    "HorasExtra" => $percepcion['HorasExtras']['HorasExtra'],
    "ImportePagado" => $percepcion['HorasExtras']['ImportePagado'],
  ];

  $detallePercepcion = new DetallePercepcion($percepcionHorasExtra['detallePercepcion']);
  $detallePercepcion->add(new HorasExtras($percepcionHorasExtra['horasExtra']));

  return $detallePercepcion;
}