<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['activa']) || $_SESSION['usuario']['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "lake18", "calificaciones");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar alumnos
$alumnos_result = $conn->query("SELECT * FROM alumnos ORDER BY grado ASC, grupo ASC, nombre ASC");

// Consultar profesores
$profesores_result = $conn->query("SELECT * FROM profesores ORDER BY grado ASC, grupo ASC, nombre ASC");

$conn->close();
?>
<?php if (isset($_GET['pdf']) && $_GET['pdf'] == '1'): ?>
<page backtop="20mm" backbottom="20mm" backleft="10mm" backright="10mm">
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios Registrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
    }

    .navbar a {
      color: white !important;
    }

    footer {
      text-align: center;
      padding: 10px;
      background-color: var(--color-primario);
      color: white;
      margin-top: 50px;
    }

    .dark-mode {
      --color-primario: #8B523A;
      --color-secundario: #B37D5A;
      --color-fondo: #E3C2A5;
      --color-texto: #3F2C1F;
    }

    @media print {
      .no-print { display: none; }
    }
  </style>

  <?php if (!isset($_GET['pdf'])): ?>
  <script>
    function toggleMode() {
      const body = document.body;
      const icon = document.getElementById("mode-icon");
      const isDark = body.classList.toggle("dark-mode");

      icon.src = isDark ? "sun_icon.png" : "moon_icon.png";
      icon.alt = isDark ? "Modo claro" : "Modo oscuro";

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
  <nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container d-flex align-items-center">
      <a href="#" onclick="toggleMode()" class="me-3 order-first">
        <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
      </a>
    </div>
  </nav>
  <?php endif; ?>

  <div class="container mt-5 pt-4">
    <h2 class="text-center mb-4">Usuarios Registrados</h2>

    <!-- BOTONES -->
    <div class="mb-3 text-center">
      <a href="admin.php" class="btn btn-primary me-2">Agregar Usuario</a>
      <a href="modificar.php" class="btn btn-warning me-2">Modificar Usuario</a>
      <?php if (!isset($_GET['pdf'])): ?>
      <a href="generar_pdf.php?archivo=resultados" class="btn btn-success me-2" target="_blank">Generar PDF</a>
      <?php endif; ?>
      <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>

    <!-- ALUMNOS -->
    <div class="card mb-4">
      <div class="card-header bg-info text-white"><h5 class="mb-0">Alumnos</h5></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th>
              <th>Edad</th>
              <th>Grado</th>
              <th>Grupo</th>
              <th>Turno</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($alumnos_result->num_rows > 0): ?>
              <?php while ($alumno = $alumnos_result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($alumno['nombre']) ?></td>
                  <td><?= htmlspecialchars($alumno['edad']) ?></td>
                  <td><?= htmlspecialchars($alumno['grado']) ?></td>
                  <td><?= htmlspecialchars($alumno['grupo']) ?></td>
                  <td><?= htmlspecialchars($alumno['turno']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">No hay alumnos registrados</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PROFESORES -->
    <div class="card">
      <div class="card-header bg-success text-white"><h5 class="mb-0">Profesores</h5></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th>
              <th>Materia</th>
              <th>Grado</th>
              <th>Grupo</th>
              <th>Turno</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($profesores_result->num_rows > 0): ?>
              <?php while ($profesor = $profesores_result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($profesor['nombre']) ?></td>
                  <td><?= htmlspecialchars($profesor['materia']) ?></td>
                  <td><?= htmlspecialchars($profesor['grado']) ?></td>
                  <td><?= htmlspecialchars($profesor['grupo']) ?></td>
                  <td><?= htmlspecialchars($profesor['turno']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">No hay profesores registrados</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mb-5"></div>

  <?php if (!isset($_GET['pdf'])): ?>
  <footer class="text-center mt-3 text-muted">
    &copy; 2025 Raíces del Saber | Todos los derechos reservados
  </footer>
  <?php endif; ?>
</body>

<?php if (isset($_GET['pdf']) && $_GET['pdf'] == '1'): ?>
</page>
<?php endif; ?>
</html>
