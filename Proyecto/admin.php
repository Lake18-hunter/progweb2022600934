<?php
session_start();

if (!isset($_SESSION['activa']) || $_SESSION['usuario']['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "lake18", "calificaciones");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_usuario'])) {
    $tipo_usuario = $_POST['tipo_usuario'];

    if ($tipo_usuario === 'profesor') {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $grupo = $_POST['grupo'];
        $grado = $_POST['grado'];
        $turno = $_POST['turno'];
        $materia = $_POST['materia'];

        $stmt = $conn->prepare("INSERT INTO profesores (nombre, grado, turno, grupo, materia) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $grado, $turno, $grupo, $materia);
        $stmt->execute();
        $stmt->close();

        // CONTRASEÑA SIN HASH
        $stmt_user = $conn->prepare("INSERT INTO usuarios (username, password, tipo_usuario, id_profesor) VALUES (?, ?, ?, LAST_INSERT_ID())");
        $stmt_user->bind_param("sss", $usuario, $password, $tipo_usuario);
        $stmt_user->execute();
        $stmt_user->close();

    } elseif ($tipo_usuario === 'alumno') {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $grupo = $_POST['grupo'];
        $edad = $_POST['edad'];
        $grado = $_POST['grado'];
        $turno = $_POST['turno'];

        $stmt = $conn->prepare("INSERT INTO alumnos (nombre, edad, grado, turno, grupo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $nombre, $edad, $grado, $turno, $grupo);
        $stmt->execute();
        $stmt->close();

        // CONTRASEÑA SIN HASH
        $stmt_user = $conn->prepare("INSERT INTO usuarios (username, password, tipo_usuario, id_alumno) VALUES (?, ?, ?, LAST_INSERT_ID())");
        $stmt_user->bind_param("sss", $usuario, $password, $tipo_usuario);
        $stmt_user->execute();
        $stmt_user->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de Administración</title>
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

    /* Modo oscuro */
    .dark-mode {
      --color-primario: #8B523A;
      --color-secundario: #B37D5A;
      --color-fondo: #E3C2A5;
      --color-texto: #3F2C1F;
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

    .login-box {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .form-control {
      border-radius: 8px;
      transition: 0.3s;
    }

    .form-control:focus {
      border-color: var(--color-primario);
      box-shadow: 0 0 8px var(--color-secundario);
    }

    .btn-primary {
      background-color: var(--color-primario);
      border: none;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: var(--color-secundario);
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
        document.getElementById("mode-icon").src = "sun_icon.png";
        document.getElementById("mode-icon").alt = "Modo claro";
      }
    };
  </script>
</head>
<body>

  <!-- Botón Día/Noche -->
  <button class="btn-toggle" onclick="toggleMode()">Día/Noche</button>

  <!-- Navbar con icono -->
  <nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container d-flex align-items-center">
      <a href="#" onclick="toggleMode()" class="me-3 order-first">
        <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
      </a>
      <span class="navbar-brand">Panel de Administración</span>
    </div>
  </nav>

  <div class="container mt-5 pt-4">
    <h2 class="text-center mb-4">Gestión de Usuarios</h2>

    <div class="mb-3 text-center">
      <a href="modificar.php" class="btn btn-warning me-2">Modificar Usuario</a>
      <a href="resultados.php" class="btn btn-success me-2">Enviar</a>
      <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>
<div class="row">
      <!-- Profesores -->
      <div class="col-md-6">
        <div class="card p-3 mb-4">
          <h4 class="text-center">Registrar Profesor</h4>
          <form action="admin.php" method="POST">
            <div class="mb-2"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
            <div class="mb-2"><label>Usuario</label><input type="text" name="usuario" class="form-control" required></div>
            <div class="mb-2"><label>Contraseña</label><input type="password" name="password" class="form-control" required></div>

            <div class="mb-2">
              <label>Grupo</label>
              <select name="grupo" class="form-select" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
              </select>
            </div>

            <div class="mb-2">
              <label>Grado</label>
              <select name="grado" class="form-select" required>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="mb-2">
              <label>Turno</label>
              <select name="turno" class="form-select" required>
                <option value="matutino">Matutino</option>
                <option value="vespertino">Vespertino</option>
              </select>
            </div>

            <div class="mb-3">
              <label>Materia</label>
              <select name="materia" class="form-select" required>
                <option value="español">Español</option>
                <option value="matematicas">Matemáticas</option>
                <option value="ingles">Inglés</option>
                <option value="historia">Historia</option>
                <option value="computacion">Computación</option>
                <option value="geografia">Geografía</option>
                <option value="biologia">Biología</option>
              </select>
            </div>

            <input type="hidden" name="tipo_usuario" value="profesor">
            <button type="submit" class="btn btn-primary w-100">Agregar Profesor</button>
          </form>
        </div>
      </div>

      <!-- Alumnos -->
      <div class="col-md-6">
        <div class="card p-3 mb-4">
          <h4 class="text-center">Registrar Alumno</h4>
          <form action="admin.php" method="POST">
            <div class="mb-2"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
            <div class="mb-2"><label>Usuario</label><input type="text" name="usuario" class="form-control" required></div>
            <div class="mb-2"><label>Contraseña</label><input type="password" name="password" class="form-control" required></div>

            <div class="mb-2">
              <label>Grupo</label>
              <select name="grupo" class="form-select" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
              </select>
            </div>

            <div class="mb-2"><label>Edad</label><input type="number" name="edad" class="form-control" required></div>

            <div class="mb-2">
              <label>Grado</label>
              <select name="grado" class="form-select" required>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="mb-3">
              <label>Turno</label>
              <select name="turno" class="form-select" required>
                <option value="matutino">Matutino</option>
                <option value="vespertino">Vespertino</option>
              </select>
            </div>
            

            <input type="hidden" name="tipo_usuario" value="alumno">
            <button type="submit" class="btn btn-primary w-100">Agregar Alumno</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center mt-3 text-muted">
    &copy; 2025 Raíces del Saber | Todos los derechos reservados
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
