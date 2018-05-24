<?php
include "vendor/autoload.php";

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Carbon\Carbon;

$filePath = '../uploads/excel/TIMBRA_ITALIKA_VIRGIN_T.xlsx';

$reader = ReaderFactory::create(Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files
$TotrosPagos = false;

$reader->open($filePath);

foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
      $header = [
        "Serie" => (string)$row[0],
        "Folio" => (string)$row[1],
        "Fecha" => DateToday(),
        "FormaPago" => (string)$row[8],
        "SubTotal" => (string)round($row[73],2),
        "Descuento" => (string)round($row[91],2),
        "Moneda" => $row[10],
        "Total" => (string)round($row[92],2),
        "TipoDeComprobante" => $row[6],
        "MetodoPago" => $row[9],
        "LugarExpedicion" => (string)$row[11]
      ];
      $emisor = [
        "Rfc" => $row[2],
        "Nombre" => $row[3],
        "RegimenFiscal" => (string)$row[5]
      ];
      $receptor = [
        "Rfc" => $row[19],
        "Nombre" => $row[27],
        "UsoCFDI" => $row[7]
      ];
      $conceptos = [
        "ClaveProdServ" => (string)$row[12],
        "Cantidad" => (string)$row[13],
        "ClaveUnidad" => (string)$row[14],
        "Descripcion" => $row[15],
        "ValorUnitario" => (string)round($row[73],2),
        "Importe" => (string)round(($row[13] * $row[73]),2),
        "Descuento" => (string)round($row[91],2)
      ];

      $nomina12_header = [
          "xmlns:nomina12" =>  (string)"http://www.sat.gob.mx/nomina12",
          "Version" => "1.2",
          "TipoNomina" => "O",
          "FechaPago" => $row[38]->format('Y-m-d'),
          "FechaInicialPago" => $row[39]->format('Y-m-d'),
          "FechaFinalPago" => $row[40]->format('Y-m-d'),
          "NumDiasPagados" => (string)$row[41],
          "TotalPercepciones" => (!empty($row[72]))? (string)round(($row[73] - $row[72]),2) : (string)round($row[73],2),
          "TotalDeducciones" => (string)round($row[91],2),
          "xsi:schemaLocation" => (string)"http://www.sat.gob.mx/nomina12 http://www.sat.gob.mx/informacion_fiscal/factura_electronica/Documents/Complementoscfdi/nomina12.xsd"
      ];
      if(!empty($row[72])){
        $nomina12_header["TotalOtrosPagos"] = (string)round($row[72], 2);
      }
      $nomina12_emisor = [
        "RegistroPatronal" => (string)$row[4],
        "RfcPatronOrigen" => (string)$row[2]
      ];
      $nomina12_receptor = [
        "Curp" => (string)$row[17],
        "FechaInicioRelLaboral" => $row[21]->format('Y-m-d'),
        "Antigüedad" => calcAntiguedad($row[39], $row[21]),
        "TipoContrato" => "0$row[22]",
        "TipoJornada" => "0$row[24]",
        "TipoRegimen" => "0$row[25]",
        "NumEmpleado" => (string)$row[26],
        "Departamento" => (string)$row[28],
        "PeriodicidadPago" => (string)$row[31],
        "Sindicalizado" => (string)$row[23],
        "SalarioBaseCotApor" => (string)$row[34],
        "CuentaBancaria" => (string)$row[33],
        "Banco" => (string)$row[32],
        "ClaveEntFed" => (string)$row[36],
        "NumSeguridadSocial" => (string)$row[18],
        "RiesgoPuesto" => "1",
        "SalarioDiarioIntegrado" => (string)$row[35]
      ];
      $nomina12_percepcion = [
        "TotalSueldos" => (string)$row[69],
        "TotalGravado" => (string)$row[70],
        "TotalExento" => (string)$row[71]
      ];

      $nomina12_detallePercepcion = [];
      if(!empty($row[43])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P001",
          "Concepto" => "SUELDOS, SALARIOS  RAYAS Y JORNALES",
          "ImporteGravado" => (string)round($row[43],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[44])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P002",
          "Concepto" => "SUELDOS, SALARIOS  RAYAS Y JORNALES",
          "ImporteGravado" => (string)round($row[44],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[45])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P034",
          "Concepto" => "COMPLEMENTO SALARIO",
          "ImporteGravado" => (string)round($row[45],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[46])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "028",
          "Concepto" => "COMISIONES",
          "ImporteGravado" => (string)round($row[46],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[47])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P038",
          "Concepto" => "BONO",
          "ImporteGravado" => (string)round($row[47],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[48])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "016",
          "Clave" => "P043",
          "Concepto" => "VIATICOS",
          "ImporteGravado" => (string)round($row[48],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[49])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P039",
          "Concepto" => "BONO DESEMPEÑO",
          "ImporteGravado" => (string)round($row[49],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[50])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P655",
          "Concepto" => "GRATIFICACION",
          "ImporteGravado" => (string)round($row[50],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[51])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "019",
          "Clave" => "P124",
          "Concepto" => "TIEMPO EXTRA TRIPLE",
          "ImporteGravado" => (string)round($row[51],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[53])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P020",
          "Concepto" => "AUSENTISMO (FALTAS)",
          "ImporteGravado" => (string)round($row[53],2),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[60])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P008",
          "Concepto" => "DESCANSO LABORADO",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)round($row[60],2)
        ]);
      }
      if(!empty($row[62])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P009",
          "Concepto" => "DIA FESTIVO TRABAJADO",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)round($row[62],2)
        ]);
      }
      if(!empty($row[66])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "020",
          "Clave" => "P022",
          "Concepto" => "PRIMA DOMINICAL",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)round($row[66],2)
        ]);
      }
      if(!empty($row[67])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "016",
          "Clave" => "P100",
          "Concepto" => "PRESTAMO PERSONAL",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)round($row[67],2)
        ]);
      }
      $nomina12_deduccion = [
        "TotalOtrasDeducciones" => (string)round($row[90],2),
        "TotalImpuestosRetenidos" => (string)round($row[75],2)
      ];
      $nomina12_detalleDeduccion = [];
      if(!empty($row[75])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "002",
          "Clave" => "D001",
          "Concepto" => "ISR",
          "Importe" => (string)round($row[75],2)
        ]);
      }
      if(!empty($row[76])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "001",
          "Clave" => "D003",
          "Concepto" => "IMSS",
          "Importe" => (string)round($row[76],2)
        ]);
      }
      if(!empty($row[77])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "003",
          "Clave" => "D004",
          "Concepto" => "APORTACIONES A RETIRO, CESANTIA EN EDAD AVANZADA Y VEJEZ",
          "Importe" => (string)round($row[77],2)
        ]);
      }
      if(!empty($row[78])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "010",
          "Clave" => "D005",
          "Concepto" => "CREDITO INFONAVIT",
          "Importe" => (string)round($row[78],2)
        ]);
      }
      if(!empty($row[79])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "010",
          "Clave" => "D009",
          "Concepto" => "SEGURO DE VIVIENDA",
          "Importe" => (string)round($row[79],2)
        ]);
      }

      if(!empty($row[72])){
        $TotrosPagos = true;
        $nomina12_otrosPagos_header = [
          "TipoOtroPago" => "002",
          "Clave" => "P600",
          "Concepto" => "SUBSIDIO AL EMPLEO",
          "Importe" => (string)round($row[72],2)
        ];
        $nomina12_otrosPagos_subsidio = [
          "SubsidioCausado" => (string)round($row[72],2)
        ];
      }
    }
}

$comprobante['empresa'] = "9999";
$comprobante['comprobante']['header'] = $header;
$comprobante['comprobante']['emisor'] = $emisor;
$comprobante['comprobante']['receptor'] = $receptor;
$comprobante['comprobante']['conceptos'] = [$conceptos];
$comprobante['comprobante']['complemento']['nomina12']['header']= $nomina12_header;
$comprobante['comprobante']['complemento']['nomina12']['emisor'] = $nomina12_emisor;
$comprobante['comprobante']['complemento']['nomina12']['receptor'] = $nomina12_receptor;
$comprobante['comprobante']['complemento']['nomina12']['percepcion'] = $nomina12_percepcion;
$comprobante['comprobante']['complemento']['nomina12']['detallePercepcion'] = $nomina12_detallePercepcion;
$comprobante['comprobante']['complemento']['nomina12']['deduccion'] = $nomina12_deduccion;
$comprobante['comprobante']['complemento']['nomina12']['detalleDeduccion'] = $nomina12_detalleDeduccion;
if($TotrosPagos){
  $comprobante['comprobante']['complemento']['nomina12']['OtrosPagos'][0]['header'] = $nomina12_otrosPagos_header;
  $comprobante['comprobante']['complemento']['nomina12']['OtrosPagos'][0]['subsidio'] = $nomina12_otrosPagos_subsidio;
}
die(json_encode($comprobante, JSON_UNESCAPED_UNICODE));

$reader->close();




function DateToday(){
  $dt = new DateTime();
  $tz = new DateTimeZone("America/Mexico_City");
  $dt->setTimezone($tz);
  $ddt = $dt->format("Y-m-d H:i:s");
  $fec1 = substr($ddt,0,4)."-".substr($ddt,5,2)."-".substr($ddt,8,2)."T".substr($ddt,11,8);
  return $fec1;
}

function calcAntiguedad($finf, $inrel){
  $finf = $finf->format('Y-m-d');
  $inrel = $inrel->format('Y-m-d');
  $datetime1 = new Carbon($finf);
  $datetime2 = new Carbon($inrel);
  $dias = $datetime1->diffInDays($datetime2);
  $dias = $dias + 1;
  $antiguedad = round($dias / 7);
  return 'P'.$antiguedad.'W';
}
