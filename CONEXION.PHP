<?php
$host = "localhost";
$usuario_db = "root";
$password_db = "";
$base_datos = "pagina_futbol";

$conexion = new mysqli($host, $usuario_db, $password_db, $base_datos);

if($conexion->connect_error){
    die("Error de conexión: " . $conexion->connect_error);
}
?>
