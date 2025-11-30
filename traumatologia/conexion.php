<?php
$server = "localhost";
$user = "root";
$pass = "nueva_contraseña";
$db = "traumatologia";

$conexion = new mysqli($server, $user, $pass, $db);

if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_error);
}

?>
