<?php
session_start();
$is_pdf = isset($_GET['pdf']) && $_GET['pdf'] == '1';

if (!isset($_SESSION['usuario']) && !$is_pdf) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "lake18", "calificaciones");
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$materias = ['espanol', 'matematicas', 'ingles', 'historia', 'computacion', 'geografia', 'biologia'];
$columnas = array_map(fn($m) => "calificacion_$m", $materias);
$columnas_sql = implode(", ", $columnas);
$resultado = $mysqli->query("SELECT nombre, $columnas_sql FROM alumnos");

$mejorAlumno = "";
$peorAlumno = "";
$mejorPromedio = 0;
$peorPromedio = 11;
$calificacionesAltas = array_fill_keys($materias, 0);
?>

<?php if ($is_pdf): ?>
<page backtop="20mm" backbottom="20mm" backleft="15mm" backright="15mm">
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        font-size: 12px;
    }
    th, td {
        border: 1px solid #555;
        padding: 4px;
    }
    th {
        background-color: #ddd;
    }
</style>
<?php else: ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Calificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #fcd9b8, #f8cfa0);
            font-family: 'Segoe UI', sans-serif;
        }
        .estadisticas-box {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 2rem auto;
        }
        table {
            width: 100%;
            text-align: center;
        }
        th {
            background-color: #f3c49b;
            color: #4b2e19;
        }
        footer {
            background-color: #d2835a;
            color: white;
            text-align: center;
            padding: 10px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .btn-volver {
            background-color: #d2835a;
            color: white;
        }
    </style>
</head>
<body>
<div class="estadisticas-box">
<?php endif; ?>

    <h3 class="text-center mb-4">Se ha capturado las calificaciones correctamente</h3>
    <h5 class="text-center text-secondary mb-3">Listado de Calificaciones</h5>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <?php foreach ($materias as $materia): ?>
                    <th><?= ucfirst($materia) ?></th>
                <?php endforeach; ?>
                <th>Promedio</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($alumno = $resultado->fetch_assoc()): ?>
                <?php
                $suma = 0;
                $validas = 0;
                foreach ($materias as $materia) {
                    $columna = "calificacion_$materia";
                    $cal = $alumno[$columna];
                    if (is_numeric($cal)) {
                        $suma += $cal;
                        $validas++;
                        if ($cal > $calificacionesAltas[$materia]) {
                            $calificacionesAltas[$materia] = $cal;
                        }
                    }
                }
                $promedio = $validas ? $suma / $validas : 0;
                if ($promedio > $mejorPromedio) {
                    $mejorPromedio = $promedio;
                    $mejorAlumno = $alumno['nombre'];
                }
                if ($promedio < $peorPromedio) {
                    $peorPromedio = $promedio;
                    $peorAlumno = $alumno['nombre'];
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($alumno['nombre']) ?></td>
                    <?php foreach ($materias as $materia): ?>
                        <td><?= $alumno["calificacion_$materia"] ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($promedio, 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <p><strong>Mejor Alumno:</strong> <?= htmlspecialchars($mejorAlumno) ?> (Promedio: <?= number_format($mejorPromedio, 2) ?>)</p>
        <p><strong>Peor Alumno:</strong> <?= htmlspecialchars($peorAlumno) ?> (Promedio: <?= number_format($peorPromedio, 2) ?>)</p>

        <h6 class="mt-3">Calificaciones más altas por materia</h6>
        <ul>
            <?php foreach ($calificacionesAltas as $materia => $max): ?>
                <li><strong><?= ucfirst($materia) ?>:</strong> <?= $max ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php if (!$is_pdf): ?>
    <div class="d-flex justify-content-between mt-4">
        <a href="calificaciones.php" class="btn btn-volver">Modificar Calificaciones</a>
        <form method="post" action="logout.php">
            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
        </form>
    </div>
</div>
<footer>
    &copy; 2025 Raíces del Saber | Todos los derechos reservados
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php else: ?>
</page>
<?php endif; ?>
