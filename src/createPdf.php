<?php
include "../vendor/autoload.php";
include "parserXml.php";
include "pdfNewXml.php";

$rutaFile = "../uploads/6/MIN120828HI0/";

if(is_dir($rutaFile)){
  if($dir = opendir($rutaFile)){
    while(($archivo = readdir($dir)) !== false){
      if($archivo != '.' && $archivo != '..') {
        $extension = strtolower(substr($archivo, -4));
				if ($extension == '.xml'){
          //$archivo = "0c2f4940-cb7d-4e86-90e2-d6febd37edad_22131_2018-05-25.xml";
          $data = "{$rutaFile}{$archivo}";

          $arrayXml = parseXML($data);
          $UUID = $arrayXml[13]['timbreFiscal']['UUID'];
          $numEmpleado = $arrayXml[6]['receptorNomina']['NumEmpleado'];
          $fechaFin = $arrayXml[4]['headerNomina']['FechaFinalPago'];

          $cadenaOriginal = file_get_contents($rutaFile."cadenaOriginal_".$UUID.".txt");
          $codigoQR = $rutaFile."codigoQr_".$UUID.".jpg";

          $pdf = new FacturaPdfXml();
        /*  $xml = $xml->comprobante;
          $nomina = $xml->complemento->nomina12;
          $subsidio = (!empty($nomina->OtrosPagos))? $nomina->OtrosPagos[0]->subsidio->SubsidioCausado : '';*/
          $subsidio = (!empty($arrayXml[12]['otrosPagos'])) ? $arrayXml[12]['otrosPagos']['Importe'] : '';
          $pdf->AddPage();
          $pdf->SetFont('Arial','B',16);
          $pdf->HeaderPay($arrayXml[0]['header']);
          $pdf->HeaderMin();
          $pdf->HeaderNomina($arrayXml[2]['receptor'], $arrayXml[4]['headerNomina'], $arrayXml[6]['receptorNomina']);
          $pdf->percep_deducc($arrayXml[7]['percepcion'], $arrayXml[8]['detallePercepcion'], $arrayXml[9]['deduccion'], $arrayXml[10]['detalleDeduccion'], $arrayXml[4]['headerNomina']['NumDiasPagados'], $subsidio);
          $pdf->Totales($arrayXml[0]['header']);
          $pdf->FooterNomina($arrayXml[13]['timbreFiscal'], $cadenaOriginal, $codigoQR);
          $newName = "{$rutaFile}/NEWPDF/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf";
          $pdf->Output('F', $newName);
          //die();
        }
      }
    }
    closedir($dir);
  }
}
