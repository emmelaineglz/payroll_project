<?php
//include "/Applications/XAMPP/htdocs/payroll_project/vendor/autoload.php";

use FPDF as FPDF;
use DOMDocument as DOMDocument;
use MarkWilson\XmlToJson\XmlToJsonConverter;
use GreenCape\Xml\Converter;

class FacturaPdf extends FPDF {

  public function HeaderPay($data, $UUID, $noCertificadoSAT, $FechaTimbrado){
      $nomina = $data->complemento->nomina12;
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(1);
      $this->Line(10, 15, 130, 15);
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(191, 191, 192);
			$this->Cell(190, 5, "Comprobante Fiscal Digital por Internet", 0, 0, 'R');
      $this->Ln();
      $this->Cell(190, 5, "Recibo de Nomina", 0, 0, 'R');
      $this->Ln();
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(190, 4, $data->emisor->Nombre, 0, 0, 'L');
      $this->Ln();
      $this->Cell(40, 4, $data->emisor->Rfc, 0, 0, 'L');
      $this->SetTextColor(173, 173, 173);
      $this->SetFont('Arial','',7);
      $this->Cell(99, 4, "Folio Fiscal", 0, 0, 'R');
      $this->Ln();
      $this->SetTextColor(5, 5, 5);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(175, 4, $UUID, 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(173, 173, 173);
      $this->Cell(125, 4, "REGIMEN FISCAL:", 0, 0, 'L');
      $this->Cell(120, 4, "No. de Serie del Cert. del SAT", 0, 0, 'L');
      $this->Ln();
      $this->Cell(125, 4, $data->emisor->RegimenFiscal, 0, 0, 'L');
      $this->SetTextColor(5, 5, 5);
      $this->Cell(117, 4, $noCertificadoSAT, 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(173, 173, 173);
      $this->Cell(125, 4, "REGISTRO PATRONAL:", 0, 0, 'L');
      $this->Cell(100, 4, "Fecha y Hora de Certificacion", 0, 0, 'L');
      $this->Ln();
      $this->Cell(125, 4, $nomina->emisor->RegistroPatronal, 0, 0, 'L');
      $this->SetTextColor(5, 5, 5);
      $this->Cell(100, 4, $FechaTimbrado, 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(173, 173, 173);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(100, 4, "Fecha y Hora de emision", 0, 0, 'L');
      $this->Ln();
      $this->Cell(125, 4, "Lugar de expedicion: {$data->header->LugarExpedicion}", 0, 0, 'L');
      $this->SetTextColor(5, 5, 5);
      $this->Cell(100, 4, $data->header->Fecha, 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(173, 173, 173);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(100, 4, "No. Certificado", 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(5, 5, 5);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(117, 4, $data->header->NoCertificado, 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(173, 173, 173);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(100, 4, "Metodo de Pago", 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(5, 5, 5);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(100, 4, $data->header->MetodoPago, 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(173, 173, 173);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(100, 4, "Forma de Pago", 0, 0, 'L');
      $this->Ln();
      $this->SetTextColor(5, 5, 5);
      $this->Cell(125, 4, "", 0, 0, 'L');
      $this->Cell(100, 4, $data->header->FormaPago, 0, 0, 'L');
      $this->Ln();
		}

    public function HeaderNomina ($receptor, $data) {
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(1);
      $this->Line(10, 85, 200, 85);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',9);
      $this->SetTextColor(5, 5, 5);
      $this->Cell(30, 2, "EMPLEADO", 0, 0, 'L');
      $this->Cell(117, 2, $receptor->Nombre, 0, 0, 'L');
      $this->Ln();
      $this->SetDrawColor(26, 84, 251);
      $this->SetLineWidth(0.5);
      $this->Line(10, 92, 200, 92);
      $this->Ln();
      $this->Ln();
      $this->SetFont('Arial','B',7);
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
      $this->Cell(25, 4, $data->receptor->SalarioDiarioIntegrado, 0, 0, 'R');
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

    public function FooterNomina ($selloCFD, $selloSAT, $cadena_original, $image) {
      $this->SetY(235); /* Inicio */
      $this->SetFont('Arial','',5);
      $this->SetTextColor(5, 5, 5);
      $this->MultiCell(190, 2, "El trabajador recibe de la empresa que se cita en el encabezado, la cantidad anteriormente descrita por lo que con este Comprobante Fiscal Digital por Internet se dan por pagadas y aceptadas todas y cada una de las prestaciones que se generaron durante este periodo y anteriores, estando de acuerdo con los descuentos aplicables, por lo que no se adeuda prestacion o cantidad alguna por cualquier otro concepto, otorgando el trabajador su consentimiento y no se  reserva accion o derecho de ejercitar en contra de la empresa a que se hace referencia.", 0, 'J');
      $this->SetY(245); /* Set 20 Eje Y */
      $this->Cell(30, 2, "Sello Digital del CFDI", 0, 0, 'L');
      $this->Ln();
      $this->MultiCell(140, 2, "$selloCFD", 0, 'J');
      $this->Ln();
      $this->Cell(30, 2, "Sello SAT", 0, 0, 'L');
      $this->Ln();
      $this->MultiCell(140, 2, $selloSAT, 0, 'J');
      $this->Ln();
      $this->Cell(30, 2, "Cadena Original del complemento de certificacion digital del SAT", 0, 0, 'L');
      $this->Ln();
      $this->MultiCell(140, 2, $cadena_original, 0, 'J');
      $this->Ln();
      $this->Image($image, 160, 245, 35, 35);
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
$pdf->HeaderNomina($xml->receptor, $nomina);
$pdf->Conceptos($xml->conceptos, $nomina->header);
$pdf->Percepciones($nomina->percepcion, $nomina->detallePercepcion);
$pdf->Deducciones($nomina->deduccion, $nomina->detalleDeduccion);
//$pdf->FooterNomina();
$archivo = "/Applications/XAMPP/htdocs/payroll_project/uploads/facturas.pdf";
$pdf->Output('F', $archivo);*/
