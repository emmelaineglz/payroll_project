<?php
//ini_set('display_errors', true);
$basePath = dirname(__DIR__);

include "{$basePath}/../vendor/autoload.php";
include "../pdfRepFiniquitosEmpleado.php";

//$requestJson = file_get_contents("/Applications/XAMPP/htdocs/payroll_project/uploads/ejemploNominaEmpleado.json");
$requestJson = file_get_contents("php://input");
file_put_contents("../../uploads/reporteFEmp.json", $requestJson);
$jsonData = json_decode($requestJson, true);

$pdf = new ReportePdfNomina();
$pdf->AddPage('P');


$countEmp = 0;
foreach ($jsonData['empleados'] as $value) {
    if ($countEmp === 1) {
        $pdf->AddPage('P');
        $countEmp = 0;
    }
    $pdf->SetFont('Arial','B',16);
    $pdf->HeaderP($jsonData['header']);
    $arrayHC['headcount'] = [];
    $arrayHC['headcount']['Gafete'] = $value['Gafete'];
    $arrayHC['headcount']['Nombre'] = $value['Nombre'];
    $arrayHC['headcount']['Puesto'] = $value['Puesto'];
    $arrayHC['headcount']['FecIngreso'] = $value['FecIngreso'];
    $arrayHC['headcount']['FecBaja'] = $value['FecBaja'];
    $arrayHC['headcount']['SueldoDiario'] = $value['SueldoDiario'];
    $arrayHC['headcount']['SueldoMensual'] = $value['SueldoMensual'];
    $arrayHC['headcount']['SDI'] = $value['SDI'];
    $arrayHC['headcount']['DiasPagados'] = $value['DiasPagados'];
    $arrayHC['headcount']['AniosLaborados'] = $value['AniosLaborados'];
    $arrayHC['headcount']['Antiguedad'] = $value['Antiguedad'];
    $arrayHC['headcount']['NSS'] = $value['NSS'];
    $arrayHC['headcount']['RFC'] = $value['RFC'];
    $pdf->Headcount($arrayHC['headcount'], $value['percepciones'], $value['deducciones'], $value['TotalPercepciones'], $value['TotalDeducciones'], $value['NetoPagado']);
    $pdf->FooterP($jsonData['header']);
    $countEmp ++;
}
$archivo = "../../uploads/reportes/reporteFiniquitos.pdf";
//$archivo = "/Applications/XAMPP/htdocs/payroll_project/uploads/reporteEmpleadosFIN.pdf";
$pdf->Output('F', $archivo);
