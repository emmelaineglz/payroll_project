<?php
include "/var/www/html/payroll_project/readExcel/vendor/autoload.php";

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Carbon\Carbon;

$filePath = '../uploads/excel/exampleObtener.xlsx';
$ws = "https://timbracfdi33.mx:1443/Timbrado.asmx?wsdl";

$reader = ReaderFactory::create(Type::XLSX); // for XLSX files
$reader->open($filePath);

foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
      var_dump($row);
      try {
        $params = array();
        /** Usuario Integrador para pruebas **/
        //$params['usuarioIntegrador'] = 'mvpNUXmQfK8=';
        /** Usuario Integrador para Productivo **/
        $params['usuarioIntegrador'] = '8E5CyvqyxsyGkK0DbKbA8g==';
        $params['rfcEmisor'] = $row[0];
        $params['folioUUID'] = $row[1];

        $client = new SoapClient($ws,$params);
        $response = $client->__soapCall('ObtieneCFDI', array('parameters' => $params));
      } catch (SoapFault $fault){
        echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
      }
      $ruta2 = "../uploads/74/{$row[1]}_{$row[2]}_{$row[3]}.xml";
      var_dump($response->ObtieneCFDIResult->anyType[3]);
      file_put_contents($ruta2, $response->ObtieneCFDIResult->anyType[3]);
    }
}

$reader->close();
