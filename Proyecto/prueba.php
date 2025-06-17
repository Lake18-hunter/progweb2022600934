<?php
$host = "localhost";
$dbname = "Calificaciones";
$user = "postgres";
$password = "lake18";

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo "Error al conectar a PostgreSQL.";
} else {
    echo "¡Conexión exitosa!";
}
?>
