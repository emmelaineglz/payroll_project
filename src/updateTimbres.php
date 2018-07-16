<?php
include "parserXml.php";

$Usuario='root';
$Password='05asl13';
$Servidor='127.0.0.1';
$BaseDeDatos="onenom";

$db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");


$sqlCliente = "SELECT idcliente FROM onenom.clientes";
$reg = mysqli_query($db_xion, $sqlCliente);
while ($row = mysqli_fetch_array($reg)) {
	$idcliente = $row["idcliente"];
    echo "onenom_".$idcliente.".nomina<br>";
		$sqlNomina = "SELECT id_nomina, urlXml FROM onenom_".$idcliente.".nomina WHERE urlXml <> ''";
		$regNomina = mysqli_query($db_xion, $sqlNomina);
		while ($rowNomina = mysqli_fetch_array($regNomina)) {
			$idNom = $rowNomina['id_nomina'];
			$urlXml = $rowNomina['urlXml'];
			$arrayXml = parseXML($urlXml);
			$UUID = $arrayXml[13]['timbreFiscal']['UUID'];
			$FechaTimbrado = $arrayXml[13]['timbreFiscal']['FechaTimbrado'];
			$NoCertificadoSAT = $arrayXml[13]['timbreFiscal']['NoCertificadoSAT'];
			$SelloCFD = $arrayXml[13]['timbreFiscal']['SelloCFD'];
			$SelloSAT = $arrayXml[13]['timbreFiscal']['SelloSAT'];
			$FechaEmision = $arrayXml[0]['header']['Fecha'];
			$Serie = $arrayXml[0]['header']['Serie'];
			$Folio = $arrayXml[0]['header']['Folio'];
			$cadenaOriginalCertificada = getCadenaOriginalCertificacion($arrayXml[13]['timbreFiscal']);

			echo "UPDATE onenom_{$idcliente}.nomina SET folio='{$Folio}', cadenaOriginal='{$cadenaOriginalCertificada}', timbreUuid='{$UUID}', fechaTimbre='{$FechaTimbrado}', selloSat='{$SelloSAT}', sello='{$SelloCFD}', fechaEmision='{$FechaEmision}', serie='{$Serie}' WHERE id_nomina={$idNom};";

			mysqli_query($db_xion, "UPDATE onenom_{$idcliente}.nomina SET folio='{$Folio}', cadenaOriginal='{$cadenaOriginalCertificada}', timbreUuid='{$UUID}', fechaTimbre='{$FechaTimbrado}', selloSat='{$SelloSAT}', sello='{$SelloCFD}', fechaEmision='{$FechaEmision}', serie='{$Serie}' WHERE id_nomina={$idNom}");
		}
}
//echo "TERMINO $reg2<br>";
