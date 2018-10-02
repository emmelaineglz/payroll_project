<?php
include "vendor/autoload.php";

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Carbon\Carbon;

$filePath = '../uploads/excel/TIMBRA_FAPRICO_JUNIO_2018_1.xlsx';

$reader = ReaderFactory::create(Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files
$TotrosPagos = false;
$data = [];
$reader->open($filePath);

foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
      //var_dump($row);
      $nomina12_otrosPagos_header = [];
      $nomina12_otrosPagos_subsidio = [];
      $header = [
        "Serie" => (string)$row[0],
        "Folio" => (string)$row[1],
        "Fecha" => "2018-06-06T22:50:57",
        "FormaPago" => (string)$row[8],
        "SubTotal" => (string)number_format($row[78],2, '.', ''),
        "Descuento" => (string)number_format($row[97],2, '.', ''),
        "Moneda" => $row[10],
        "Total" => (string)number_format((float)$row[98],2, '.', ''),
        "TipoDeComprobante" => $row[6],
        "MetodoPago" => $row[9],
        "LugarExpedicion" => "06700"
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
        "ValorUnitario" => (string)number_format(round($row[78],2),2, '.', ''),
        "Importe" => (string)number_format(round(($row[13] * $row[78]),2),2, '.', ''),
        "Descuento" => (string)number_format(round($row[97],2),2, '.', '')
      ];

      $nomina12_header = [
          "xmlns:nomina12" =>  (string)"http://www.sat.gob.mx/nomina12",
          "Version" => "1.2",
          "TipoNomina" => "O",
          "FechaPago" => $row[38]->format('Y-m-d'),
          "FechaInicialPago" => $row[39]->format('Y-m-d'),
          "FechaFinalPago" => $row[40]->format('Y-m-d'),
          "NumDiasPagados" => (string)$row[41],
          "TotalPercepciones" => (!empty($row[77]))? (string)number_format(round(($row[78] - $row[77]),2),2, '.', '') : (string)number_format(round($row[78],2),2, '.', ''),
          "TotalDeducciones" => (string)number_format(round($row[97],2),2, '.', ''),
          "xsi:schemaLocation" => (string)"http://www.sat.gob.mx/nomina12 http://www.sat.gob.mx/informacion_fiscal/factura_electronica/Documents/Complementoscfdi/nomina12.xsd"
      ];
      if(!empty($row[77])){
        $nomina12_header["TotalOtrosPagos"] = (string)number_format(round($row[77],2),2, '.', '');
      }

      $nomina12_emisor = [
        "RegistroPatronal" => (string)$row[4],
        "RfcPatronOrigen" => (string)$row[2]
      ];

      $nomina12_receptor = [
        "Curp" => (string)$row[17],
        "FechaInicioRelLaboral" => $row[21]->format('Y-m-d'),
        "Antigüedad" => calcAntiguedad($row[39], $row[21]),
        "TipoContrato" => "$row[22]",
        "TipoJornada" => "$row[24]",
        "TipoRegimen" => "$row[25]",
        "NumEmpleado" => (string)$row[26],
        "Departamento" => (string)$row[28],
        "PeriodicidadPago" => (string)$row[31],
        "Sindicalizado" => (string)$row[23],
        "SalarioBaseCotApor" => (string)$row[34],
        "ClaveEntFed" => (string)$row[36],
        "NumSeguridadSocial" => (string)$row[18],
        "RiesgoPuesto" => "2",
        "SalarioDiarioIntegrado" => (string)number_format(round($row[35],2),2, '.', '')
      ];
      if(!empty($row[32])){
        $nomina12_receptor["Banco"] = (string)$row[32];
      }
      $nomina12_percepcion = [
        "TotalSueldos" => (string)number_format(round($row[74],2),2, '.', ''),
        "TotalGravado" => (string)number_format(round($row[75],2),2, '.', ''),
        "TotalExento" => (string)number_format(round($row[76],2),2, '.', '')
      ];

      $nomina12_detallePercepcion = [];
      if(!empty($row[43])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P001",
          "Concepto" => "SUELDOS, SALARIOS  RAYAS Y JORNALES",
          "ImporteGravado" => (string)number_format(round($row[43],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[44])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P002",
          "Concepto" => "SUELDOS, SALARIOS  RAYAS Y JORNALES",
          "ImporteGravado" => (string)number_format(round($row[44],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[45])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P034",
          "Concepto" => "COMPLEMENTO SALARIO",
          "ImporteGravado" => (string)number_format(round($row[45],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[46])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "028",
          "Clave" => "P031",
          "Concepto" => "COMISIONES",
          "ImporteGravado" => (string)number_format(round($row[46],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[47])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P038",
          "Concepto" => "BONO",
          "ImporteGravado" => (string)number_format(round($row[47],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[48])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "016",
          "Clave" => "P043",
          "Concepto" => "VIATICOS",
          "ImporteGravado" => (string)number_format(round($row[48],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[49])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P039",
          "Concepto" => "BONO DESEMPEÑO",
          "ImporteGravado" => (string)number_format(round($row[49],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[50])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P655",
          "Concepto" => "GRATIFICACION",
          "ImporteGravado" => (string)number_format(round($row[50],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[51])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "019",
          "Clave" => "P124",
          "Concepto" => "TIEMPO EXTRA TRIPLE",
          "ImporteGravado" => (string)number_format(round($row[51],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[52])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P003",
          "Concepto" => "VACACIONES",
          "ImporteGravado" => (string)number_format(round($row[52],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      /*if(!empty($row[53])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P020",
          "Concepto" => "AUSENTISMO (FALTAS)",
          "ImporteGravado" => (string)abs(number_format(round($row[53],2),2, '.', '')),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[54])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P030",
          "Concepto" => "FALTA POR RETARDO",
          "ImporteGravado" => (string)abs(number_format(round($row[54],2),2, '.', '')),
          "ImporteExento" => "0.00"
        ]);
      }*/
      if(!empty($row[55])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P626",
          "Concepto" => "RETROACTIVO",
          "ImporteGravado" => (string)number_format(round($row[55],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[56])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P625",
          "Concepto" => "DEV DESCUENTO IMPROCEDENTE",
          "ImporteGravado" => (string)number_format(round($row[56],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[57])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "019",
          "Clave" => "P123",
          "Concepto" => "TIEMPO EXTRA DOBLE",
          "ImporteGravado" => (string)number_format(round($row[57],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[58])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "019",
          "Clave" => "P123",
          "Concepto" => "TIEMPO EXTRA DOBLE",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[58],2),2, '.', '')
        ]);
      }
      if(!empty($row[59])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P008",
          "Concepto" => "DESCANSO LABORADO",
          "ImporteGravado" => (string)number_format(round($row[59],2),2, '.', ''),
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
          "ImporteExento" => (string)number_format(round($row[60],2),2, '.', '')
        ]);
      }
      if(!empty($row[61])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P009",
          "Concepto" => "DIA FESTIVO TRABAJADO",
          "ImporteGravado" => (string)number_format(round($row[61],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[62])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "001",
          "Clave" => "P009",
          "Concepto" => "DIA FESTIVO TRABAJADO",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[62],2),2, '.', '')
        ]);
      }
      if(!empty($row[63])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "021",
          "Clave" => "P021",
          "Concepto" => "PRIMA VACACIONAL",
          "ImporteGravado" => (string)number_format(round($row[63],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[64])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "021",
          "Clave" => "P021",
          "Concepto" => "PRIMA VACACIONAL",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[64],2),2, '.', '')
        ]);
      }
      if(!empty($row[65])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "020",
          "Clave" => "P022",
          "Concepto" => "PRIMA DOMINICAL",
          "ImporteGravado" => (string)number_format(round($row[65],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[66])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "020",
          "Clave" => "P022",
          "Concepto" => "PRIMA DOMINICAL",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[66],2),2, '.', '')
        ]);
      }
      if(!empty($row[67])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "016",
          "Clave" => "P100",
          "Concepto" => "PRESTAMO PERSONAL",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[67],2),2, '.', '')
        ]);
      }
      if(!empty($row[68])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "016",
          "Clave" => "P101",
          "Concepto" => "OTROS INGRESOS",
          "ImporteGravado" => (string)number_format(round($row[68],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[69])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "029",
          "Clave" => "P029",
          "Concepto" => "VALES DESPENSA",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[69],2),2, '.', '')

        ]);
      }
      if(!empty($row[70])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "029",
          "Clave" => "P029",
          "Concepto" => "VALES DESPENSA",
          "ImporteGravado" => (string)number_format(round($row[70],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[71])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "005",
          "Clave" => "P005",
          "Concepto" => "FONDO DE AHORRO EMPRESA",
          "ImporteGravado" => "0.00",
          "ImporteExento" => (string)number_format(round($row[71],2),2, '.', '')

        ]);
      }
      if(!empty($row[72])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "005",
          "Clave" => "P005",
          "Concepto" => "FONDO DE AHORRO EMPRESA",
          "ImporteGravado" => (string)number_format(round($row[72],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      if(!empty($row[73])){
        array_push($nomina12_detallePercepcion,
        [
          "TipoPercepcion" => "038",
          "Clave" => "P038",
          "Concepto" => "PREMIO DE PRODUCTIVIDAD",
          "ImporteGravado" => (string)number_format(round($row[73],2),2, '.', ''),
          "ImporteExento" => "0.00"
        ]);
      }
      $nomina12_deduccion = [
        "TotalOtrasDeducciones" => (string)number_format(round($row[95],2),2, '.', ''),
        "TotalImpuestosRetenidos" => (string)number_format(round($row[80],2),2, '.', '')
      ];

      $nomina12_detalleDeduccion = [];
      if(!empty($row[80])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "002",
          "Clave" => "D001",
          "Concepto" => "ISR",
          "Importe" => (string)number_format(round($row[80],2),2, '.', '')
        ]);
      }
      if(!empty($row[81])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "001",
          "Clave" => "D003",
          "Concepto" => "IMSS",
          "Importe" => (string)number_format(round($row[81],2),2, '.', '')
        ]);
      }
      if(!empty($row[82])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "003",
          "Clave" => "D004",
          "Concepto" => "APORTACIONES A RETIRO, CESANTIA EN EDAD AVANZADA Y VEJEZ",
          "Importe" => (string)number_format(round($row[82],2),2, '.', '')
        ]);
      }
      if(!empty($row[83])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "010",
          "Clave" => "D005",
          "Concepto" => "CREDITO INFONAVIT",
          "Importe" => (string)number_format(round($row[83],2),2, '.', '')
        ]);
      }
      if(!empty($row[84])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "010",
          "Clave" => "D009",
          "Concepto" => "SEGURO DE VIVIENDA",
          "Importe" => (string)number_format(round($row[84],2),2, '.', '')
        ]);
      }
      if(!empty($row[85])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "006",
          "Clave" => "D013",
          "Concepto" => "INCAPACIDAD MATERNIDAD",
          "Importe" => (string)number_format(round($row[85],2),2, '.', '')
        ]);
      }
      if(!empty($row[86])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "006",
          "Clave" => "D015",
          "Concepto" => "INCAPACIDAD RIESGO TRABAJO",
          "Importe" => (string)number_format(round($row[86],2),2, '.', '')
        ]);
      }
      if(!empty($row[87])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "006",
          "Clave" => "D014",
          "Concepto" => "INCAPACIDAD ENFER GRAL",
          "Importe" => (string)number_format(round($row[87],2),2, '.', '')
        ]);
      }
      if(!empty($row[88])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "004",
          "Clave" => "D010",
          "Concepto" => "DESC. AXEDES",
          "Importe" => (string)number_format(round($row[88],2),2, '.', '')
        ]);
      }
      if(!empty($row[89])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "004",
          "Clave" => "D011",
          "Concepto" => "DESC PRESTAMO QUANT",
          "Importe" => (string)number_format(round($row[89],2),2, '.', '')
        ]);
      }
      if(!empty($row[90])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "011",
          "Clave" => "D316",
          "Concepto" => "CREDITO FONACOT",
          "Importe" => (string)number_format(round($row[90],2),2, '.', '')
        ]);
      }
      if(!empty($row[91])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "013",
          "Clave" => "D328",
          "Concepto" => "DESC PAGO IMPROC.",
          "Importe" => (string)number_format(round($row[91],2),2, '.', '')
        ]);
      }
      if(!empty($row[92])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "020",
          "Clave" => "D336",
          "Concepto" => "DIAS NO TRABAJADOS",
          "Importe" => (string)number_format(round($row[92],2),2, '.', '')
        ]);
      }
      if(!empty($row[93])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "007",
          "Clave" => "D403",
          "Concepto" => "PENSION ALIMENTICIA",
          "Importe" => (string)number_format(round($row[93],2),2, '.', '')
        ]);
      }
      if(!empty($row[94])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "015",
          "Clave" => "D399",
          "Concepto" => "DESCUENTO GAFETE",
          "Importe" => (string)number_format(round($row[94],2),2, '.', '')
        ]);
      }
      if(!empty($row[96])){
        array_push($nomina12_detalleDeduccion,
        [
          "TipoDeduccion" => "004",
          "Clave" => "D004",
          "Concepto" => "FONDO DE AHORRO",
          "Importe" => (string)number_format(round($row[96],2),2, '.', '')
        ]);
      }
      if(!empty($row[77])){
        $TotrosPagos = true;
        $nomina12_otrosPagos_header = [
          "TipoOtroPago" => "002",
          "Clave" => "P600",
          "Concepto" => "SUBSIDIO AL EMPLEO",
          "Importe" => (string)number_format(round($row[77],2),2, '.', '')
        ];
        $nomina12_otrosPagos_subsidio = [
          "SubsidioCausado" => (string)number_format(round($row[77],2),2, '.', '')
        ];
      }

      // AQui termina el ciclo de cada row
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

      $data['data'][] = $comprobante;
    }
}


die(json_encode($data, JSON_UNESCAPED_UNICODE));

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
