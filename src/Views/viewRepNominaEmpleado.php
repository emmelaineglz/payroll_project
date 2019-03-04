<?php
//ini_set('display_errors', true);
$basePath = dirname(__DIR__);

include "{$basePath}/../vendor/autoload.php";
include "../pdfRepNominaEmpleado.php";

//$requestJson = file_get_contents("/Applications/XAMPP/htdocs/payroll_project/uploads/ejemploNominaEmpleado.json");
$requestJson = file_get_contents("php://input");
file_put_contents("../../uploads/resumenNEmp.json", $requestJson);
$jsonData = json_decode($requestJson, true);

$pdf = new ReportePdfNomina();
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',16);
$pdf->HeaderP($jsonData['header']);

$countEmp = 0;
foreach ($jsonData['empleados'] as $value) {
    if ($countEmp === 3) {
        $pdf->AddPage('L');
        $countEmp = 0;
    }
    $arrayHC['headcount'] = [];
    $arrayHC['headcount']['Gafete'] = $value['Gafete'];
    $arrayHC['headcount']['Nombre'] = $value['Nombre'];
    $arrayHC['headcount']['Puesto'] = $value['Puesto'];
    $arrayHC['headcount']['FecIngreso'] = $value['FecIngreso'];
    $arrayHC['headcount']['SueldoDiario'] = $value['SueldoDiario'];
    $arrayHC['headcount']['SDI'] = $value['SDI'];
    $arrayHC['headcount']['DiasPagados'] = $value['DiasPagados'];
    $pdf->Headcount($arrayHC['headcount'], $value['percepciones'], $value['deducciones'], $value['TotalPercepciones'], $value['TotalDeducciones'], $value['NetoPagado']);
    $countEmp ++;
}
$archivo = "../../uploads/reportes/reporteEmpleadosNomina.pdf";
//$archivo = "/Applications/XAMPP/htdocs/payroll_project/uploads/reporteEmpleadosNomina.pdf";
$pdf->Output('F', $archivo);
