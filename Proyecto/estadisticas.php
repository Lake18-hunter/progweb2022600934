<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$id_profesor = $usuario['id_profesor'];

$mysqli = new mysqli('localhost', 'root', 'lake18', 'calificaciones');
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$prof = $mysqli->query("SELECT grado, grupo, materia FROM profesores WHERE id = $id_profesor")->fetch_assoc();
$grado = $prof['grado'];
$grupo = $prof['grupo'];
$materia = strtolower($prof['materia']);
$campo_calificacion = "calificacion_" . $materia;

$sql = "SELECT nombre, $campo_calificacion AS calificacion FROM alumnos WHERE grado = ? AND grupo = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $grado, $grupo);
$stmt->execute();
$result = $stmt->get_result();
$alumnos = $result->fetch_all(MYSQLI_ASSOC);

$mejores = [];
$peores = [];
$reprobados = [];
$calif_max = -1;
$calif_min = 11;

foreach ($alumnos as $alumno) {
    $calif = intval($alumno['calificacion']);
    if ($calif > $calif_max) {
        $calif_max = $calif;
        $mejores = [$alumno['nombre']];
    } elseif ($calif == $calif_max) {
        $mejores[] = $alumno['nombre'];
    }

    if ($calif < $calif_min) {
        $calif_min = $calif;
        $peores = [$alumno['nombre']];
    } elseif ($calif == $calif_min) {
        $peores[] = $alumno['nombre'];
    }

    if ($calif < 6) {
        $reprobados[] = $alumno['nombre'];
    }
}
?>
<?php if (isset($_GET['pdf']) && $_GET['pdf'] == '1') echo '<page backtop="20mm" backbottom="20mm" backleft="10mm" backright="10mm">'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas del Profesor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --color-primario: #D98860;
            --color-secundario: #F4C095;
            --color-fondo: #FFEEDB;
            --color-texto: #5F4B32;
        }

        .dark-mode {
            --color-primario: #8B523A;
            --color-secundario: #B37D5A;
            --color-fondo: #E3C2A5;
            --color-texto: #3F2C1F;
        }

        body {
            background: linear-gradient(135deg, var(--color-fondo), var(--color-secundario));
            color: var(--color-texto);
        }

        .btn-toggle {
            background-color: var(--color-primario);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1100;
        }

        .btn-toggle:hover {
            background-color: var(--color-secundario);
        }

        .navbar {
            background-color: var(--color-primario);
        }

        .navbar a {
            color: white !important;
        }

        footer {
            background-color: var(--color-primario);
            color: white;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>

    <?php if (!isset($_GET['pdf'])): ?>
    <script>
        function toggleMode() {
            const body = document.body;
            const icon = document.getElementById("mode-icon");
            const isDark = body.classList.toggle("dark-mode");

            if (icon) {
                icon.src = isDark ? "sun_icon.png" : "moon_icon.png";
                icon.alt = isDark ? "Modo claro" : "Modo oscuro";
            }

            setCookie("theme", isDark ? "dark" : "light", 7);
        }

        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/";
        }

        function getCookie(name) {
            const cookies = document.cookie.split(";");
            for (let c of cookies) {
                const [key, val] = c.trim().split("=");
                if (key === name) return val;
            }
            return null;
        }

        window.onload = function () {
            const theme = getCookie("theme");
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                const icon = document.getElementById("mode-icon");
                if (icon) {
                    icon.src = "sun_icon.png";
                    icon.alt = "Modo claro";
                }
            }
        };
    </script>
    <?php endif; ?>
</head>
<body>

<?php if (!isset($_GET['pdf'])): ?>
<!-- Botón Día/Noche -->
<button class="btn-toggle" onclick="toggleMode()">Día/Noche</button>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container d-flex align-items-center">
        <a href="#" onclick="toggleMode(event)" class="me-3 order-first">
            <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
        </a>
        <span class="navbar-brand mb-0 h1">Estadísticas de Calificaciones</span>
    </div>
</nav>
<?php endif; ?>

<div class="container mt-5">
    <h2 class="text-center">Estadísticas de Calificaciones</h2>
    <p class="text-center">Profesor: <strong><?= htmlspecialchars($usuario['username']) ?></strong></p>
    <p class="text-center">Materia: <strong><?= ucfirst($materia) ?></strong></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre del Alumno</th>
                <th>Calificación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alumnos as $al): ?>
                <tr>
                    <td><?= htmlspecialchars($al['nombre']) ?></td>
                    <td><?= $al['calificacion'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="alert alert-success">
        Mejores alumnos: <strong><?= implode(", ", $mejores) ?></strong> (<?= $calif_max ?>)
    </div>

    <div class="alert alert-warning">
        Peores alumnos: <strong><?= implode(", ", $peores) ?></strong> (<?= $calif_min ?>)
    </div>

    <div class="alert alert-danger">
        Alumnos reprobados:
        <ul>
            <?php foreach ($reprobados as $rep): ?>
                <li><?= htmlspecialchars($rep) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if (!isset($_GET['pdf'])): ?>
    <div class="text-center mt-4 no-print">
        <a href="calificaciones.php" class="btn btn-primary me-2">Modificar Calificaciones</a>
        <a href="generar_pdf.php?archivo=estadisticas" class="btn btn-success me-2" target="_blank">Generar PDF</a>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
    <?php endif; ?>
</div>

<?php if (!isset($_GET['pdf'])): ?>
<footer class="text-center mt-5">
    <p>&copy; 2025 Raíces del Saber | Todos los derechos reservados</p>
</footer>
<?php endif; ?>
</body>
</html>
<?php if (isset($_GET['pdf']) && $_GET['pdf'] == '1') echo '</page>'; ?>
