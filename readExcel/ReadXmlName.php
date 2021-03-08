<?php
$rutaFile = "uploadsFiles/1/CFDIS_SMS20181/";

if(is_dir($rutaFile)){
  if($dir = opendir($rutaFile)){
    while(($archivo = readdir($dir)) !== false){
      if($archivo != '.' && $archivo != '..') {
        $extension = strtolower(substr($archivo, -4));
				if ($extension == '.xml'){
          $data = file_get_contents("{$rutaFile}{$archivo}");
          $xml_READ = simplexml_load_string($data);
          $ns = $xml_READ->getNamespaces(true);
          $xml_READ->registerXPathNamespace('n', $ns['nomina12']);
          $receptor = $xml_READ->xpath('//n:Receptor');
          foreach ($receptor as $recep) {
              $numEmpleado = $recep['NumEmpleado'];
          }

          $header = $xml_READ->xpath('//n:Nomina');
          foreach ($header as $head) {
              $fechaFin = $head['FechaFinalPago'];
          }

          $xml_READ->registerXPathNamespace('t', $ns['tfd']);
          $timbre = $xml_READ->xpath('//t:TimbreFiscalDigital');
          foreach ($timbre as $tfd) {
              $UUID = $tfd['UUID'];
          }

          //rename("../uploads/2/BON150210EN4/BON150210EN4_ADMON_Y_LOGISTICA/{$UUID}.pdf", "../uploads/2/BON150210EN4/BON150210EN4_ADMON_Y_LOGISTICA/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf");
          rename("{$rutaFile}{$archivo}", "{$rutaFile}{$UUID}_{$numEmpleado}_{$fechaFin}.xml");
        }
      }
    }
    closedir($dir);
  }
}
