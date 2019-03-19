<?php
//ini_set('display_errors', true);
$basePath = dirname(__DIR__);

include "{$basePath}/vendor/autoload.php";
include "parserXml.php";
include "pdfNewXml.php";
include "obtenerEmpresa.php";

/*$empresa = "9999";
$rfc = "AAA010101AAA";
$archivo = "3e7dc291-16c0-4620-9244-0e8ff4c4f50f.xml";*/

$empresa = base64_decode($_GET["e"]);
$rfc = base64_decode($_GET["r"]);
$archivo = base64_decode($_GET["a"]);

/*$arrayRfcs = ["MIN120828HI0", "BON150210EN4", "HEI1501217Y9", "FAP141125CR3"];
if (!in_array($rfc, $arrayRfcs)) {
  die('No existe la configuracion de la empresa!');
}*/

$rutaFile = "{$basePath}/uploads/{$empresa}/{$rfc}/";
$data = "{$rutaFile}{$archivo}";
$arrayXml = parseXML($data);

$UUID = $arrayXml[13]['timbreFiscal']['UUID'];
$numEmpleado = $arrayXml[6]['receptorNomina']['NumEmpleado'];
$fechaFin = $arrayXml[4]['headerNomina']['FechaFinalPago'];
$headerXml = $arrayXml[0]['header'];
$cadenaOriginalCertificada = getCadenaOriginalCertificacion($arrayXml[13]['timbreFiscal']);
$codigoQR = getQRCode($rfc,$arrayXml[2]['receptor']['Rfc'], $headerXml['Total'], $UUID, $basePath);

$pdf = new FacturaPdfXml();
$subsidio = (!empty($arrayXml[12]['otrosPagos'])) ? $arrayXml[12]['otrosPagos']['Importe'] : 0;
$sCausado = (!empty($arrayXml[13]['subsidioAlEmpleo'])) ? $arrayXml[13]['subsidioAlEmpleo']['SubsidioCausado'] : 0;
$isr = (!empty($arrayXml[9]['deduccion']['TotalImpuestosRetenidos'])) ? $arrayXml[9]['deduccion']['TotalImpuestosRetenidos'] : 0;
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->HeaderPay($headerXml);

$headerEmpresa = json_decode(obtenerDatosEmpresa($empresa, $rfc));
$pdf->HeaderG($headerEmpresa->cfdiFiscal);
$pdf->HeaderNomina($arrayXml[2]['receptor'], $arrayXml[4]['headerNomina'], $arrayXml[6]['receptorNomina']);
$pdf->percep_deducc($arrayXml[7]['percepcion'], $arrayXml[8]['detallePercepcion'], $arrayXml[9]['deduccion'], $arrayXml[10]['detalleDeduccion'], $arrayXml[4]['headerNomina']['NumDiasPagados'], $subsidio);
$pdf->Totales($arrayXml[0]['header']);
$pdf->BlockSubsidio($subsidio, $sCausado, $isr);
if(!empty($arrayXml[14]['timbreFiscal'])){
  $pdf->FooterNomina($arrayXml[14]['timbreFiscal'], $cadenaOriginalCertificada, $codigoQR);
}
//$newName = "{$rutaFile}/NEWPDF/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf";
header("Content-Type: application/pdf");
$pdf->Output();
