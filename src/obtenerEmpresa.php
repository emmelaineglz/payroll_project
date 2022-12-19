<?php
function getAnyRfc ($empresa) {
    $Usuario="admin";
    $Password="N3xN0m";
    $Servidor="localhost:3306";
    $BaseDeDatos="onenom";

    $db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");
    $query = "SELECT * FROM clientes WHERE idcliente = ".$empresa;
    $query_exec = mysqli_query($db_xion, $query);
    $row=mysqli_fetch_array($query_exec);

    return ($row['varios'] == 'SI') ? true : false;
};

function obtenerDatosEmpresa ($empresa, $rfc, $archivo) {
    $Usuario="admin";
    $Password="N3xN0m";
    $Servidor="localhost:3306";
    $BaseDeDatos="onenom";
    $db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");
    
    $varios = getAnyRfc($empresa);
    $uriFile = obtenerXmlPorRfc($empresa, $archivo);

    $query = "SELECT * FROM onenom_" . $empresa . ".riesgoDePuesto WHERE rfc = '" . $uriFile['rfc'] . "';";
    $query_exec = mysqli_query($db_xion, $query);
    $row =mysqli_fetch_array($query_exec);
    
    $cfdiFiscal = [
        "rfc" => $row['rfc'],
        "razonSocial" => (!isset($row['razonSocial'])) ? $row['nombreFiscal'] : $row['razonSocial'],
        "domicilio" => $row['domicilio'],
        "codigoPostal" => (!isset($row['codigoPostal'])) ? $row['cp'] : $row['codigoPostal'],
        "registroPatronal" => $row['registroPatronal'],
        "src" => $uriFile['src'],
        "regimenFiscal" => $row['regimenFiscal'],
        "varios" => $uriFile['varios']
    ];

    $cfdiAsimilados = [
        "rfc" => $row['rfcA'],
        "razonSocial" => $row['razonSocialA'],
        "domicilio" => $row['domicilioA'],
        "codigoPostal" => $row['cpA'],
        "registroPatronal" => $row['registroPatronal'],
        "src" => $uriFile['src'],
        "regimenFiscal" => $row['regimenFiscal'],
        "varios" => $uriFile['varios']
    ];
    $cfdiPPP = [
        "rfc" => $row['rfcPPP'],
        "razonSocial" => $row['razonSocialPPP'],
        "domicilio" => $row['domicilioPPP'],
        "codigoPostal" => $row['cpPPP'],
        "registroPatronal" => $row['registroPatronal'],
        "src" => $uriFile['src'],
        "regimenFiscal" => $row['regimenFiscal'],
        "varios" => $uriFile['varios']
    ];

    if($cfdiAsimilados['rfc'] == $rfc) {
        $responseFinal['cfdiFiscal'] = $cfdiAsimilados;
        return $responseFinal;
    }

    if($cfdiPPP['rfc'] == $rfc) {
        $responseFinal['cfdiFiscal'] = $cfdiPPP;
        return $responseFinal;
    }

    $responseFinal['cfdiFiscal'] = $cfdiFiscal;
    return $responseFinal;

}
function obtenerXmlPorRfc ($empresa, $archivo) {
    $basePath = dirname(__DIR__);
    $Usuario="admin";
    $Password="N3xN0m";
    $Servidor="localhost:3306";
    $BaseDeDatos="onenom";

    $db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");
    $query = "SELECT * FROM onenom_" . $empresa . ".riesgoDePuesto WHERE rfc <> '';";
    $query_exec = mysqli_query($db_xion, $query);
    $n = 0;
    while ($row = mysqli_fetch_array($query_exec)) {
        $rfc = trim($row["rfc"]);
        $rutaFile = "{$basePath}/uploads/{$empresa}/{$rfc}/{$archivo}";
        if (file_get_contents($rutaFile)) {
            return [
                "src" => $rutaFile,
                "rfc" => $rfc,
                "varios" => ($n > 0) ? 'SI' : 'NO'
            ];
        }
        $n++;
    }
}