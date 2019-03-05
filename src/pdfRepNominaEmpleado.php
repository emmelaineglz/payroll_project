<?php
//include "/Applications/XAMPP/htdocs/payroll_project/vendor/autoload.php";
include "numberToLetter.php";

use FPDF as FPDF;
use DOMDocument as DOMDocument;
use MarkWilson\XmlToJson\XmlToJsonConverter;
use GreenCape\Xml\Converter;

class ReportePdfNomina extends FPDF {
  function reduceText($text, $maxWidth) {
      return strlen($text) > $maxWidth ? substr($text, 0, $maxWidth)."..." : $text;
  }

  function HeaderP ($request) {
    $this->SetFont('Arial','B',9);
    $this->SetFillColor(191, 191, 192);
    $this->Cell(190, 5, utf8_decode("RESUMEN DE NÓMINA POR EMPLEADO"), 0, 0, 'C');
    $this->Ln(4);
    $this->Ln(4);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(80, 4, $request['Empresa'], 0, 0, 'C');
    $this->Cell(30, 4, $request['RPatronal'], 0, 0, 'C');
    $this->Cell(80, 4, $request['RFC'], 0, 0, 'C');
    $this->Ln(4);
    $this->Cell(25, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "PROCESO:", 0, 0, 'L');
    $this->Cell(15, 4, $request['Proceso'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "PERIODO", 0, 0, 'L');
    $this->Cell(15, 4, $request['Periodo'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "EJERCICIO:", 0, 0, 'L');
    $this->Cell(15, 4, $request['Ejercicio'], 0, 0, 'L');
    $this->Cell(25, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Cell(25, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, utf8_decode("T. NÓMINA:"), 0, 0, 'L');
    $this->Cell(15, 4, $request['TNomina'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "REGISTROS", 0, 0, 'L');
    $this->Cell(15, 4, $request['NoRegistros'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "TOTAL NETO:", 0, 0, 'L');
    $this->Cell(15, 4, "$".number_format($request['TotalNeto'],2), 0, 0, 'L');
    $this->Cell(25, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Ln();
  }

  function Headcount ($request, $detPercepcion, $detDeduccion, $tPerc, $tDedu, $netoP) {
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Ln();
    $this->Cell(7, 4, "", 0, 0, 'L');
    $this->Cell(10, 4, $request['Gafete'], 0, 0, 'L');
    $this->Cell(70, 4, $request['Nombre'], 0, 0, 'L');
    $this->Cell(15, 4, "PUESTO:", 0, 0, 'L');
    $this->Cell(50, 4, $request['Puesto'], 0, 0, 'L');
    $this->Cell(15, 4, "INGRESO:", 0, 0, 'L');
    $this->Cell(15, 4, $request['FecIngreso'], 0, 0, 'L');
    $this->Cell(8, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Cell(7, 4, "", 0, 0, 'L');
    $this->Cell(10, 4, "RFC:", 0, 0, 'L');
    $this->Cell(25, 4, $request['RFC'], 0, 0, 'L');
    $this->Cell(10, 4, "NSS:", 0, 0, 'L');
    $this->Cell(25, 4, $request['NSS'], 0, 0, 'L');
    $this->Cell(20, 4, "S.DIARIO:", 0, 0, 'L');
    $this->Cell(20, 4, "$".number_format($request['SueldoDiario'], 2), 0, 0, 'L');
    $this->Cell(10, 4, "SDI:", 0, 0, 'L');
    $this->Cell(20, 4, "$".number_format($request['SDI'], 2), 0, 0, 'L');
    $this->Cell(25, 4, "DIAS PAGADOS:", 0, 0, 'L');
    $this->Cell(10, 4, $request['DiasPagados'], 0, 0, 'R');
    $this->Cell(8, 4, "", 0, 0, 'L');
    $this->Ln(8);
    $this->SetDrawColor(26, 84, 251);
    $this->SetLineWidth(0.5);
    $this->SetFont('Arial','B',9);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(95, 4, "Percepciones", 0, 0, 'C');
    $this->Cell(95, 4, "Deducciones", 0, 0, 'C');
    $this->Ln(10);
    $this->SetFont('Arial','',5);
    $this->SetTextColor(5, 5, 5);
    $currentY = 0;
    foreach ($detPercepcion as $value) {
      if ($value['Importe'] > 0) {
        $currentY = $this->GetY();
        $this->MultiCell(15, 4, "", 0, 'L');
        $this->SetXY($this->GetX()+15, $currentY);
        $this->MultiCell(40, 4, $this->reduceText($value['Concepto'], 42), 0, 'L');
        $this->SetXY($this->GetX()+40+15, $currentY);
        $this->MultiCell(20, 4, number_format($value['Importe'], 2), 0, 'R');
      }
    }

    $space = 0;
    if (sizeof($detPercepcion) > sizeof($detDeduccion)) {
      $space = 25;
    } else if (sizeof($detPercepcion) < sizeof($detDeduccion)){
      $space = 15;
    } else {
      $space = 20;
    }


    $currentY = $this->GetY() - ($space);
    foreach ($detDeduccion as $value) {
      if ($value['Importe'] > 0) {
        $currentY = $currentY + 4;
        $this->SetXY($this->GetX()+20+40+15, $currentY);
        $this->MultiCell(35, 4, "", 0, 'C');
        $this->SetXY($this->GetX()+35+20+40+15, $currentY);
        $this->MultiCell(40, 4, $this->reduceText($value['Concepto'], 42).$space, 0, 'L');
        $this->SetXY($this->GetX()+40+35+20+40+15, $currentY);
        $this->MultiCell(20, 4, number_format($value['Importe'], 2), 0, 'R');
      }
    }

    $this->SetXY($this->GetX(), $this->GetY()+6);
    $this->Ln(4);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(15, 4, "", 0, 0, 'L');
    $this->Cell(40, 4, "Total Percepciones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($tPerc, 2), 0, 0, 'R');
    $this->Cell(35, 4, "", 0, 0, 'L');
    $this->Cell(40, 4, "Total Deducciones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($tDedu, 2), 0, 0, 'R');
    $this->Ln();
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(110, 4, "", 0, 0, 'L');
    $this->Cell(40, 4, "Neto a Pagar", 0, 0, 'L');
    $this->Cell(20, 4, number_format($netoP, 2), 0, 0, 'R');
    $this->Ln(12);
  }
}