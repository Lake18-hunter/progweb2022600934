<?php
session_start();

// Validar sesión activa
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["tipo_usuario"] !== 'alumno') {
    header("Location: login.php");
    exit;
}

// Conexión a BD
$pdo = new PDO("mysql:host=localhost;dbname=calificaciones", "root", "lake18");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener ID del alumno desde la sesión
$id_alumno = $_SESSION["usuario"]["id_alumno"];
$nombre = $_SESSION["usuario"]["nombre"];

// Consulta de calificaciones
$stmt = $pdo->prepare("SELECT * FROM alumnos WHERE id = :id_alumno");
$stmt->bindParam(":id_alumno", $id_alumno, PDO::PARAM_INT);
$stmt->execute();
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

// Calificaciones individuales
$materias = [
    'español' => $alumno['calificacion_espanol'],
    'matemáticas' => $alumno['calificacion_matematicas'],
    'inglés' => $alumno['calificacion_ingles'],
    'historia' => $alumno['calificacion_historia'],
    'computación' => $alumno['calificacion_computacion'],
    'geografía' => $alumno['calificacion_geografia'],
    'biología' => $alumno['calificacion_biologia']
];

// Obtener la más alta y más baja
$calif_mayor = max($materias);
$calif_menor = min($materias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Alumno</title>
    <style>
        body {
            background: linear-gradient(to bottom right, #fce3c4, #f9d6b4);
            font-family: Arial, sans-serif;
            padding: 2em;
            color: #4e2603;
        }

        h2 {
            text-align: center;
            color: #b85c38;
        }

        table {
            margin: 1em auto;
            border-collapse: collapse;
            width: 70%;
        }

        th, td {
            padding: 10px;
            border: 1px solid #b85c38;
            text-align: center;
        }

        th {
            background-color: #f0b38c;
        }

        .boton {
            display: block;
            margin: 2em auto;
            padding: 10px 20px;
            background-color: #cc4e4e;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .boton:hover {
            background-color: #a94444;
        }
    </style>
</head>
<body>
    <h2>Bienvenido, <?= htmlspecialchars($nombre) ?></h2>

    <table>
        <tr>
            <th>Materia</th>
            <th>Calificación</th>
        </tr>
        <?php foreach ($materias as $materia => $calif): ?>
            <tr>
                <td><?= ucfirst($materia) ?></td>
                <td><?= $calif ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3 style="text-align: center;">Calificación más alta: <?= $calif_mayor ?> | Calificación más baja: <?= $calif_menor ?></h3>

    <a href="login.php" class="boton">Cerrar sesión</a>
</body>
</html>
