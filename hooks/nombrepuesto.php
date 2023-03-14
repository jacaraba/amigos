<?php
// Recibe el parámetro POST "direccion"
$direccion = $_POST["direccion"];

// Conexión a la base de datos MySQL
$host = "localhost";
$user = "u392441939_amigos";
$password = "Timo2195";
$dbname = "u392441939_amigos";
$conn = mysqli_connect($host, $user, $password, $dbname);

// Verifica la conexión a la base de datos
if (!$conn) {
  die("Conexión fallida: " . mysqli_connect_error());
}

// Consulta a la base de datos
$sql = "SELECT * FROM `divpol2022` where `direccion` = ".$direccion." LIMIT 1";
$resultado = mysqli_query($conn, $sql);

// Crea un arreglo con los resultados de la consulta
$amigos = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
  $amigos[] = $fila;
}

// Convierte el arreglo en formato JSON y lo muestra en pantalla
echo json_encode($amigos);

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
