<?php
ini_set('display_errors', true);
include "../vendor/autoload.php";
include "parserXml.php";
include "pdfNewXml.php";

/*$empresa = "6";
$rfc = "MIN120828HI0";
$archivo = "0a4b664d-f9ba-436c-aabb-82f5280a44f0_23733_2018-05-18.xml";*/

$empresa = base64_decode($_GET["e"]);
$rfc = base64_decode($_GET["r"]);
$archivo = base64_decode($_GET["a"]);

$arrayRfcs = ["MIN120828HI0", "BON150210EN4"];
if (!in_array($rfc, $arrayRfcs)) {
  die('No existe la configuracion de la empresa!');
}

$rutaFile = "../uploads/{$empresa}/{$rfc}/";
$data = "{$rutaFile}{$archivo}";
$arrayXml = parseXML($data);
$UUID = $arrayXml[13]['timbreFiscal']['UUID'];
$numEmpleado = $arrayXml[6]['receptorNomina']['NumEmpleado'];
$fechaFin = $arrayXml[4]['headerNomina']['FechaFinalPago'];

$cadenaOriginal = file_get_contents($rutaFile."cadenaOriginal_".$UUID.".txt");
$codigoQR = $rutaFile."codigoQr_".$UUID.".jpg";

$pdf = new FacturaPdfXml();
/*  $xml = $xml->comprobante;
$nomina = $xml->complemento->nomina12;
$subsidio = (!empty($nomina->OtrosPagos))? $nomina->OtrosPagos[0]->subsidio->SubsidioCausado : '';*/
$subsidio = (!empty($arrayXml[12]['otrosPagos'])) ? $arrayXml[12]['otrosPagos']['Importe'] : '';
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->HeaderPay($arrayXml[0]['header']);
if($rfc === "MIN120828HI0"){
  $pdf->HeaderMin();
}elseif($rfc === "BON150210EN4"){
  $pdf->HeaderBon();
}
$pdf->HeaderNomina($arrayXml[2]['receptor'], $arrayXml[4]['headerNomina'], $arrayXml[6]['receptorNomina']);
$pdf->percep_deducc($arrayXml[7]['percepcion'], $arrayXml[8]['detallePercepcion'], $arrayXml[9]['deduccion'], $arrayXml[10]['detalleDeduccion'], $arrayXml[4]['headerNomina']['NumDiasPagados'], $subsidio);
$pdf->Totales($arrayXml[0]['header']);
$pdf->FooterNomina($arrayXml[13]['timbreFiscal'], $cadenaOriginal, $codigoQR);
//$newName = "{$rutaFile}/NEWPDF/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf";
header("Content-Type: application/pdf");
$pdf->Output();
