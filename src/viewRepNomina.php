<?php
//ini_set('display_errors', true);
$basePath = dirname(__DIR__);

include "{$basePath}/vendor/autoload.php";
include "pdfRepNomina.php";

//$requestJson = file_get_contents("../uploads/resumenN.json");
$requestJson = file_get_contents("php://input");
file_put_contents("../uploads/resumenN.json", $requestJson);
$jsonData = json_decode($requestJson, true);


$empresa = $jsonData['empresa'];
$TotalPerc = $jsonData['totales']['TotalPercepciones'];
$subsidio = $jsonData['percepciones'][40];
$maternidad = $jsonData['deducciones'][10];
$general = $jsonData['deducciones'][11];
$riesgoT = $jsonData['deducciones'][12];

$baseI = (float)$TotalPerc - ((float)$subsidio + (float)$maternidad + (float)$general + (float)$riesgoT);


$pdf = new ReportePdfNomina();
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',16);
$pdf->HeaderP($jsonData['requeridosReporte']);
$pdf->percep_deducc($jsonData['percepciones'], $jsonData['deducciones'],$jsonData['totales']['TotalPercepciones'], $jsonData['totales']['TotalDeducciones']);
$archivo = "../uploads/reportes/".$empresa."_reporteNomina.pdf";
$pdf->totales($jsonData['totales'], $baseI);
$pdf->AddPage('L');
$pdf->conceptos_esquema($jsonData['esquemaIngresos'], $jsonData['esquemaDescuentos'], $jsonData['totalesEsquema']['TotalPercepcionesEsquema'], $jsonData['totalesEsquema']['TotalDeduccionesEsquema']);
$pdf->totalesEsquema($jsonData['totalesEsquema']);
$pdf->Output('F', $archivo);
