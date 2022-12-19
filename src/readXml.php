<?php

/*ini_set('display_errors', true);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

$basePath = dirname(__DIR__);

include "{$basePath}/vendor/autoload.php";
include "parserXml.php";
include "pdfNewXml.php";
include "obtenerEmpresa.php";
include "../config/config.php";

/*$empresa = "9999";
$rfc = "AAA010101AAA";
$archivo = "3e7dc291-16c0-4620-9244-0e8ff4c4f50f.xml";*/

$empresa = trim(base64_decode($_GET["e"]));
$rfc = str_replace(" ", "", base64_decode($_GET["r"]));
$archivo = trim(base64_decode($_GET["a"]));

/*$arrayRfcs = ["MIN120828HI0", "BON150210EN4", "HEI1501217Y9", "FAP141125CR3"];
if (!in_array($rfc, $arrayRfcs)) {
  die('No existe la configuracion de la empresa!');
}*/

$getData = obtenerDatosEmpresa($empresa, $rfc, $archivo);

if ($getData === NULL) {
	$rutaFile = trim("{$basePath}/uploads/{$empresa}/{$rfc}/");
	$data = "{$rutaFile}{$archivo}";
	$arrayXml = parseXML($data);
	$varios = 'NO';
	$rfc = $rfc;
} else {
	$data = $getData[cfdiFiscal];
	$varios = $data[varios];
	$rfc = $data[rfc];
	$arrayXml = parseXML($data[src]);
}

$UUID = ($arrayXml[13]['timbreFiscal']['UUID']) ? $arrayXml[13]['timbreFiscal']['UUID'] : $arrayXml[14]['timbreFiscal']['UUID'];
$numEmpleado = $arrayXml[6]['receptorNomina']['NumEmpleado'];
$fechaFin = $arrayXml[4]['headerNomina']['FechaFinalPago'];
$regPatronal = ($arrayXml[5]) ? $arrayXml[5]['emisorNomina']['RegistroPatronal'] : '';
$headerXml = $arrayXml[0]['header'];
$cadenaOriginalCertificada = getCadenaOriginalCertificacion($arrayXml[14]['timbreFiscal']);
$codigoQR = getQRCode($rfc,$arrayXml[2]['receptor']['Rfc'], $headerXml['Total'], $UUID, $basePath);

$pdf = new FacturaPdfXml();
$subsidio = 0;
$oPrestaciones = 0;
$iXriesgos = 0;

if(!empty($arrayXml[12]['otrosPagos'])) {
	$dataP = $arrayXml[12]['otrosPagos'];
	foreach ($dataP as $value) {
		if($value['Clave'] === 'D600') {
			$subsidio = $value['Importe'];
		} else {
			$subsidio = $dataP['Importe'];
		}
		if($value['Clave'] === 'P056') {
			$oPrestaciones = $value['Importe'];
		}
		if($value['Clave'] === 'P035') {
			$iXriesgos = $value['Importe'];
		}
	}
}

$sCausado = (!empty($arrayXml[13]['subsidioAlEmpleo'])) ? $arrayXml[13]['subsidioAlEmpleo']['SubsidioCausado'] : 0;
$isr = (!empty($arrayXml[9]['deduccion']['TotalImpuestosRetenidos'])) ? $arrayXml[9]['deduccion']['TotalImpuestosRetenidos'] : 0;
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pathLogo = PATH_LOGO;
$imageLogo = "{$empresa}_{$arrayXml[1]['emisor']['Rfc']}";
$logo = "{$pathLogo}{$imageLogo}.png";
$pdf->HeaderPay($headerXml, $logo);

// $headerEmpresa = json_decode(obtenerDatosEmpresa( $empresa, $rfc, $archivo) );
$pdf->HeaderG($data, $regPatronal);
$pdf->HeaderNomina($arrayXml[2]['receptor'], $arrayXml[4]['headerNomina'], $arrayXml[6]['receptorNomina']);
$pdf->percep_deducc($arrayXml[7]['percepcion'], $arrayXml[8]['detallePercepcion'], $arrayXml[9]['deduccion'], $arrayXml[10]['detalleDeduccion'], $arrayXml[4]['headerNomina']['NumDiasPagados'], $subsidio, $oPrestaciones, $iXriesgos); 
$pdf->Totales($arrayXml[0]['header']);
$pdf->BlockSubsidio($subsidio, $sCausado, $isr);
if(!empty($arrayXml[14]['timbreFiscal'])){
  $pdf->FooterNomina($arrayXml[14]['timbreFiscal'], $cadenaOriginalCertificada, $codigoQR);
}
//$newName = "{$rutaFile}/NEWPDF/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf";
header("Content-Type: application/pdf");
$pdf->Output();
