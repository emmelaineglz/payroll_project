<?php
include "vendor/autoload.php";

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Carbon\Carbon;

$filePath = '../uploads/excel/exampleCancela.xlsx';
$ws = "https://cfdi33-pruebas.buzoncfdi.mx:1443/Timbrado.asmx?wsdl";

$reader = ReaderFactory::create(Type::XLSX); // for XLSX files
$reader->open($filePath);

foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
      var_dump($row);
      try {
        $params = array();
        /** Usuario Integrador para pruebas **/
        $params['usuarioIntegrador'] = 'mvpNUXmQfK8=';
        /** Usuario Integrador para Productivo **/
        //$params['usuarioIntegrador'] = '8E5CyvqyxsyGkK0DbKbA8g==';
        $params['rfcEmisor'] = $row[0];
        $params['folioUUID'] = $row[1];

        $client = new SoapClient($ws,$params);
        $response = $client->__soapCall('CancelaCFDIAck', array('parameters' => $params));
      } catch (SoapFault $fault){
        echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
      }
      var_dump($response);
    }
}

$reader->close();
