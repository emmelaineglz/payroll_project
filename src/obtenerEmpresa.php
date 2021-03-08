<?php
function obtenerDatosEmpresa ($empresa, $rfc, $varios = 'NO') {
    $Usuario="root";
    $Password="20edo18";
    $Servidor="localhost:3306";
    $BaseDeDatos="onenom";

    $db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");
    $query = "SELECT * FROM clientes WHERE idcliente = ".$empresa;
    $query_exec = mysqli_query($db_xion, $query);
    $row=mysqli_fetch_array($query_exec);

    if($row['varios'] == 'SI' || $varios == 'SI') {
       $query2 = "SELECT * FROM onenom_" . $empresa . ".riesgoDePuesto WHERE rfc = '" . $rfc . "';";
       $query_exec2 = mysqli_query($db_xion, $query2);
       $row2 =mysqli_fetch_array($query_exec2);
       
       
       $cfdiFiscal = [
            "rfc" => $row2['rfc'],
            "razonSocial" => $row2['nombreFiscal'],
            "domicilio" => $row2['domicilio'],
            "codigoPostal" => $row2['cp'],
            "registroPatronal" => $row2['registroPatronal']
        ];
    } else {
        $cfdiFiscal = [
            "rfc" => $row['rfc'],
            "razonSocial" => $row['razonSocial'],
            "domicilio" => $row['domicilio'],
            "codigoPostal" => $row['codigoPostal'],
            "registroPatronal" => $row['registroPatronal']
        ];
    }
    $cfdiAsimilados = [
        "rfc" => $row['rfcA'],
        "razonSocial" => $row['razonSocialA'],
        "domicilio" => $row['domicilioA'],
        "codigoPostal" => $row['cpA'],
        "registroPatronal" => $row['registroPatronal']
    ];
    $cfdiPPP = [
        "rfc" => $row['rfcPPP'],
        "razonSocial" => $row['razonSocialPPP'],
        "domicilio" => $row['domicilioPPP'],
        "codigoPostal" => $row['cpPPP'],
        "registroPatronal" => $row['registroPatronal']
    ];
    if($cfdiFiscal['rfc'] == $rfc) {
        $responseFinal['cfdiFiscal'] = $cfdiFiscal;
        return json_encode($responseFinal);
    }

    if($cfdiAsimilados['rfc'] == $rfc) {
        $responseFinal['cfdiFiscal'] = $cfdiAsimilados;
        return json_encode($responseFinal);
    }

    if($cfdiPPP['rfc'] == $rfc) {
        $responseFinal['cfdiFiscal'] = $cfdiPPP;
        return json_encode($responseFinal);
    }
}
function obtenerXmlPorRfc ($empresa, $archivo) {
    $basePath = dirname(__DIR__);
    $Usuario="root";
    $Password="20edo18";
    $Servidor="localhost:3306";
    $BaseDeDatos="onenom";

    $db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");
    $query = "SELECT * FROM onenom_" . $empresa . ".riesgoDePuesto WHERE rfc <> '';";
    $query_exec = mysqli_query($db_xion, $query);
    $n = 0;
    while ($row = mysqli_fetch_array($query_exec)) {
        $rfc = $row["rfc"];
        $rutaFile = "{$basePath}/uploads/{$empresa}/{$rfc}/{$archivo}";
        if (file_get_contents($rutaFile)) {
            return json_encode([
                "src" => $rutaFile,
                "rfc" => $rfc,
                "varios" => ($n > 0) ? 'SI' : 'NO'
            ]);
        }
        $n++;
    }
}