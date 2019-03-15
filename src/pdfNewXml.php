<?php
//include "/Applications/XAMPP/htdocs/payroll_project/vendor/autoload.php";
include "numberToLetter.php";

use FPDF as FPDF;
use DOMDocument as DOMDocument;
use MarkWilson\XmlToJson\XmlToJsonConverter;
use GreenCape\Xml\Converter;

class FacturaPdfXml extends FPDF {


  public function reduceText($text, $maxWidth) {
      return strlen($text) > $maxWidth ? substr($text, 0, $maxWidth)."..." : $text;
  }

  public function HeaderPay($data){
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(191, 191, 192);
			//$this->Cell(190, 5, "Comprobante Fiscal Digital por Internet", 0, 0, 'R');
      $this->Ln();
      $this->Cell(153, 5, utf8_decode("RECIBO DE NÓMINA"), 0, 0, 'R');
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
      $this->Cell(40, 4, $data['Serie']."-".$data['Folio'], 0, 0, 'L');
      $this->Cell(30, 4, $data['FormaPago'], 0, 0, 'L');
      $this->Ln();
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, "Fecha y Hora de emision", 0, 0, 'L');
      $this->Cell(45, 4, "Lugar de expedicion", 0, 0, 'L');
      $this->Ln();
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(60, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, $data['Fecha'], 0, 0, 'L');
      $this->Cell(40, 4, $data['LugarExpedicion'], 0, 0, 'L');
    }

    public function HeaderG($data) {
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
      $this->Cell(40, 4, $data->razonSocial, 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "RFC", 0, 0, 'L');
      $this->Cell(40, 4, $data->rfc, 0, 0, 'L');
      $this->Cell(25, 4, "Regimen Fiscal", 0, 0, 'L');
      $this->Cell(50, 4, "General de Ley Personas Morales", 0, 0, 'L');
      $this->Cell(25, 4, "Registro Patronal", 0, 0, 'L');
      $this->Cell(40, 4, $data->registroPatronal, 0, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "Domicilio", 0, 0, 'L');
      $this->Cell(180, 4, $data->domicilio, 0, 'L');
      $this->Ln();
      $this->Cell(25, 4, "Estado", 0, 0, 'L');
      $this->Cell(40, 4, "Ciudad de Mexico", 0, 0, 'L');
      $this->Cell(25, 4, "Codigo Postal", 0, 0, 'L');
      $this->Cell(50, 4, $data->codigoPostal, 0, 0, 'L');
      $this->Cell(25, 4, "Pais", 0, 0, 'L');
      $this->Cell(40, 4, "Mexico", 0, 0, 'L');
		}

    public function HeaderNomina ($receptor, $headerNomina, $receptorNomina) {
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
      $this->Cell(50, 4, $receptor['Nombre'], 0, 0, 'L');
      $this->Ln();
      $this->Cell(33, 4, "No Empleado", 0, 0, 'L');
      $this->Cell(36, 4, $receptorNomina['NumEmpleado'], 0, 0, 'L');
      $this->Cell(23, 4, "IMSS", 0, 0, 'L');
      $this->Cell(43, 4, $receptorNomina['NumSeguridadSocial'], 0, 0, 'L');
      $this->Cell(30, 4, "Dias", 0, 0, 'L');
      $this->Cell(25, 4, $headerNomina['NumDiasPagados'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "Riesgo Puesto", 0, 0, 'L');
      $this->Cell(36, 4, $receptorNomina['RiesgoPuesto'], 0, 0, 'L');
      $this->Cell(23, 4, "Jornada", 0, 0, 'L');
      $this->Cell(43, 4, $receptorNomina['TipoJornada'], 0, 0, 'L');
      $this->Cell(30, 4, "Periodicidad", 0, 0, 'L');
      $this->Cell(25, 4, $receptorNomina['PeriodicidadPago'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "Fecha Ingreso", 0, 0, 'L');
      $this->Cell(36, 4, $receptorNomina['FechaInicioRelLaboral'], 0, 0, 'L');
      $this->Cell(23, 4, "Tipo Contrato", 0, 0, 'L');
      $this->Cell(43, 4, $receptorNomina['TipoContrato'], 0, 0, 'L');
      $this->Cell(30, 4, "Salario Diario", 0, 0, 'L');
      $this->Cell(25, 4, $receptorNomina['SalarioDiarioIntegrado'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "RFC", 0, 0, 'L');
      $this->Cell(36, 4, $receptor['Rfc'], 0, 0, 'L');
      $this->Cell(23, 4, "Fecha Pago", 0, 0, 'L');
      $this->Cell(43, 4, $headerNomina['FechaPago'], 0, 0, 'L');
      $this->Cell(30, 4, "S.B.C", 0, 0, 'L');
      $this->Cell(25, 4, $receptorNomina['SalarioBaseCotApor'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "CURP", 0, 0, 'L');
      $this->Cell(36, 4, $receptorNomina['Curp'], 0, 0, 'L');
      $this->Cell(23, 4, "Fecha Inicio", 0, 0, 'L');
      $this->Cell(43, 4, $headerNomina['FechaInicialPago'], 0, 0, 'L');
      $this->Cell(30, 4, "Tipo Regimen", 0, 0, 'L');
      $this->Cell(25, 4, $receptorNomina['TipoRegimen'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(33, 4, "", 0, 0, 'L');
      $this->Cell(36, 4, "", 0, 0, 'L');
      $this->Cell(23, 4, "Fecha Fin", 0, 0, 'L');
      $this->Cell(43, 4, $headerNomina['FechaFinalPago'], 0, 0, 'L');
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
      $this->Cell(95, 4, utf8_decode("DESCRIPCIÓN"), 0, 0, 'L');
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
      $detPercepcion = (!empty($detPercepcion['Clave'])) ? array($detPercepcion) : $detPercepcion;
      foreach ($detPercepcion as $value) {
        $currentY = $this->GetY();
	      $this->MultiCell(6, 4, $value['TipoPercepcion'], 0, 'C');
        $this->SetXY($this->GetX()+6, $currentY);
	      $this->MultiCell(48, 4, utf8_decode($this->reduceText($value['Concepto'], 42)), 0);
        $this->SetXY($this->GetX()+48+6, $currentY);
	      $this->MultiCell(8, 4, ($value['Clave'] == 'P001')? $dias." Dias" : '' , 0, 'R');
        $this->SetXY($this->GetX()+48+6+8, $currentY);
	      $this->MultiCell(18, 4, number_format($value['ImporteGravado'], 2), 0, 'R');
        $this->SetXY($this->GetX()+48+6+8+18, $currentY);
        $this->MultiCell(18, 4, number_format($value['ImporteExento'], 2), 0, 'R');

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
      $detDeduccion = (!empty($detDeduccion['Clave'])) ? array($detDeduccion) : $detDeduccion;
      foreach ($detDeduccion as $value) {
        $currentY = $currentY + 4;
        $this->SetXY($this->GetX()+48+6+8+16+16+7, $currentY);
	      $this->MultiCell(8, 4, $value['TipoDeduccion'], 0, 'L');
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6, $currentY);
        $this->MultiCell(7, 4, $value['Clave'], 0);
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6+7, $currentY);
        $this->MultiCell(53, 4, utf8_decode($this->reduceText($value['Concepto'], 45)), 0, 'L');
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6+7+53, $currentY);
        $this->MultiCell(10, 4, "", 0);
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6+7+53+8, $currentY);
        $this->MultiCell(16, 4, number_format($value['Importe'], 2), 0, 'R');
      }

      /*$currentY = $this->GetY();
      $this->SetXY($this->GetX(), $currentY);
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      //$this->Line(10, $this->GetX(), 108, $this->GetX());*/
  
      $totalExc = (!empty($subsidio))? ($percepcion['TotalExento'] + $subsidio) : $percepcion['TotalExento'];
      $this->Ln();
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(68, 4, "Total de percepciones", 0, 0, 'C');
      $this->Cell(12, 4, number_format($percepcion['TotalGravado'], 2), 0, 0, 'R');
      $this->Cell(18, 4, number_format($totalExc, 2), 0, 0, 'R');
      $this->Line(200, 100, 111, 100);
      $this->Cell(81, 4, "Total de Deducciones", 0, 0, 'C');
      $TIR = (!empty($deduccion['TotalImpuestosRetenidos'])) ? $deduccion['TotalImpuestosRetenidos'] : 0;
      $this->Cell(12, 4, number_format(($deduccion['TotalOtrasDeducciones'] + $TIR), 2), 0, 0, 'R');
    }
//$selloCFD, $selloSAT, $cadenaOriginal, $image, $UUID, $noCertificadoSAT, $FechaTimbrado
  public function FooterNomina ($data, $cadenaOriginal, $image) {
      $this->SetY(220); /* Inicio */
      $this->SetFont('Arial','B',6);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(20, 4, "Folio Fiscal", 0, 0, 'L');
      $this->SetFont('Arial','',6);
      $this->Cell(30, 4, $data['UUID'], 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(25, 4, "Fecha de Timbrado", 0, 0, 'L');
      $this->SetFont('Arial','',6);
      $this->Cell(30, 4, $data['FechaTimbrado'], 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(45, 4, "Numero de Serie del Certificado del SAT", 0, 0, 'L');
      $this->SetFont('Arial','',6);
      $this->Cell(30, 4, $data['NoCertificadoSAT'], 0, 0, 'L');
      $this->SetY(235); /* Set 20 Eje Y */
      $this->SetFont('Arial','B',6);
      $this->Cell(30, 2, "Sello Digital del CFDI", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','',6);
      $this->MultiCell(140, 2, $data['SelloCFD'], 0, 'J');
      $this->Ln();
      $this->SetFont('Arial','B',6);
      $this->Cell(30, 2, "Sello SAT", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','',6);
      $this->MultiCell(140, 2, $data['SelloSAT'], 0, 'J');
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
      $total = $data['Total'];
      $this->Ln(10);
      $this->SetFont('Arial','B',7);
      $this->Cell(152, 4, "Neto a pagar:", 0, 0, 'R');
      $this->Cell(39, 4, number_format($total, 2), 0, 0, 'R');
      $this->Ln(5);
      $this->Cell(191, 4, num2letras($total, 0, 0).' '. substr(strrchr($total, "."), 1)."/100 M. N.", 0, 0, 'R');
    }

    public function BlockSubsidio ($subsidio, $causado, $isr) {
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "CONCEPTO", 0, 0, 'L');
      $this->Cell(30, 4, "IMPORTE", 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln(6);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "Subsidio correspondido", 0, 0, 'L');
      $this->Cell(30, 4, "$".number_format($causado, 2), 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln(4);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "Subsidio pagado", 0, 0, 'L');
      $this->Cell(30, 4, "$".number_format($subsidio, 2), 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln(4);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "ISR determinado", 0, 0, 'L');
      $this->Cell(30, 4, "$0.00", 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln(4);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "ISR retenido", 0, 0, 'L');
      $this->Cell(30, 4, "$".number_format($isr, 2), 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln();
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
