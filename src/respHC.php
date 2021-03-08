<?
$Usuario='root';
$Password='xxxxxx';
$Servidor='localhost';
$BaseDeDatos="xxxxx";

$db_xion = mysqli_connect($Servidor,$Usuario,$Password,$BaseDeDatos) or die("Error: El SERVIDOR no puede conectar con la base de datos");


$sql = "SELECT idcliente FROM onenom.clientes";
$reg = mysqli_query($db_xion, $sql);
while ($row = mysqli_fetch_array($reg)) {
	$idcliente = $row["idcliente"];
    echo "onenom_".$idcliente.".headcount<br>";
   $archivo_csv = fopen('backups/headcount_'.$idcliente.'.csv', 'w');
   if ($archivo_csv) {
        fputs($archivo_csv, "".PHP_EOL);

        $sqlHC = "SELECT * FROM onenom_".$idcliente.".headcount";
        $reg2 = mysqli_query($db_xion, $sqlHC);
        while ($fila = mysqli_fetch_assoc($reg2)) {
            fputs($archivo_csv, implode($fila, ',').PHP_EOL);
        }
        fclose($archivo_csv);
    } else {
        echo "El archivo no existe o no se pudo crear";
    }
}
echo "TERMINO $reg<br>";

?>