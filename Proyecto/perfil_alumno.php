<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'alumno') {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$id_alumno = $usuario['id_alumno'];

// Conexión a la base de datos
$mysqli = new mysqli("localhost", "root", "lake18", "calificaciones");
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Obtener los datos del alumno
$stmt = $mysqli->prepare("SELECT nombre, calificacion_espanol, calificacion_matematicas, calificacion_ingles, calificacion_historia, calificacion_computacion, calificacion_geografia, calificacion_biologia FROM alumnos WHERE id = ?");
$stmt->bind_param("i", $id_alumno);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();
$stmt->close();
$mysqli->close();

$calificaciones = [
    'Español' => $datos['calificacion_espanol'],
    'Matemáticas' => $datos['calificacion_matematicas'],
    'Inglés' => $datos['calificacion_ingles'],
    'Historia' => $datos['calificacion_historia'],
    'Computación' => $datos['calificacion_computacion'],
    'Geografía' => $datos['calificacion_geografia'],
    'Biología' => $datos['calificacion_biologia']
];

$mayor = max($calificaciones);
$menor = min($calificaciones);
?>

<?php if (isset($_GET['pdf']) && $_GET['pdf'] == '1'): ?>
<page backtop="20mm" backbottom="20mm" backleft="10mm" backright="10mm">
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil del Alumno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --color-primario: #D98860;
      --color-secundario: #F4C095;
      --color-fondo: #FFEEDB;
      --color-texto: #5F4B32;
    }

    body {
      background: linear-gradient(135deg, var(--color-fondo), var(--color-secundario));
      color: var(--color-texto);
      padding-top: 80px;
    }

    .dark-mode {
      --color-primario: #8B523A;
      --color-secundario: #B37D5A;
      --color-fondo: #E3C2A5;
      --color-texto: #3F2C1F;
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
      z-index: 1050;
    }

    .btn-toggle:hover {
      background-color: var(--color-secundario);
    }

    .navbar {
      background-color: var(--color-primario);
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar a {
      color: white !important;
    }

    .table th {
      background-color: var(--color-primario);
      color: white;
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

  <?php if (!isset($_GET['pdf'])): ?>
  <script>
    function setCookie(name, value, days) {
      let expires = "";
      if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + value + expires + "; path=/";
    }

    function getCookie(name) {
      let nameEQ = name + "=";
      let ca = document.cookie.split(";");
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
      }
      return null;
    }

    function applySavedTheme() {
      const savedTheme = getCookie("theme");
      const body = document.body;
      const icon = document.getElementById("mode-icon");

      if (savedTheme === "dark") {
        body.classList.add("dark-mode");
        if (icon) icon.src = "sun_icon.png";
      } else {
        body.classList.remove("dark-mode");
        if (icon) icon.src = "moon_icon.png";
      }
    }

    function toggleMode(event) {
      event.preventDefault();
      const body = document.body;
      const icon = document.getElementById("mode-icon");
      const isDark = body.classList.toggle("dark-mode");

      icon.src = isDark ? "sun_icon.png" : "moon_icon.png";
      setCookie("theme", isDark ? "dark" : "light", 7);
    }

    document.addEventListener("DOMContentLoaded", applySavedTheme);
  </script>
  <?php endif; ?>
</head>

<body>
  <?php if (!isset($_GET['pdf'])): ?>
  <button class="btn-toggle" onclick="toggleMode(event)">Día/Noche</button>

  <nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container d-flex align-items-center">
      <a href="#" onclick="toggleMode(event)" class="me-3 order-first">
        <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
      </a>
      <a class="navbar-brand text-white fw-bold ms-3" href="#">Raíces del Saber</a>
    </div>
  </nav>
  <?php endif; ?>

  <div class="container mt-5">
    <h2 class="text-center mb-4">Bienvenido, <?= htmlspecialchars($datos['nombre']) ?></h2>
    <h4 class="text-center">Tus Calificaciones</h4>

    <table class="table table-bordered table-striped mt-4">
      <thead>
        <tr>
          <th>Materia</th>
          <th>Calificación</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($calificaciones as $materia => $valor): ?>
          <tr>
            <td><?= htmlspecialchars($materia) ?></td>
            <td><?= htmlspecialchars($valor) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="mt-3">
      <p><strong>Calificación más alta:</strong> <?= $mayor ?></p>
      <p><strong>Calificación más baja:</strong> <?= $menor ?></p>
    </div>

    <?php if (!isset($_GET['pdf'])): ?>
    <div class="text-center mt-4">
      <a href="generar_pdf.php?archivo=perfil_alumno" class="btn btn-success mb-3" target="_blank">Generar PDF</a>
      <form method="post" action="logout.php">
        <button type="submit" class="btn btn-danger w-100">Cerrar sesión</button>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!isset($_GET['pdf'])): ?>
  <footer><p>&copy; 2025 Raíces del Saber | Todos los derechos reservados</p></footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php endif; ?>
</body>
<?php if (isset($_GET['pdf']) && $_GET['pdf'] == '1'): ?>
</page>
<?php endif; ?>
</html>
