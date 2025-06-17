<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Conexión
$host = 'localhost';
$db = 'calificaciones';
$user = 'root';
$pass = 'lake18'; // <- asegurarse que coincida
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Obtener alumnos
$alumnos = $mysqli->query("SELECT id, nombre FROM alumnos");

// Guardar calificaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alumno_id'])) {
    $alumno_id = intval($_POST['alumno_id']);
    $calificaciones = $_POST['calificaciones'];

    // Generar dinámicamente el SQL
    $set_clauses = [];
    foreach ($calificaciones as $materia => $calificacion) {
        $materia = $mysqli->real_escape_string(strtolower($materia));
        $calificacion = intval($calificacion);
        $set_clauses[] = "`$materia` = $calificacion";

    }

    $set_sql = implode(", ", $set_clauses);
    $update_sql = "UPDATE alumnos SET $set_sql WHERE id = $alumno_id";

    if ($mysqli->query($update_sql)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
       header("Location: estadisticas.php?status=success");
exit;
    } else {
        echo "<script>Swal.fire('Error', 'No se pudo guardar: " . $mysqli->error . "', 'error')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Calificaciones | Raíces del Saber</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    .calificaciones-box {
      background-color: #fff;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      width: 85%;
      max-width: 950px;
      margin: auto;
      min-height: 280px;
    }

    .form-control {
      border-radius: 8px;
      transition: 0.3s;
    }

    .form-control:focus {
      border-color: var(--color-primario);
      box-shadow: 0 0 8px var(--color-secundario);
    }

    .btn-primary, .btn-danger {
      border: none;
      transition: 0.3s;
      padding: 6px 10px;
      font-size: 14px;
    }

    .btn-primary {
      background-color: var(--color-primario);
    }

    .btn-primary:hover {
      background-color: var(--color-secundario);
    }

    .btn-danger {
      background-color: #C0392B;
    }

    .btn-danger:hover {
      background-color: #A93226;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed; /* Hace que ambas columnas sean del mismo tamaño */
    }

    th, td {
      border: 1px solid var(--color-secundario);
      padding: 6px;
      text-align: center;
      width: 50%; /* Distribución equitativa entre "Materia" y "Calificación" */
    }

    th {
      background-color: var(--color-primario);
      color: white;
    }

    select {
      padding: 5px;
      width: 80px;
      border-radius: 5px;
      border: 1px solid var(--color-primario);
    }

    .form-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
      width: 100%; /* Asegura alineación con la tabla */
    }

    .form-buttons .btn {
      width: 50%;
      text-align: center;
    }

    footer {
      text-align: center;
      padding: 10px;
      background-color: var(--color-primario);
      color: white;
      position: fixed;
      bottom: 0;
      width: 100%;
    }
  </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100 form-container">
<div class="calificaciones-box">
    <h2 class="text-center mb-2">Registro de Calificaciones</h2>
    <p class="text-center"><strong>Bienvenido Profesor Valdepeña</strong></p>

    <form method="post">
        <div class="mb-3 text-center">
            <label><strong>Alumno:</strong></label>
            <select name="alumno_id" class="form-select w-50 mx-auto" required>
                <option value="">-- Selecciona alumno --</option>
                <?php while ($row = $alumnos->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <table>
            <tr>
                <th>Materia</th>
                <th>Calificación</th>
            </tr>
            <?php
           $materias = [
    'calificacion_espanol',
    'calificacion_matematicas',
    'calificacion_ingles',
    'calificacion_historia',
    'calificacion_computacion',
    'calificacion_geografia',
    'calificacion_biologia'
];

            foreach ($materias as $materia): ?>
                <tr>
                    <td><?= $materia ?></td>
                    <td>
                        <select name="calificaciones[<?= $materia ?>]" class="form-select" required>
                            <option value="">--Selecciona--</option>
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="form-buttons mt-3">
            <input type="reset" value="Restablecer" class="btn btn-danger btn-sm">
            <input type="submit" value="Enviar" class="btn btn-primary btn-sm">
        </div>
    </form>

    <form method="post" action="logout.php" class="mt-3 text-center">
        <button type="submit" class="btn btn-outline-dark btn-sm">Cerrar sesión</button>
    </form>
</div>
</div>
<footer>
    <p>&copy; 2025 Raíces del Saber | Todos los derechos reservados</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
