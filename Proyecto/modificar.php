<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['activa']) || $_SESSION['usuario']['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

$conexion = new mysqli("localhost", "root", "lake18", "calificaciones");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Eliminar usuario
if (isset($_GET['eliminar']) && isset($_GET['tipo'])) {
    $id = intval($_GET['eliminar']);
    $tipo = $_GET['tipo'];

    if ($tipo === 'alumno') {
        $conexion->query("DELETE FROM usuarios WHERE id_alumno = $id");
        $conexion->query("DELETE FROM alumnos WHERE id = $id");
    } elseif ($tipo === 'profesor') {
        $conexion->query("DELETE FROM usuarios WHERE id_profesor = $id");
        $conexion->query("DELETE FROM profesores WHERE id = $id");
    }
    header("Location: modificar.php");
    exit;
}

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['tipo'] === 'alumno') {
        $stmt = $conexion->prepare("UPDATE alumnos SET nombre=?, grado=?, grupo=?, edad=?, turno=? WHERE id=?");
        $stmt->bind_param("sssisi", $_POST['nombre'], $_POST['grado'], $_POST['grupo'], $_POST['edad'], $_POST['turno'], $_POST['id']);
        $stmt->execute();
        $stmt->close();

        $stmt2 = $conexion->prepare("UPDATE usuarios SET username=?, password=? WHERE id_alumno=?");
        $stmt2->bind_param("ssi", $_POST['usuario'], $_POST['password'], $_POST['id']);
        $stmt2->execute();
        $stmt2->close();

    } elseif ($_POST['tipo'] === 'profesor') {
        $stmt = $conexion->prepare("UPDATE profesores SET nombre=?, grado=?, grupo=?, turno=?, materia=? WHERE id=?");
        $stmt->bind_param("sssssi", $_POST['nombre'], $_POST['grado'], $_POST['grupo'], $_POST['turno'], $_POST['materia'], $_POST['id']);
        $stmt->execute();
        $stmt->close();

        $stmt2 = $conexion->prepare("UPDATE usuarios SET username=?, password=? WHERE id_profesor=?");
        $stmt2->bind_param("ssi", $_POST['usuario'], $_POST['password'], $_POST['id']);
        $stmt2->execute();
        $stmt2->close();
    }
    header("Location: modificar.php");
    exit;
}

$alumnos = $conexion->query("SELECT a.*, u.username, u.password FROM alumnos a JOIN usuarios u ON a.id = u.id_alumno ORDER BY grado, grupo");
$profesores = $conexion->query("SELECT p.*, u.username, u.password FROM profesores p JOIN usuarios u ON p.id = u.id_profesor ORDER BY grado, grupo");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Modificar Usuarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
      margin-bottom: 20px;
    }

    .navbar a {
      color: white !important;
    }

    .dark-mode {
      --color-primario: #8B523A;
      --color-secundario: #B37D5A;
      --color-fondo: #E3C2A5;
      --color-texto: #3F2C1F;
    }
  </style>

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
</head>
<body class="container py-4">
  <!-- Botón Día/Noche -->
  <button class="btn-toggle" onclick="toggleMode()">Día/Noche</button>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container d-flex align-items-center">
      <a href="#" onclick="toggleMode(event)" class="me-3 order-first">
        <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
      </a>
      <span class="navbar-brand mb-0 h1">Modificar Usuarios</span>
    </div>
  </nav>

  <div class="mb-4">
    <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    <a href="admin.php" class="btn btn-success">Agregar usuarios</a>
    <a href="resultados.php" class="btn btn-primary">Ver resultados</a>
  </div>

  <h4>Alumnos</h4>
  <?php while ($row = $alumnos->fetch_assoc()): ?>
    <form method="POST" class="border p-3 mb-3">
      <input type="hidden" name="tipo" value="alumno">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="row mb-2">
        <div class="col-md-2">
          <label>Nombre</label>
          <input name="nombre" value="<?= $row['nombre'] ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label>Username</label>
          <input name="usuario" value="<?= $row['username'] ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label>Contraseña</label>
          <input name="password" value="<?= $row['password'] ?>" class="form-control" required>
        </div>
        <div class="col-md-1">
          <label>Grado</label>
          <select name="grado" class="form-select">
            <?php for ($i = 1; $i <= 6; $i++) echo "<option value='$i'" . ($row['grado'] == $i ? ' selected' : '') . ">$i</option>"; ?>
          </select>
        </div>
        <div class="col-md-1">
          <label>Grupo</label>
          <select name="grupo" class="form-select">
            <?php foreach (["A", "B", "C"] as $g) echo "<option value='$g'" . ($row['grupo'] == $g ? ' selected' : '') . ">$g</option>"; ?>
          </select>
        </div>
        <div class="col-md-1">
          <label>Edad</label>
          <input name="edad" value="<?= $row['edad'] ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label>Turno</label>
          <select name="turno" class="form-select">
            <option value="matutino" <?= $row['turno'] == 'matutino' ? 'selected' : '' ?>>Matutino</option>
            <option value="vespertino" <?= $row['turno'] == 'vespertino' ? 'selected' : '' ?>>Vespertino</option>
          </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" class="btn btn-warning w-100">Guardar</button>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <a href="modificar.php?eliminar=<?= $row['id'] ?>&tipo=alumno" class="btn btn-outline-danger w-100">Eliminar</a>
        </div>
      </div>
    </form>
  <?php endwhile; ?>

  <h4>Profesores</h4>
  <?php while ($row = $profesores->fetch_assoc()): ?>
    <form method="POST" class="border p-3 mb-3">
      <input type="hidden" name="tipo" value="profesor">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="row mb-2">
        <div class="col-md-2">
          <label>Nombre</label>
          <input name="nombre" value="<?= $row['nombre'] ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label>Username</label>
          <input name="usuario" value="<?= $row['username'] ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label>Contraseña</label>
          <input name="password" value="<?= $row['password'] ?>" class="form-control" required>
        </div>
        <div class="col-md-1">
          <label>Grado</label>
          <select name="grado" class="form-select">
            <?php for ($i = 1; $i <= 6; $i++) echo "<option value='$i'" . ($row['grado'] == $i ? ' selected' : '') . ">$i</option>"; ?>
          </select>
        </div>
        <div class="col-md-1">
          <label>Grupo</label>
          <select name="grupo" class="form-select">
            <?php foreach (["A", "B", "C"] as $g) echo "<option value='$g'" . ($row['grupo'] == $g ? ' selected' : '') . ">$g</option>"; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label>Turno</label>
          <select name="turno" class="form-select">
            <option value="matutino" <?= $row['turno'] == 'matutino' ? 'selected' : '' ?>>Matutino</option>
            <option value="vespertino" <?= $row['turno'] == 'vespertino' ? 'selected' : '' ?>>Vespertino</option>
          </select>
        </div>
        <div class="col-md-2">
          <label>Materia</label>
          <select name="materia" class="form-select">
            <?php foreach (["español", "matematicas", "ingles", "historia", "computacion", "geografia", "biologia"] as $m) echo "<option value='$m'" . ($row['materia'] == $m ? ' selected' : '') . ">$m</option>"; ?>
          </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" class="btn btn-warning w-100">Guardar</button>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <a href="modificar.php?eliminar=<?= $row['id'] ?>&tipo=profesor" class="btn btn-outline-danger w-100">Eliminar</a>
        </div>
      </div>
    </form>
  <?php endwhile; ?>
</body>
</html>
