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
    $this->SetFillColor(191, 191, 192);
    $this->Cell(190, 5, utf8_decode("REPORTE FINIQUITO"), 0, 0, 'C');
    $this->Ln(8);
    $this->SetFont('Arial','B',10);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(190, 4, $request['Empresa'], 0, 0, 'C');
    $this->Ln(6);
    $this->Cell(190, 4, $request['RFC'], 0, 0, 'C');
    $this->Ln(8);
  }

  function FooterP ($request) {
    $this->SetFont('Arial','B',8);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Cell(170, 4, utf8_decode("Recibí de ").$request['Empresa'], 0, 0, 'C');
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Ln(4);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->MultiCell(170, 4, utf8_decode("el pago de los conceptos arriba señalados, los recibo a mi entera conformidad, manifestando que no me reservo derecho, ni acción alguna en contra de la empresa y por lo cual extiendo el presente con carácter de FINIQUITO, dando por terminada de manera definitiva la relación de trabajo que me unia, en vista del presente pago."), 0, 'L');
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Ln(6);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Cell(170, 4, utf8_decode("Se retiene el Impuesto Sobre la Renta por Salarios en base al Articulo 94, 95 y 96 de la ley del ISR Vigente."), 0, 0, 'L');
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Ln(6);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Cell(90, 4, "Ingresos exentos para efectos del impuesto", 0, 0, 'L');
    $this->Cell(80, 4, "0.00", 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Ln(6);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Cell(90, 4, "Ingresos gravados para efectos del impuesto", 0, 0, 'L');
    $this->Cell(80, 4, "0.00", 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Ln(20);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Cell(60, 4, "", 0, 0, 'L');
    $this->Cell(110, 4, "________________________", 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Ln(4);
    $this->Cell(10, 4, "", 0, 0, 'C');
    $this->Cell(90, 4, "", 0, 0, 'L');
    $this->Cell(70, 4, "Firma y Fecha", 0, 0, 'R');
    $this->Cell(20, 4, "", 0, 0, 'C');
  }

  function Headcount ($request, $detPercepcion, $detDeduccion, $tPerc, $tDedu, $netoP) {
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Ln();
    $this->Cell(175, 4, "", 0, 0, 'L');
    $this->Cell(15, 4, $request['Gafete'], 0, 0, 'L');
    $this->Ln();
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(20, 4, "NOMBRE: ", 0, 0, 'L');
    $this->Cell(150, 4, $request['Nombre'], 0, 0, 'L');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln();
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(30, 4, "RFC:", 0, 0, 'L');
    $this->Cell(65, 4, $request['RFC'], 0, 0, 'L');
    $this->Cell(30, 4, "NSS:", 0, 0, 'L');
    $this->Cell(55, 4, $request['NSS'], 0, 0, 'L');
    $this->Ln(8);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(30, 4, "FECHA INGRESO:", 0, 0, 'L');
    $this->Cell(65, 4, $request['FecIngreso'], 0, 0, 'L');
    $this->Cell(30, 4, "FECHA BAJA:", 0, 0, 'L');
    $this->Cell(55, 4, $request['FecIngreso'], 0, 0, 'L');
    $this->Ln(4);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(30, 4, utf8_decode("AÑOS LABORADOS:"), 0, 0, 'L');
    $this->Cell(65, 4, "1", 0, 0, 'L');
    $this->Cell(30, 4, utf8_decode("DÍAS TRABAJADOS:"), 0, 0, 'L');
    $this->Cell(55, 4, "365", 0, 0, 'L');
    $this->Ln(4);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(30, 4, "SALARIO MENSUAL:", 0, 0, 'L');
    $this->Cell(140, 4, "0.00", 0, 0, 'L');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln(4);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(30, 4, utf8_decode("SALARIO DIARIO:"), 0, 0, 'L');
    $this->Cell(65, 4, $request['SueldoDiario'], 0, 0, 'L');
    $this->Cell(30, 4, "SMG:", 0, 0, 'L');
    $this->Cell(55, 4, "0.00", 0, 0, 'L');
    $this->Ln(4);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(40, 4, utf8_decode("SALARIO BASE COTIZACIÓN:"), 0, 0, 'L');
    $this->Cell(55, 4, "0.00", 0, 0, 'L');
    $this->Cell(30, 4, "UMA:", 0, 0, 'L');
    $this->Cell(55, 4, "0.00", 0, 0, 'L');
    $this->Ln(12);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(60, 4, "PERCEPCIONES", 0, 0, 'L');
    $this->Cell(60, 4, utf8_decode("PROPORCIÓN"), 0, 0, 'C');
    $this->Cell(50, 4, "IMPORTES", 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln(8);
    foreach ($detPercepcion as $value) {
      if ($value['Importe'] > 0) {
        $this->Cell(10, 4, "", 0, 0, 'L');
        $this->Cell(60, 4, $this->reduceText($value['Concepto'], 42), 0, 0, 'L');
        $this->Cell(60, 4, "", 0, 0, 'C');
        $this->Cell(50, 4, number_format($value['Importe'], 2), 0, 0, 'R');
        $this->Cell(10, 4, "", 0, 0, 'L');
        $this->Ln(4);
      }
    }
    $this->Ln(8);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(60, 4, "Total Percepciones", 0, 0, 'R');
    $this->Cell(50, 4, number_format($tPerc, 2), 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln(12);
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Cell(60, 4, "DEDUCCIONES", 0, 0, 'L');
    $this->Cell(60, 4, utf8_decode("PROPORCIÓN"), 0, 0, 'C');
    $this->Cell(50, 4, "IMPORTES", 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln(8);
    
    foreach ($detDeduccion as $value) {
      if ($value['Importe'] > 0) {
        $this->Cell(10, 4, "", 0, 0, 'L');
        $this->Cell(60, 4, $this->reduceText($value['Concepto'], 42), 0, 0, 'L');
        $this->Cell(60, 4, "", 0, 0, 'C');
        $this->Cell(50, 4, number_format($value['Importe'], 2), 0, 0, 'R');
        $this->Cell(10, 4, "", 0, 0, 'L');
        $this->Ln(4);
      }
    }

    $this->Ln(8);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(60, 4, "Total Deducciones", 0, 0, 'R');
    $this->Cell(50, 4, number_format($tDedu, 2), 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln(6);
    $this->SetFont('Arial','B',7);
    $this->SetTextColor(5, 5, 5);
    $this->Cell(70, 4, "", 0, 0, 'L');
    $this->Cell(60, 4, "Neto a pagar", 0, 0, 'R');
    $this->Cell(50, 4, number_format($netoP, 2), 0, 0, 'R');
    $this->Cell(10, 4, "", 0, 0, 'L');
    $this->Ln(12);
  }

}