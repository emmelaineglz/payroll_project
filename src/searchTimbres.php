<?php
include "parserXml.php";

$Usuario='root';
$Password='20edo18';
$Servidor='localhost';
$BaseDeDatos="onenom";

$db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");


$sqlCliente = "SELECT idcliente FROM onenom.clientes WHERE idcliente = 1";
$reg = mysqli_query($db_xion, $sqlCliente);
while ($row = mysqli_fetch_array($reg)) {
	$idcliente = $row["idcliente"];
    echo "onenom_".$idcliente.".nomina<br>";
		$sqlNomina = "SELECT id_nomina, urlXml FROM onenom_".$idcliente.".nomina WHERE urlXml <> ''";
		$regNomina = mysqli_query($db_xion, $sqlNomina);
		$sum = 0;
		while ($rowNomina = mysqli_fetch_array($regNomina)) {
			$idNom = $rowNomina['id_nomina'];
			$urlXml = $rowNomina['urlXml'];
			$arrayXml = parseXML($urlXml);

			if ($arrayXml[14]) {
				$UUID = $arrayXml[14]['timbreFiscal']['UUID'];
				$RfcProvCertif = $arrayXml[14]['timbreFiscal']['RfcProvCertif'];
			} else {
				$RfcProvCertif = 'NO TIMBRE';
			}
			
			if ($RfcProvCertif == 'AAA010101AAA') {
				removeTimbre($urlXml);
				$sum ++;
				echo $idNom . "-". $sum ."-".$RfcProvCertif." RFC \n";
				die();
			}
			//$cadenaOriginalCertificada = getCadenaOriginalCertificacion($arrayXml[13]['timbreFiscal']);

			//echo "UPDATE onenom_{$idcliente}.nomina SET folio='{$Folio}', cadenaOriginal='{$cadenaOriginalCertificada}', timbreUuid='{$UUID}', fechaTimbre='{$FechaTimbrado}', selloSat='{$SelloSAT}', sello='{$SelloCFD}', fechaEmision='{$FechaEmision}', serie='{$Serie}' WHERE id_nomina={$idNom};";

			//echo "rfc: {$RfcProvCertif}";;
			
			
			/* mysqli_query($db_xion, "UPDATE onenom_{$idcliente}.nomina SET folio='{$Folio}', cadenaOriginal='{$cadenaOriginalCertificada}', timbreUuid='{$UUID}', fechaTimbre='{$FechaTimbrado}', selloSat='{$SelloSAT}', sello='{$SelloCFD}', fechaEmision='{$FechaEmision}', serie='{$Serie}' WHERE id_nomina={$idNom}");*/
		}
		echo "tTERMINO y EXISTEN: " . $sum . "REGISTROS";
}


function removeTimbre ($urlXml) {
	$doc = new DOMDocument;
	$doc->load($urlXml);
	$book = $doc->documentElement;
	var_dump($book);
	$chapter = $book->getElementsByTagName("cfdi:Comprobante")->item(0);
	var_dump($chapter);
	//$oldchapter = $book->removeChild($chapter);
	//echo $doc->saveXML();
}
//echo "TERMINO $reg2<br>";
