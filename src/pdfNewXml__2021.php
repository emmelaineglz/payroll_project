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
      $this->SetFont('Arial','B',10);
      $this->SetFillColor(26, 84, 251);
			//$this->Cell(190, 5, "Comprobante Fiscal Digital por Internet", 0, 0, 'R');
      //$this->Ln();
      $this->Cell(185, 5, utf8_decode("RECIBO CFDI DE NÓMINA"), 0, 0, 'R');
      $this->Ln(6);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(156, 4, "Forma de Pago:", 0, 0, 'R');
      $this->Cell(30, 4, $data['FormaPago'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(156, 4, utf8_decode("Lugar de expedición:"), 0, 0, 'R');
      $this->Cell(30, 4, $data['LugarExpedicion'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(156, 4, utf8_decode("Fecha y Hora de emisión:"), 0, 0, 'R');
      $this->Cell(30, 4, $data['Fecha'], 0, 0, 'R');
      $this->Ln();
    }

    public function HeaderG($data, $regPatronal = '') {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 34, 200, 34);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 2, "Datos del Emisor", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 40, 200, 40);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->Cell(31, 4, utf8_decode("Razón Social:"), 0, 0, 'L');
      $this->Cell(80, 4, (!$data[razonSocial]) ? $data[nombreFiscal] : $data[razonSocial], 0, 0, 'L');
      $this->Ln();
      $this->Cell(31, 4, "RFC:", 0, 0, 'L');
      $this->Cell(38, 4, $data[rfc], 0, 0, 'L');
      $this->Cell(29, 4, "Registro Patronal:", 0, 0, 'L');
      $this->Cell(40, 4, $regPatronal, 0, 0, 'L');
      $this->Ln();
      $this->Cell(31, 4, "Domicilio:", 0, 0, 'L');
      $this->Cell(180, 4, $data[domicilio], 0, 'L');
      $this->Ln();
      $this->Cell(31, 4, "Codigo Postal:", 0, 0, 'L');
      $this->Cell(38, 4, (!$data[codigoPostal]) ? $data[cp] : $data[codigoPostal], 0, 0, 'L');
      $this->Cell(29, 4, "Pais:", 0, 0, 'L');
      $this->Cell(20, 4, utf8_decode("México"), 0, 0, 'L');
      $this->Cell(30, 4, utf8_decode("Régimen Fiscal:"), 0, 0, 'L');
      $this->Cell(20, 4, ($data[regimenFiscal] === '601') ? "General de Ley Personas Morales" : utf8_decode("Personas Físicas con Actividades"), 0, 0, 'L');
      $this->Cell(25, 4, "", 0, 0, 'L');
      $this->Cell(40, 4, "", 0, 0, 'L');
    }
    
    public function riesgoPuesto ($riesgo) {
      $array = ['Clase I', 'Clase II', 'Clase III', 'Clase IV', 'Clase V'];
      return $array[$riesgo-1];
    }

    public function tipoJornada ($jornada) {
      $array = ['01'=>'Diurna', '02'=>'Nocturna', '03'=>'Mixta', '04'=>'Por hora', '05'=>'Reducida', '06'=>'Continuada', '07'=>'Partida', '08'=>'Por turnos', '99'=>'Otra jornada' ];
      return $array[$jornada];
    }

    public function periodicidad ($perio) {
      $array = ['01'=>'Diario', '02'=>'Semanal', '03'=>'Catorcenal', '04'=>'Quincenal', '05'=>'Mensual', '06'=>'Bimestral', '07'=>'Unidad obra', '08'=>'Comisión', '09'=>'Precio alzado', '99'=>'Otra periodicidad' ];
      return $array[$perio];
    }

    public function tipoContrato ($contrato) {
      $array = ['01'=>'Por tiempo indeterminado', '02'=>'Para obra determinada', '03'=>'Por tiempo determinado', '04'=>'Por temporada', '05'=>'Sujeto a prueba', '06'=>'Con capacitación inicial', '07'=>'Por pago de hora laborada', '08'=>'Por comisión laboral', '09'=>'No existe relación de trabajo', '10'=>'Jubilación, pensión, retiro.', '99'=>'Otra contrato' ];
      return $array[$contrato];
    }

    public function tipoRegimen ($regimen) {
      $array = ['02'=>'Sueldos', '03'=>'Jubilados', '04'=>'Pensionados', '05'=>'Asimilados Miembros Sociedades Cooperativas Produccion', '06'=>'Asimilados Integrantes Sociedades Asociaciones Civiles', '07'=>'Asimilados Miembros consejos', '08'=>'Asimilados comisionistas', '09'=>'Asimilados H', '10'=>'Asimilados acciones', '11'=>'Asimilados O', '99'=>'Otro régimen' ];
      return $array[$regimen];
    }

    public function HeaderNomina ($receptor, $headerNomina, $receptorNomina) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 61, 200, 61);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 4, "Datos del Receptor", 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 67, 200, 67);
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->cell(31, 4, "Nombre:", 0, 0, 'L');
      $this->Cell(50, 4, utf8_decode($receptor['Nombre']), 0, 0, 'L');
      $this->Cell(28, 4, " ", 0, 0, 'R');
      $this->Cell(25, 4, $receptorNomina['Puesto'], 0, 0, 'L');
      $this->Ln();
      $this->Cell(31, 4, "No Empleado:", 0, 0, 'L');
      $this->Cell(50, 4, $receptorNomina['NumEmpleado'], 0, 0, 'L');
      $this->Cell(36, 4, "Departamento:", 0, 0, 'R');
      $this->Cell(25, 4, $receptorNomina['Departamento'], 0, 0, 'L');
      $this->Ln(5);
      $this->Cell(31, 4, "RFC:", 0, 0, 'L');
      $this->Cell(38, 4, $receptor['Rfc'], 0, 0, 'L');
      $this->Cell(29, 4, "Tipo contrato:", 0, 0, 'L');
      $this->Cell(40, 4, $this->tipoContrato($receptorNomina['TipoContrato']), 0, 0, 'L');
      $this->Cell(30, 4, utf8_decode("Dias pagados:"), 0, 0, 'L');
      $this->Cell(20, 4, $headerNomina['NumDiasPagados'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(31, 4, "CURP:", 0, 0, 'L');
      $this->Cell(38, 4, $receptorNomina['Curp'], 0, 0, 'L');
      $this->Cell(29, 4, "Tipo de jornada:", 0, 0, 'L');
      $this->Cell(40, 4, $this->tipoJornada($receptorNomina['TipoJornada']), 0, 0, 'L');
      $this->Cell(30, 4, "Periodicidad de pago:", 0, 0, 'L');
      $this->Cell(20, 4, utf8_decode($this->periodicidad($receptorNomina['PeriodicidadPago'])), 0, 0, 'R');
      $this->Ln();
      $this->Cell(31, 4, "NSS:", 0, 0, 'L');
      $this->Cell(38, 4, $receptorNomina['NumSeguridadSocial'], 0, 0, 'L');
      $this->Cell(29, 4, "Fecha inicial de pago:", 0, 0, 'L');
      $this->Cell(40, 4, $headerNomina['FechaInicialPago'], 0, 0, 'L');
      $this->Cell(30, 4, utf8_decode("Régimen de contratación:"), 0, 0, 'L');
      $this->Cell(20, 4, utf8_decode($this->tipoRegimen($receptorNomina['TipoRegimen'])), 0, 0, 'R');
      $this->Ln();
      $this->Cell(31, 4, "Riesgo de puesto:", 0, 0, 'L');
      $this->Cell(38, 4, $this->riesgoPuesto($receptorNomina['RiesgoPuesto']), 0, 0, 'L');
      $this->Cell(29, 4, "Fecha final de pago:", 0, 0, 'L');
      $this->Cell(40, 4, $headerNomina['FechaFinalPago'], 0, 0, 'L');
      $this->Cell(30, 4, "Salario Diario:", 0, 0, 'L');
      $this->Cell(20, 4, $receptorNomina['SalarioDiarioIntegrado'], 0, 0, 'R');
      $this->Ln();
      $this->Cell(31, 4, utf8_decode("Inicio relación laboral:"), 0, 0, 'L');
      $this->Cell(38, 4, $receptorNomina['FechaInicioRelLaboral'], 0, 0, 'L');
      $this->Cell(29, 4, "Fecha de pago:", 0, 0, 'L');
      $this->Cell(40, 4, $headerNomina['FechaPago'], 0, 0, 'L');
      $this->Cell(30, 4, "Salario Base:", 0, 0, 'L');
      $this->Cell(20, 4, $receptorNomina['SalarioBaseCotApor'], 0, 0, 'R');
      $this->Ln();
    }

    public function Conceptos ($data, $header) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(1);
      $this->Line(10, 126, 200, 126);
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
      $this->Line(10, 131, 200, 131);

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

    public function percep_deducc ($percepcion, $detPercepcion, $deduccion, $detDeduccion, $dias, $subsidio, $oPrestaciones){
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 102, 108, 102);
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(100, 4, "Percepciones", 0, 0, 'L');
      $this->Line(10, 112, 108, 112);
      $this->Cell(100, 4, "Deducciones", 0, 0, 'L');
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(32, 155, 255);
      $this->Cell(8, 4, "Tipo", 0, 0, 'L');
      $this->Cell(45, 4, "Percepcion", 0, 0, 'L');
      $this->Cell(12, 4, "        ", 0, 0, 'L');
      $this->Cell(18, 4, "Imp.Gravado", 0, 0, 'R');
      $this->Cell(16, 4, "Imp.Exento", 0, 0, 'R');
      //$this->Cell(5, 4, "", 0, 0, 'L');
      $this->Line(200, 102, 111, 102);
      $this->Cell(8, 4, "Tipo", 0, 0, 'R');
      $this->Cell(60, 4, "Deduccion", 0, 0, 'L');
      $this->Cell(12, 4, "        ", 0, 0, 'C');
      $this->Cell(12, 4, "Importe", 0, 0, 'R');
      //$this->Cell(10, 4, "", 0, 0, 'L');
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(200, 112, 111, 112);
      $this->SetFont('Arial','',6);
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
	      $this->MultiCell(8, 4, ($value['Clave'] == 'P001')?"       " : '' , 0, 'R');
        $this->SetXY($this->GetX()+48+6+8, $currentY);
	      $this->MultiCell(18, 4, number_format($value['ImporteGravado'], 2), 0, 'R');
        $this->SetXY($this->GetX()+48+6+8+18, $currentY);
        $this->MultiCell(18, 4, number_format($value['ImporteExento'], 2), 0, 'R');

      }
      if(!empty($subsidio) && $subsidio > 0){
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

      if(!empty($oPrestaciones) && $oPrestaciones > 0){
        $currentY = $this->GetY();
        $this->MultiCell(6, 4, "999", 0, 'C');
        $this->SetXY($this->GetX()+6, $currentY);
        $this->MultiCell(48, 4, "OTRAS PRESTACIONES", 0);
        $this->SetXY($this->GetX()+48+6, $currentY);
        $this->MultiCell(8, 4, "P056" , 0, 'R');
        $this->SetXY($this->GetX()+48+6+8, $currentY);
        $this->MultiCell(18, 4, "0.00", 0, 'R');
        $this->SetXY($this->GetX()+48+6+8+18, $currentY);
        $this->MultiCell(18, 4, number_format($oPrestaciones, 2), 0, 'R');
      }

      $currentY = 109;
      $detDeduccion = (!empty($detDeduccion['Clave'])) ? array($detDeduccion) : $detDeduccion;
      foreach ($detDeduccion as $value) {
        $currentY = $currentY + 4;
        $this->SetXY($this->GetX()+48+6+8+16+16+7, $currentY);
	      $this->MultiCell(8, 4, $value['TipoDeduccion'], 0, 'L');
        $this->SetXY($this->GetX()+48+6+8+16+16+7+6, $currentY);
        $this->MultiCell(7, 4, "", 0);
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
  
      $totalExc = (!empty($subsidio))? ($percepcion['TotalExento'] + $subsidio + $oPrestaciones) : $percepcion['TotalExento'];
      $this->Ln(10);
      $this->Ln(10);
      $this->Ln(10);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(68, 4, "TOTAL DE PERCEPCIONES:", 0, 0, 'C');
      $this->Cell(12, 4, number_format($percepcion['TotalGravado'], 2), 0, 0, 'R');
      $this->Cell(18, 4, number_format($totalExc, 2), 0, 0, 'R');
      //$this->Line(200, 160, 111, 160);
      $this->Cell(81, 4, "TOTAL DE DEDUCCIONES:", 0, 0, 'C');
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
      //$this->Line(200, 160, 111, 160);
      $this->SetFont('Arial','B',8);
      $this->Cell(152, 4, "NETO PAGADO:", 0, 0, 'R');
      $this->Cell(39, 4, "$ ".number_format($total, 2), 0, 0, 'R');
      $this->Ln(5);
      $this->Cell(191, 4, "( ".num2letras($total, 0, 0).' '. substr(strrchr($total, "."), 1)."/100 M. N. )", 0, 0, 'R');
      //$this->Line(200, 171, 111, 171);
    }

    public function BlockSubsidio ($subsidio, $causado, $isr) {
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "Subsidio causado:", 0, 0, 'L');
      $this->Cell(30, 4, "$ ".number_format($causado, 2), 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln(4);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "Subsidio al Empleo:", 0, 0, 'L');
      $this->Cell(30, 4, "$ ".number_format($subsidio, 2), 0, 0, 'R');
      $this->Cell(100, 4, "", 0, 0, 'L');
      $this->Ln(4);
      $this->Cell(10, 4, "", 0, 0, 'L');
      $this->Cell(30, 4, "ISR:", 0, 0, 'L');
      $this->Cell(30, 4, "$ ".number_format($isr, 2), 0, 0, 'R');
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
