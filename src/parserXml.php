<?php

use Endroid\QrCode\QrCode;

//$json = file_get_contents("php://input");
/*$json = file_get_contents('../uploads/ejemplo_toPDF.json');
$ruta = '';

if($json) {
  $jsonData = json_decode($json, true);
	if($jsonData['empresa'] && $jsonData['uuid'] && $jsonData['rfc']) {
		$empresa = $jsonData['empresa'];
		$uuid = $jsonData['uuid'];
		$rfc = $jsonData['rfc'];

		$ruta = '../uploads/'.$empresa.'/'.$rfc.'/';
		$xml = $ruta.$uuid.'.xml';
		if(file_exists($xml)) {
			$jsonData = parseXML($xml, $empresa);
		}
	}
}*/
function parseXML($xmlFile) {
	$xml = simplexml_load_file($xmlFile, "SimpleXMLElement", LIBXML_NOCDATA);
	if($xml) {
		registerNamespaces($xml);

		$header = getNodeData($xml, 'Comprobante', 'header', 'c');
		$emisor = getNodeData($xml, 'Emisor', 'emisor', 'c');
		$receptor = getNodeData($xml, 'Receptor', 'receptor', 'c');
		$conceptos = getNodeData($xml, 'Concepto', 'conceptos', 'c');
		$headerNomina = getNodeData($xml, 'Nomina', 'headerNomina', 'n');
		$emisorNomina = getNodeData($xml, 'Emisor', 'emisorNomina', 'n');
		$receptorNomina = getNodeData($xml, 'Receptor', 'receptorNomina', 'n');
		$percepcionesNomina = getNodeData($xml, 'Percepciones', 'percepcion', 'n');
		$detallePercepcionesNomina = getNodeData($xml, 'Percepcion', 'detallePercepcion', 'n');
		$deduccionesNomina = getNodeData($xml, 'Deducciones', 'deduccion', 'n');
		$detalleDeduccionesNomina = getNodeData($xml, 'Deduccion', 'detalleDeduccion', 'n');
		$incapacidadesNomina = getNodeData($xml, 'Incapacidad', 'incapacidades', 'n');
		  $otrosPagosNomina = getNodeData($xml, 'OtroPago', 'otrosPagos', 'n');
		  $subsidioNomina = getNodeData($xml, 'SubsidioAlEmpleo', 'subsidioAlEmpleo', 'n');  
		$timbreFiscal = getNodeData($xml, 'TimbreFiscalDigital', 'timbreFiscal', 't');
	
	  $fileXml = [$header, $emisor, $receptor, $conceptos, $headerNomina, $emisorNomina, $receptorNomina, $percepcionesNomina, $detallePercepcionesNomina, $deduccionesNomina, $detalleDeduccionesNomina, $incapacidadesNomina, $otrosPagosNomina, $subsidioNomina, $timbreFiscal];
	  return $fileXml;
	}
	return false;
}

function getNodeData($xml, $nodeName, $nodeAlias = '', $type = '') {
	$node = [];
	$nodeData = $xml->xpath('//'.$type.':'.$nodeName);
	if($nodeData) {
		$nodeName = ($nodeAlias) ? $nodeAlias : $nodeName;
		$totalNodes = count($nodeData);
		for($i = 0; $i < $totalNodes; $i++) {
			foreach ($nodeData[$i]->attributes() as $attribute => $value) {
				if($totalNodes > 1) {
					$node[$nodeName][$i][$attribute] = (string)$value;
				} else {
					$node[$nodeName][$attribute] = (string)$value;
				}
			}
		}
	}

	return $node;
}

function registerNamespaces($xml) {
	$ns = $xml->getNamespaces(true);
	$xml->registerXPathNamespace('c', $ns['cfdi']);
	$xml->registerXPathNamespace('n', $ns['nomina12']);
	$xml->registerXPathNamespace('t', $ns['tfd']);
}

function getCadenaOriginalCertificacion($timbreFiscal) {
	$version = $timbreFiscal['Version'];
	$UUID = $timbreFiscal['UUID'];
	$FechaTimbrado = $timbreFiscal['FechaTimbrado'];
	$selloCFD = $timbreFiscal['SelloCFD'];
	$noCertificadoSAT = $timbreFiscal['NoCertificadoSAT'];
	$cadena = "||{$version}|{$UUID}|{$FechaTimbrado}|{$selloCFD}|{$noCertificadoSAT}||";

	return $cadena;
}

function getQRCode($rfcEmisor, $rfcReceptor, $total, $UUID, $saveInPath) {
	$cadena = "?re={$rfcEmisor}&rr={$rfcReceptor}&tt={$total}&id={$UUID}";
	$qrCode = new QrCode($cadena);
	$qrCode->writeFile($saveInPath.'/uploads/gQR.png');
	return $saveInPath.'/uploads/gQR.png';

}
