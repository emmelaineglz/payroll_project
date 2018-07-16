<?php
$rutaFile = "uploadsFiles/1/CFDIS_SMS20188/";
$folio = 36781;
$varVar = '';
$query = "INSERT INTO
nomina (`id_nomina`, `calendario`, `tNomina`, `ejercicio`, `periodo`, `proceso`, `fecFinPeriodo`, `cliente`, `noEmpleado`, `nombre`, `puesto`, `area`, `fecAntiguedad`, `fecIngreso`, `fecBaja`, `rfc`, `sMensual`, `sDiario`, `sdi`, `diasPeriodo`, `diasSueldo`, `descansoLaborado`, `ausentismo`, `incapMaternidad`, `incapRiesgoTrabajo`, `incapEnfGral`, `vacacionesDisfrutadas`, `diaFestivo`, `primaDominical`, `sueldo`, `aguinaldo`, `complementoSueldo`, `retroactivo`, `vacacionesDisfrutadas2`, `comisiones`, `bono`, `bonoDesempeno`, `viaticos`, `faltasAusentismo`, `diaFestivoTrabajado`, `descansoLaborado2`, `primaDominical2`, `horasExtrasDobles`, `horasExtrasDoblesExento`, `horasExtrasTriples`, `apoyoFiscal`, `descDifCredInfo`, `primaVacacional`, `vacacionesFiniquito`, `primaVacacionalFiniquito`, `aguinaldoFiniquito`, `subsidioEmpleo`, `totalPercepciones`, `isr`, `imss`, `cyv`, `creditoInfonavit`, `seguroVivienda`, `descEquipo`, `incapMaternidad2`, `incapEnfGral2`, `incapRiesgoTrabajo2`, `descPagoIndebido`, `totalDeducciones`, `netoPagado`, `provCuotaFija`, `provExcedente`, `provPrestDinero`, `provGtosMedicos`, `provGuarderia`, `provInvalidezVida`, `riesgoTrabajo`, `provCesantiaVejez`, `provRetiroAfore`, `infonavitCinco`, `totalCostoSocial`, `impuestoNominaTres`, `provisionAguinaldo`, `provisionVacaciones`, `provisionPrimaVac`, `totalProvisiones`, `baseIngreso`, `porcientoHonorario`, `honorario`, `costoTotalNomina`, `iva`, `total`, `folio`, `cadenaOriginal`, `timbreUuid`, `fechaTimbre`, `selloSat`, `sello`, `fechaEmision`, `serie`, `estado`, `diasDeAntiguedad`, `baseExento`, `primaDominicalGravado`, `primaVacacionalGravada`, `descansoLaboradoGravada`, `festivosGrabada`, `primaDominicalExento`, `primaVacacionalExenta`, `descansoLaboradoExenta`, `festivosExenta`, `gratificacion`, `PensionAlimenticia`, `infonavit`, `incapacidades`, `urlXml`, `urlPdf`, `prestamos`, `campo1`, `campo2`, `campo3`, `campo4`, `campo5`, `campo6`, `campo7`, `campo8`, `campo9`, `campo10`, `campo11`, `campo12`, `campo13`, `campo14`, `campo15`, `campo16`, `campo17`, `campo18`, `campo19`, `campo20`, `campo21`, `campo22`, `campo23`, `campo24`, `campo25`, `campo26`, `campo27`, `campo28`, `campo29`, `campo30`, `campo31`, `campo32`, `campo33`, `campo34`, `campo35`, `campo36`, `campo37`, `campo38`, `campo39`, `campo40`, `campo41`, `campo42`, `campo43`, `campo44`, `campo45`, `campo46`, `campo47`, `campo48`, `campo49`, `campo50`, `campo51`, `campo52`, `campo53`, `campo54`, `campo55`, `campo56`, `Campo57`, `Campo58`, `Campo59`, `Campo60`, `campo61`, `campo62`, `campo63`, `campo64`, `campo65`, `campo66`, `campo67`, `campo68`, `campo69`, `campo70`, `campo71`, `campo72`, `campo73`, `campo74`, `campo75`, `campo76`, `campo77`, `campo78`, `campo79`, `campo80`, `curp`, `nss`, `ajusteNeto`, `ajusteTotalGravado`, `mensaje`, `urlXmlError`)
VALUES ";
if(is_dir($rutaFile)){
  if($dir = opendir($rutaFile)){
    while(($archivo = readdir($dir)) !== false){
      if($archivo != '.' && $archivo != '..') {
        $extension = strtolower(substr($archivo, -4));
				if ($extension == '.xml'){
          $data = file_get_contents("{$rutaFile}{$archivo}");
          $xml_READ = simplexml_load_string($data);
          $ns = $xml_READ->getNamespaces(true);
          $xml_READ->registerXPathNamespace('c', $ns['cfdi']);
          $comprobante = $xml_READ->xpath('//c:Comprobante');
          foreach ($comprobante as $comp) {
              $netoP = $comp['Total'];
          }
          $receptor = $xml_READ->xpath('//c:Receptor');
          foreach ($receptor as $recep) {
              $Nombre = $recep['Nombre'];
              $Rfc = $recep['Rfc'];
          }
          $xml_READ->registerXPathNamespace('n', $ns['nomina12']);
          $receptorN = $xml_READ->xpath('//n:Receptor');
          foreach ($receptorN as $recep) {
              $Curp = $recep['Curp'];
              $numEmpleado = $recep['NumEmpleado'];
              $FecIniRelLab = $recep['FechaInicioRelLaboral'];
              $nss = $recep['NumSeguridadSocial'];
              $depto = $recep['Departamento'];
          }
          $header = $xml_READ->xpath('//n:Nomina');
          foreach ($header as $head) {
              $fechaFin = $head['FechaFinalPago'];
              $numDias = $head['NumDiasPagados'];
          }

          $xml_READ->registerXPathNamespace('t', $ns['tfd']);
          $timbre = $xml_READ->xpath('//t:TimbreFiscalDigital');
          foreach ($timbre as $tfd) {
              $UUID = $tfd['UUID'];
              $FechaTimbrado = $tfd['FechaTimbrado'];
          }
          $varVar .= "({$folio},'Q','Q01',2018,8,'ORDINARIO','{$fechaFin}','ADMON Y LOGISTICA', {$numEmpleado}, '{$Nombre}', '{$depto}', '{$depto}', '{$FecIniRelLab}', '{$FecIniRelLab}', '0000-00-00', '{$Rfc}', 0, 0, 0, {$numDias}, {$numDias}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, {$netoP}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,'0','0','$UUID','$FechaTimbrado','0','0','0','0','0','0', 0, 0, 0, 0, 0 , 0, 0, 0, 0, 0, 0, 0, 0, 'http://159.89.38.133/payroll_project/uploads/1/BON150210EN4/{$archivo}', 'OK', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ,0 , 0, 0, 0, 0, 0, '', '', '0', 'TRASFERENCIA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '{$Curp}', '{$nss}', 0, 0, '', ''),";
          $folio ++;
          //rename("../uploads/2/BON150210EN4/BON150210EN4_ADMON_Y_LOGISTICA/{$UUID}.pdf", "../uploads/2/BON150210EN4/BON150210EN4_ADMON_Y_LOGISTICA/{$UUID}_{$numEmpleado}_{$fechaFin}.pdf");
          //rename("{$rutaFile}{$archivo}", "{$rutaFile}{$UUID}_{$numEmpleado}_{$fechaFin}.xml");
        }
      }
    }
    closedir($dir);
    echo $query.$varVar;
  }
}
