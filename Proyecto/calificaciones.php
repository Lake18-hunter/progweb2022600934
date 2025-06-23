<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$db = 'calificaciones';
$user = 'root';
$pass = 'lake18';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$profesor_id = $_SESSION['usuario']['id_profesor'];
$result = $mysqli->query("SELECT grado, grupo, materia FROM profesores WHERE id = $profesor_id");
$datos_profesor = $result->fetch_assoc();

$grado = $mysqli->real_escape_string($datos_profesor['grado']);
$grupo = $mysqli->real_escape_string($datos_profesor['grupo']);
$materia = strtolower($datos_profesor['materia']);

$alumnos = $mysqli->query("SELECT id, nombre FROM alumnos WHERE grado = '$grado' AND grupo = '$grupo'");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alumno_id'], $_POST['calificacion'])) {
    $alumno_id = intval($_POST['alumno_id']);
    $calificacion = intval($_POST['calificacion']);
    $columna_materia = 'calificacion_' . $materia;

    $stmt = $mysqli->prepare("UPDATE alumnos SET $columna_materia = ? WHERE id = ?");
    $stmt->bind_param('ii', $calificacion, $alumno_id);
    if ($stmt->execute()) {
        header("Location: estadisticas.php?status=success");
        exit;
    } else {
        echo "<script>alert('Error al guardar calificacion.');</script>";
    }
    $stmt->close();
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
    /* Modo claro por defecto */
    :root {
      --color-primario: #D98860;
      --color-secundario: #F4C095;
      --color-fondo: #FFEEDB;
      --color-texto: #5F4B32;
    }

    /* Modo oscuro */
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

    .navbar {
      background-color: var(--color-primario);
    }

    .navbar a {
      color: white !important;
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

    footer {
      background-color: var(--color-primario);
      color: white;
    }
  </style>

  <script>
    // Función para alternar entre modo claro y oscuro
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

    // Función para establecer cookies
    function setCookie(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
      document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/";
    }

    // Función para obtener cookies
    function getCookie(name) {
      const cookies = document.cookie.split(";");
      for (let c of cookies) {
        const [key, val] = c.trim().split("=");
        if (key === name) return val;
      }
      return null;
    }

    // Función para cargar la preferencia de tema desde las cookies
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

</head>
<body>
  <div class="container py-5">
    <!-- Botón Día/Noche -->
    <button class="btn-toggle" onclick="toggleMode()">Día/Noche</button>

    <!-- Navbar con ícono de modo -->
    <nav class="navbar navbar-expand-lg shadow-sm mb-4">
      <div class="container d-flex align-items-center">
        <a href="#" onclick="toggleMode(event)" class="me-3 order-first">
          <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
        </a>
        <span class="navbar-brand mb-0 h1">Registro de Calificaciones</span>
      </div>
    </nav>

    <div class="text-center mb-4">
      <h2>Registro de Calificaciones</h2>
      <p><strong>Bienvenido Profesor <?= htmlspecialchars($_SESSION['usuario']['username']) ?></strong></p>
      <p><strong>Grado:</strong> <?= htmlspecialchars($grado) ?> | <strong>Grupo:</strong> <?= htmlspecialchars($grupo) ?> | <strong>Materia:</strong> <?= htmlspecialchars($datos_profesor['materia']) ?></p>
    </div>

    <form method="post" class="border rounded p-4 bg-light">
      <div class="mb-3">
        <label for="alumno" class="form-label">Selecciona un alumno:</label>
        <select name="alumno_id" class="form-select" required>
          <option value="">-- Selecciona alumno --</option>
          <?php while ($row = $alumnos->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="calificacion" class="form-label">Calificación para <?= htmlspecialchars($datos_profesor['materia']) ?>:</label>
        <select name="calificacion" class="form-select" required>
          <option value="">--Selecciona--</option>
          <?php for ($i = 0; $i <= 10; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="d-flex justify-content-between">
        <button type="reset" class="btn btn-danger">Restablecer</button>
        <button type="submit" class="btn btn-primary">Enviar</button>
      </div>
    </form>

    <div class="mt-4 d-flex justify-content-center gap-3">
      <form method="post" action="logout.php">
        <button type="submit" class="btn btn-outline-dark">Cerrar sesión</button>
      </form>
    </div>
  </div>

  <footer class="text-center mt-5">
    <p>&copy; 2025 Raíces del Saber | Todos los derechos reservados</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
