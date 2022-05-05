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
    $this->Ln();
    $this->Cell(280, 5, "RESUMEN DE NOMINA", 0, 0, 'C');
    $this->Ln();
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(280, 4, $request['Cliente'], 0, 0, 'C');
    $this->Ln();
    $this->Cell(280, 4, $request['RFC'], 0, 0, 'C');
    $this->Ln();
    $this->Ln();
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "PROCESO:", 0, 0, 'L');
    $this->Cell(15, 4, $request['Proceso'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "DEL:", 0, 0, 'L');
    $this->Cell(15, 4, $request['FechaInicial'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "FECHA:", 0, 0, 'L');
    $this->Cell(15, 4, date('Y-m-d '), 0, 0, 'L');
    $this->Cell(80, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, utf8_decode("NÓMINA:"), 0, 0, 'L');
    $this->Cell(15, 4, $request['TipoNomina'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "AL:", 0, 0, 'L');
    $this->Cell(15, 4, $request['FechaFinal'], 0, 0, 'L');
    $this->Cell(130, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, "PERIODO:", 0, 0, 'L');
    $this->Cell(15, 4, $request['Periodo'], 0, 0, 'L');
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, utf8_decode("AÑO:"), 0, 0, 'L');
    $this->Cell(15, 4, $request['Ejercicio'], 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(18, 4, "EMPLEADOS:", 0, 0, 'L');
    $this->Cell(15, 4, $request['TotalEmpleados'], 0, 0, 'L');
    $this->Cell(150, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Ln();
  }

  function percep_deducc ($detPercepcion, $detDeduccion, $tPerc, $tDedu) {
    $this->SetDrawColor(26, 84, 251);
    $this->SetLineWidth(0.5);
    //$this->Line(50, 50, 250, 50);
    $this->SetFont('Arial','B',9);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(40, 4, "", 0, 0, 'L');
    $this->Cell(90, 4, "Percepciones", 0, 0, 'C');
    $this->Cell(30, 4, "", 0, 0, 'L');
    $this->Cell(90, 4, "Deducciones", 0, 0, 'C');
    $this->Ln();
    $this->Ln();
    //$this->Line(10, 110, 108, 110);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(32, 155, 255);
    $this->Cell(40, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "Clave", 0, 0, 'L');
    $this->Cell(50, 4, utf8_decode("Descripción"), 0, 0, 'L');
    $this->Cell(20, 4, "Importe", 0, 0, 'R');
    // $this->Line(200, 100, 111, 100);
    $this->Cell(30, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "Clave", 0, 0, 'L');
    $this->Cell(50, 4, utf8_decode("Descripción"), 0, 0, 'L');
    $this->Cell(20, 4, "Importe", 0, 0, 'R');
    
    $this->SetFont('Arial','',7);
    $this->SetTextColor(5, 5, 5);
    $this->Ln();
    $this->Ln();

    $currentY = 0;
    foreach ($detPercepcion as $value) {
      if ($value['Importe'] > 0) {
        $currentY = $this->GetY();
        $this->MultiCell(40, 4, "", 0, 'C');
        $this->SetXY($this->GetX()+40, $currentY);
        $this->MultiCell(20, 4, $value['Clave'], 0, 'L');
        $this->SetXY($this->GetX()+20+40, $currentY);
        $this->MultiCell(50, 4, $this->reduceText($value['Concepto'], 42), 0, 'L');
        $this->SetXY($this->GetX()+50+20+40, $currentY);
        $this->MultiCell(20, 4, number_format($value['Importe'], 2), 0, 'R');
      }
    }

    $currentY = 58;
    foreach ($detDeduccion as $value) {
      if ($value['Importe'] > 0) {
        $currentY = $currentY + 4;
        $this->SetXY($this->GetX()+20+50+20+40, $currentY);
        $this->MultiCell(30, 4, "", 0, 'C');
        $this->SetXY($this->GetX()+30+20+50+20+40, $currentY);
        $this->MultiCell(20, 4, $value['Clave'], 0, 'L');
        $this->SetXY($this->GetX()+20+30+20+50+20+40, $currentY);
        $this->MultiCell(50, 4, $this->reduceText($value['Concepto'], 42), 0, 'L');
        $this->SetXY($this->GetX()+50+20+30+20+50+20+40, $currentY);
        $this->MultiCell(20, 4, number_format($value['Importe'], 2), 0, 'R');
      }
    }

    $this->Ln();
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(40, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Total Percepciones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($tPerc, 2), 0, 0, 'R');
    $this->Cell(30, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Total Deducciones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($tDedu, 2), 0, 0, 'R');
  }

  function conceptos_esquema ($ingresos, $descuentos, $tPerc, $tDedu) {
    $this->Ln();
    $this->Ln();
    $this->SetDrawColor(26, 84, 251);
    $this->SetLineWidth(0.5);
    //$this->Line(50, 50, 250, 50);
    $this->SetFont('Arial','B',9);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(40, 4, "", 0, 0, 'L');
    $this->Cell(90, 4, "INGRESOS POR ESQUEMA", 0, 0, 'C');
    $this->Cell(30, 4, "", 0, 0, 'L');
    $this->Cell(90, 4, "DESCUENTOS POR ESQUEMA", 0, 0, 'C');
    $this->Ln();
    $this->Ln();
    //$this->Line(10, 110, 108, 110);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(32, 155, 255);
    $this->Cell(40, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "Clave", 0, 0, 'L');
    $this->Cell(50, 4, utf8_decode("Descripción"), 0, 0, 'L');
    $this->Cell(20, 4, "Importe", 0, 0, 'R');
    // $this->Line(200, 100, 111, 100);
    $this->Cell(30, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "Clave", 0, 0, 'L');
    $this->Cell(50, 4, utf8_decode("Descripción"), 0, 0, 'L');
    $this->Cell(20, 4, "Importe", 0, 0, 'R');
    
    $this->SetFont('Arial','',5);
    $this->SetTextColor(5, 5, 5);
    $this->Ln();
    $this->Ln();

    $currentY = 0;
    foreach ($ingresos as $value) {
      if ($value['Importe'] > 0) {
        $currentY = $this->GetY();
        $this->MultiCell(40, 4, "", 0, 'C');
        $this->SetXY($this->GetX()+40, $currentY);
        $this->MultiCell(20, 4, $value['Clave'], 0, 'L');
        $this->SetXY($this->GetX()+20+40, $currentY);
        $this->MultiCell(50, 4, $this->reduceText($value['Concepto'], 42), 0, 'L');
        $this->SetXY($this->GetX()+50+20+40, $currentY);
        $this->MultiCell(20, 4, number_format($value['Importe'], 2), 0, 'R');
      }
    }

    $currentY = 30;
    foreach ($descuentos as $value) {
      if ($value['Importe'] > 0) {
        $currentY = $currentY + 4;
        $this->SetXY($this->GetX()+20+50+20+40, $currentY);
        $this->MultiCell(30, 4, "", 0, 'C');
        $this->SetXY($this->GetX()+30+20+50+20+40, $currentY);
        $this->MultiCell(20, 4, $value['Clave'], 0, 'L');
        $this->SetXY($this->GetX()+20+30+20+50+20+40, $currentY);
        $this->MultiCell(50, 4, $this->reduceText($value['Concepto'], 42), 0, 'L');
        $this->SetXY($this->GetX()+50+20+30+20+50+20+40, $currentY);
        $this->MultiCell(20, 4, number_format($value['Importe'], 2), 0, 'R');
      }
    }

    $this->Ln();
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(40, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Total Percepciones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($tPerc, 2), 0, 0, 'R');
    $this->Cell(30, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Total Deducciones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($tDedu, 2), 0, 0, 'R');
  }

  function totales ($data, $baseI) {
    $this->Ln();
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Neto a Pagar", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['NetoPagado'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Base Ingresos", 0, 0, 'L');
    $this->Cell(20, 4, number_format($baseI, 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Costo Social", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['TotalCostoSocial'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, utf8_decode("Impuesto de Nómina"), 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['ImpuestoNominaTres'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Provisiones", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['TotalProvisiones'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Honorario ".$data['PorcientoHonorario']."%", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['TotalHonorarios'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Subtotal", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['CostoTotalNomina'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Iva", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['Iva'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "TOTAL", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['Total'], 2), 0, 0, 'R');
  }

  function totalesEsquema ($data) {
    $this->Ln();
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Neto a Pagar", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['NetoPagado'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Honorario ".number_format($data['HonorarioImpuesto'], 2)."%", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['Honorarios'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Subtotal", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['Subtotal'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "Iva", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['Iva'], 2), 0, 0, 'R');
    $this->Ln();
    $this->Cell(180, 4, "", 0, 0, 'L');
    $this->Cell(50, 4, "TOTAL", 0, 0, 'L');
    $this->Cell(20, 4, number_format($data['NetoTotal'], 2), 0, 0, 'R');
  }
}