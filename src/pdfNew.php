<?php
//include "/Applications/XAMPP/htdocs/payroll_project/vendor/autoload.php";
include "numberToLetter.php";

use FPDF as FPDF;
use DOMDocument as DOMDocument;
use MarkWilson\XmlToJson\XmlToJsonConverter;
use GreenCape\Xml\Converter;

class FacturaPdf extends FPDF {


  public function reduceText($text, $maxWidth) {
      return strlen($text) > $maxWidth ? substr($text, 0, $maxWidth)."..." : $text;
  }

  public function HeaderPay($data){
      $nomina = $data->complemento->nomina12;
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(191, 191, 192);
			//$this->Cell(190, 5, "Comprobante Fiscal Digital por Internet", 0, 0, 'R');
      $this->Ln();
      $this->Cell(153, 5, "RECIBO DE NOMINA", 0, 0, 'R');
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, "Numero", 0, 0, 'L');
      $this->Cell(40, 4, "Forma de Pago", 0, 0, 'L');
      $this->Ln();
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, "12345", 0, 0, 'L');
      $this->Cell(30, 4, $data->header->FormaPago, 0, 0, 'L');
      $this->Ln();
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, "Fecha y Hora de emision", 0, 0, 'L');
      $this->Cell(45, 4, "Lugar de expedicion", 0, 0, 'L');
      $this->Ln();
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, $data->header->Fecha, 0, 0, 'L');
      $this->Cell(40, 4, $data->header->LugarExpedicion, 0, 0, 'L');
    }

    public function HeaderBon() {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 33, 200, 33);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 2, "Datos del Emisor", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 39, 200, 39);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->Cell(25, 4, "Razon Social", 0, 0, 'L');
      $this->Cell(40, 4, "BON&EFFICACE SA DE CV", 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "RFC", 0, 0, 'L');
      $this->Cell(40, 4, "BON150210EN4", 0, 0, 'L');
      $this->Cell(25, 4, "Regimen Fiscal", 0, 0, 'L');
      $this->Cell(50, 4, "General de Ley Personas Morales", 0, 0, 'L');
      $this->Cell(25, 4, "Registro Patronal", 0, 0, 'L');
      $this->Cell(40, 4, "Y6064672105", 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "Colonia", 0, 0, 'L');
      $this->Cell(40, 4, "ROMA NORTE", 0, 0, 'L');
      $this->Cell(25, 4, "Calle y Numero", 0, 0, 'L');
      $this->Cell(50, 4, "GUANAJUATO", 0, 0, 'L');
      $this->Cell(25, 4, "Codigo Postal", 0, 0, 'L');
      $this->Cell(40, 4, "06700", 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "Estado", 0, 0, 'L');
      $this->Cell(40, 4, "Ciudad de Mexico", 0, 0, 'L');
      $this->Cell(25, 4, "Delegacion", 0, 0, 'L');
      $this->Cell(50, 4, "Cuauhtemoc", 0, 0, 'L');
      $this->Cell(25, 4, "Pais", 0, 0, 'L');
      $this->Cell(40, 4, "Mexico", 0, 0, 'L');
		}

    public function HeaderMin() {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 33, 200, 33);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 2, "Datos del Emisor", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 39, 200, 39);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->Cell(25, 4, "Razon Social", 0, 0, 'L');
      $this->Cell(40, 4, "MERCATO INNOVATIVO S.A. DE C.V", 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "RFC", 0, 0, 'L');
      $this->Cell(40, 4, "MIN120828HI0", 0, 0, 'L');
      $this->Cell(25, 4, "Regimen Fiscal", 0, 0, 'L');
      $this->Cell(50, 4, "General de Ley Personas Morales", 0, 0, 'L');
      $this->Cell(25, 4, "Registro Patronal", 0, 0, 'L');
      $this->Cell(40, 4, "Y5453275108", 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "Colonia", 0, 0, 'L');
      $this->Cell(40, 4, "ROMA NORTE", 0, 0, 'L');
      $this->Cell(25, 4, "Calle y Numero", 0, 0, 'L');
      $this->Cell(50, 4, "MEDELLIN 79", 0, 0, 'L');
      $this->Cell(25, 4, "Codigo Postal", 0, 0, 'L');
      $this->Cell(40, 4, "06700", 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "Estado", 0, 0, 'L');
      $this->Cell(40, 4, "Ciudad de Mexico", 0, 0, 'L');
      $this->Cell(25, 4, "Delegacion", 0, 0, 'L');
      $this->Cell(50, 4, "Cuauhtemoc", 0, 0, 'L');
      $this->Cell(25, 4, "Pais", 0, 0, 'L');
      $this->Cell(40, 4, "Mexico", 0, 0, 'L');
		}

    public function HeaderNomina ($receptor, $data) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 60, 200, 60);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 4, "Datos del Receptor", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 66, 200, 66);
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->cell(33, 4, "Nombre", 0, 0, 'L');
      $this->Cell(50, 4, $receptor->Nombre, 0, 0, 'L');
      $this->Ln();
      $this->Cell(33, 4, "No Empleado", 0, 0, 'L');
      $this->Cell(36, 4, $data->receptor->NumEmpleado, 0, 0, 'L');
      $this->Cell(23, 4, "IMSS", 0, 0, 'L');
      $this->Cell(43, 4, $data->receptor->NumSeguridadSocial, 0, 0, 'L');
      $this->Cell(30, 4, "Dias", 0, 0, 'L');
      $this->Cell(25, 4, $data->header->NumDiasPagados, 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "Riesgo Puesto", 0, 0, 'L');
      $this->Cell(36, 4, $data->receptor->RiesgoPuesto, 0, 0, 'L');
      $this->Cell(23, 4, "Jornada", 0, 0, 'L');
      $this->Cell(43, 4, $data->receptor->TipoJornada, 0, 0, 'L');
      $this->Cell(30, 4, "Periodicidad", 0, 0, 'L');
      $this->Cell(25, 4, $data->receptor->PeriodicidadPago, 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "Antigüedad", 0, 0, 'L');
      $this->Cell(36, 4, $data->receptor->Antigüedad, 0, 0, 'L');
      $this->Cell(23, 4, "Tipo Contrato", 0, 0, 'L');
      $this->Cell(43, 4, $data->receptor->TipoContrato, 0, 0, 'L');
      $this->Cell(30, 4, "Salario Diario", 0, 0, 'L');
      $this->Cell(25, 4, $data->receptor->SalarioDiarioIntegrado, 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "RFC", 0, 0, 'L');
      $this->Cell(36, 4, $receptor->Rfc, 0, 0, 'L');
      $this->Cell(23, 4, "Fecha Pago", 0, 0, 'L');
      $this->Cell(43, 4, $data->header->FechaPago, 0, 0, 'L');
      $this->Cell(30, 4, "S.B.C", 0, 0, 'L');
      $this->Cell(25, 4, $data->receptor->SalarioBaseCotApor, 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "CURP", 0, 0, 'L');
      $this->Cell(36, 4, $data->receptor->Curp, 0, 0, 'L');
      $this->Cell(23, 4, "Fecha Inicio", 0, 0, 'L');
      $this->Cell(43, 4, $data->header->FechaInicialPago, 0, 0, 'L');
      $this->Cell(30, 4, "Tipo Regimen", 0, 0, 'L');
      $this->Cell(25, 4, $data->receptor->TipoRegimen, 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "", 0, 0, 'L');
      $this->Cell(36, 4, "", 0, 0, 'L');
      $this->Cell(23, 4, "Fecha Fin", 0, 0, 'L');
      $this->Cell(43, 4, $data->header->FechaFinalPago, 0, 0, 'L');
      $this->Cell(30, 4, "", 0, 0, 'L');
      $this->Cell(25, 4, "", 0, 0, 'R');
      $this->Ln();
    }

    public function Conceptos ($data, $header) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(1);
      $this->Line(10, 125, 200, 125);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(20, 4, "CANTIDAD", 0, 0, 'C');
      $this->Cell(20, 4, "UNIDAD", 0, 0, 'C');
      $this->Cell(95, 4, "DESCRIPCION", 0, 0, 'L');
      $this->Cell(35, 4, "VALOR UNITARIO", 0, 0, 'R');
      $this->Cell(20, 4, "IMPORTE", 0, 0, 'R');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 130, 200, 130);

      foreach ($data as $value) {
        $this->Ln();
        $this->Cell(20, 4, $value->Cantidad, 0, 0, 'C');
        $this->Cell(20, 4, $value->ClaveUnidad, 0, 0, 'C');
        $this->Cell(95, 4, "{$value->ClaveProdServ} {$value->Descripcion}", 0, 0, 'L');
        $this->Cell(35, 4, $value->ValorUnitario, 0, 0, 'R');
        $this->Cell(20, 4, $value->Importe, 0, 0, 'R');
      }

      $this->Ln();
      $this->Ln();
      $this->Cell(20, 4, "", 0, 0, 'C');
      $this->Cell(20, 4, "", 0, 0, 'C');
      $this->Cell(95, 4, "", 0, 0, 'L');
      $this->Cell(35, 4, "Sub Total (Percepciones)", 0, 0, 'L');
      $this->Cell(20, 4, $header->TotalPercepciones, 0, 0, 'R');
      $this->Ln();
      $this->Cell(20, 4, "", 0, 0, 'C');
      $this->Cell(20, 4, "", 0, 0, 'C');
      $this->Cell(95, 4, "", 0, 0, 'L');
      $this->Cell(35, 4, "Deducciones Nomina", 0, 0, 'L');
      $this->Cell(20, 4, $header->TotalDeducciones, 0, 0, 'R');
      $this->Ln();
      $this->Cell(20, 4, "", 0, 0, 'C');
      $this->Cell(20, 4, "", 0, 0, 'C');
      $this->Cell(95, 4, "", 0, 0, 'L');
      $this->Cell(35, 4, "Total", 0, 0, 'L');
      $this->Cell(20, 4, ($header->TotalPercepciones - $header->TotalDeducciones), 0, 0, 'R');
      $this->Ln();
    }

    public function percep_deducc ($percepcion, $detPercepcion, $deduccion, $detDeduccion, $dias, $subsidio){
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 100, 108, 100);
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(100, 4, "Percepciones", 0, 0, 'L');
      $this->Line(10, 110, 108, 110);
      $this->Cell(100, 4, "Deducciones", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(32, 155, 255);
      $this->Cell(8, 4, "Tipo", 0, 0, 'L');
      $this->Cell(45, 4, "Percepcion", 0, 0, 'L');
      $this->Cell(12, 4, "Unidades", 0, 0, 'L');
      $this->Cell(18, 4, "Imp.Gravado", 0, 0, 'R');
      $this->Cell(16, 4, "Imp.Exento", 0, 0, 'R');
      //$this->Cell(5, 4, "", 0, 0, 'L');
      $this->Line(200, 100, 111, 100);
      $this->Cell(8, 4, "Tipo", 0, 0, 'R');
      $this->Cell(60, 4, "Deduccion", 0, 0, 'L');
      $this->Cell(12, 4, "Unidades", 0, 0, 'C');
      $this->Cell(12, 4, "Importe", 0, 0, 'R');
      //$this->Cell(10, 4, "", 0, 0, 'L');
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(200, 110, 111, 110);
      $this->SetFont('Arial','',5);
      $this->SetTextColor(5, 5, 5);
      $this->Ln();
      $this->Ln();

      $currentY = 0;
      foreach ($detPercepcion as $value) {
        $currentY = $this->GetY();
	      $this->MultiCell(6, 4, $value->TipoPercepcion, 0, 'C');
        $this->SetXY($this->GetX()+6, $currentY);
	      $this->MultiCell(48, 4, $this->reduceText($value->Concepto, 42), 0);
        $this->SetXY($this->GetX()+48+6, $currentY);
	      $this->MultiCell(8, 4, ($value->Clave == 'P001')? $dias." Dias" : '' , 0, 'R');
        $this->SetXY($this->GetX()+48+6+8, $currentY);
	      $this->MultiCell(18, 4, number_format($value->ImporteGravado, 2), 0, 'R');
        $this->SetXY($this->GetX()+48+6+8+18, $currentY);
        $this->MultiCell(18, 4, number_format($value->ImporteExento, 2), 0, 'R');
      }
      if(!empty($subsidio)){
        $currentY = $this->GetY();
	      $this->MultiCell(6, 4, "002", 0, 'C');
        $this->SetXY($this->GetX()+6, $currentY);
	      $this->MultiCell(48, 4, "SUBSIDIO AL EMPLEO", 0);
        $this->SetXY($this->GetX()+48+6, $currentY);
	      $this->MultiCell(8, 4, "P600" , 0, 'R');
        $this->SetXY($this->GetX()+48+6+8, $currentY);
	      $this->MultiCell(18, 4, "0.00", 0, 'R');
        $this->SetXY($this->GetX()+48+6+8+18, $currentY);
        $this->MultiCell(18, 4, number_format($subsidio, 2), 0, 'R');
      }

      $currentY = 109;
      foreach ($detDeduccion as $value) {
        $currentY = $currentY + 4;
        $this->SetXY($this->GetX()+48+6+8+16+16+7, $currentY);
	      $this->MultiCell(8, 4, $value->TipoDeduccion, 0, 'L');
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6, $currentY);
        $this->MultiCell(7, 4, $value->Clave, 0);
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6+7, $currentY);
        $this->MultiCell(53, 4, $this->reduceText($value->Concepto, 45), 0, 'L');
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6+7+53, $currentY);
        $this->MultiCell(10, 4, "", 0);
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6+7+53+8, $currentY);
        $this->MultiCell(16, 4, number_format($value->Importe, 2), 0, 'R');
      }

      /*$currentY = $this->GetY();
      $this->SetXY($this->GetX(), $currentY);
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      //$this->Line(10, $this->GetX(), 108, $this->GetX());*/

      $totalExc = (!empty($subsidio))? ($percepcion->TotalExento + $subsidio) : $percepcion->TotalExento;
      $this->Ln();
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(68, 4, "Total de percepciones", 0, 0, 'C');
      $this->Cell(12, 4, number_format($percepcion->TotalGravado, 2), 0, 0, 'R');
      $this->Cell(18, 4, number_format($totalExc, 2), 0, 0, 'R');
      $this->Line(200, 100, 111, 100);
      $this->Cell(81, 4, "Total de Deducciones", 0, 0, 'C');
      $this->Cell(12, 4, number_format(($deduccion->TotalOtrasDeducciones + $deduccion->TotalImpuestosRetenidos), 2), 0, 0, 'R');
    }

    public function Percepciones ($percepcion, $data) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(1);
      $this->Line(10, 160, 200, 160);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 4, "PERCEPCIONES", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 167, 200, 167);
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(32, 155, 255);
      $this->Cell(30, 4, "Tipo Percepcion", 0, 0, 'C');
      $this->Cell(30, 4, "Clave", 0, 0, 'C');
      $this->Cell(95, 4, "Descripcion", 0, 0, 'L');
      $this->Cell(35, 4, "Importe", 0, 0, 'R');
      $this->SetFont('Arial','',6);
      $this->SetTextColor(5, 5, 5);

      foreach ($data as $value) {
        $this->Ln();
        $this->Cell(30, 4, $value->TipoPercepcion, 0, 0, 'C');
        $this->Cell(30, 4, $value->Clave, 0, 0, 'C');
        $this->Cell(95, 4, $value->Concepto, 0, 0, 'L');
        $this->Cell(35, 4, ($value->ImporteGravado + $value->ImporteExento), 0, 0, 'R');
      }

      $this->Ln();
      $this->Cell(30, 4, "", 0, 0, 'C');
      $this->Cell(30, 4, "", 0, 0, 'C');
      $this->SetFont('Arial','B',7);
      $this->Cell(95, 4, "Total:", 0, 0, 'C');
      $this->SetFont('Arial','',7);
      $this->Cell(35, 4, $percepcion->TotalSueldos, 0, 0, 'R');
      $this->Ln();
    }

    public function Deducciones ($deduccion, $data) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(1);
      $this->Line(10, 196, 200, 196);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 4, "DEDUCCIONES", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 203, 200, 203);
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(32, 155, 255);
      $this->Cell(30, 4, "Tipo Deduccion", 0, 0, 'C');
      $this->Cell(30, 4, "Clave", 0, 0, 'C');
      $this->Cell(95, 4, "Descripcion", 0, 0, 'L');
      $this->Cell(35, 4, "Importe", 0, 0, 'R');
      $this->SetFont('Arial','',6);
      $this->SetTextColor(5, 5, 5);
      foreach ($data as $value) {
        $this->Ln();
        $this->Cell(30, 4, $value->TipoDeduccion, 0, 0, 'C');
        $this->Cell(30, 4, $value->Clave, 0, 0, 'C');
        $this->Cell(95, 4, $value->Concepto, 0, 0, 'L');
        $this->Cell(35, 4, $value->Importe, 0, 0, 'R');
      }

      $this->Ln();
      $this->Cell(30, 4, "", 0, 0, 'C');
      $this->Cell(30, 4, "", 0, 0, 'C');
      $this->SetFont('Arial','B',7);
      $this->Cell(95, 4, "Total:", 0, 0, 'C');
      $this->SetFont('Arial','',7);
      $this->Cell(35, 4, ($deduccion->TotalOtrasDeducciones + $deduccion->TotalImpuestosRetenidos), 0, 0, 'R');
      $this->Ln();
    }

  public function FooterNomina ($selloCFD, $selloSAT, $cadenaOriginal, $image, $UUID, $noCertificadoSAT, $FechaTimbrado) {
      $this->SetY(220); /* Inicio */
      $this->SetFont('Arial','B',6);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(20, 4, "Folio Fiscal", 0, 0, 'L');
      $this->SetFont('Arial','',6);
      $this->Cell(30, 4, $UUID, 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(25, 4, "Fecha de Timbrado", 0, 0, 'L');
      $this->SetFont('Arial','',6);
      $this->Cell(30, 4, $FechaTimbrado, 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(45, 4, "Numero de Serie del Certificado del SAT", 0, 0, 'L');
      $this->SetFont('Arial','',6);
      $this->Cell(30, 4, $noCertificadoSAT, 0, 0, 'L');
      $this->SetY(235); /* Set 20 Eje Y */
      $this->SetFont('Arial','B',6);
      $this->Cell(30, 2, "Sello Digital del CFDI", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','',6);
      $this->MultiCell(140, 2, $selloCFD, 0, 'J');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(30, 2, "Sello SAT", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','',6);
      $this->MultiCell(140, 2, $selloSAT, 0, 'J');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(30, 2, "Cadena Original del complemento de certificacion digital del SAT", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','',6);
      $this->MultiCell(140, 2, $cadenaOriginal, 0, 'J');
      $this->Ln();
      $this->Image($image, 160, 235, 35, 35);
      $this->Ln();
      $this->Cell(200, 2, "Este documento es una representacion impresa de un CFDI", 0, 0, 'C');

    }

    public function Totales ($data) {
      $total = $data->header->Total;
      $this->Ln(10);
      $this->SetFont('Arial','B',7);
      $this->Cell(152, 4, "Neto a pagar:", 0, 0, 'R');
      $this->Cell(39, 4, number_format($total, 2), 0, 0, 'R');
      $this->Ln(5);
      $this->Cell(191, 4, num2letras($total, 0, 0).' '. substr(strrchr($total, "."), 1)."/100 M. N.", 0, 0, 'R');
    }
}

/*$fileXml = file_get_contents('/Applications/XAMPP/htdocs/payroll_project/uploads/ejemplo.json');
$pdf = new FacturaPdf();
$xml = json_decode($fileXml);
$xml = $xml->comprobante;
$nomina = $xml->complemento->nomina12;
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->HeaderPay($xml);
$pdf->HeaderEmisor($xml->emisor);
$pdf->HeaderNomina($xml->receptor, $nomina);
//$pdf->Conceptos($xml->conceptos, $nomina->header);
$pdf->percep_deducc($nomina->percepcion, $nomina->detallePercepcion, $nomina->deduccion, $nomina->detalleDeduccion);
//$pdf->Deducciones($nomina->deduccion, $nomina->detalleDeduccion);
/*$pdf->HeaderNomina($xml->receptor, $nomina);
$pdf->Conceptos($xml->conceptos, $nomina->header);
$pdf->Percepciones($nomina->percepcion, $nomina->detallePercepcion);
$pdf->Deducciones($nomina->deduccion, $nomina->detalleDeduccion);
$pdf->FooterNomina();
$archivo = "/Applications/XAMPP/htdocs/payroll_project/uploads/facturas.pdf";
$pdf->Output('F', $archivo);*/
