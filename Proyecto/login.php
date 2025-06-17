<?php
session_start();

function validaLogin($username, $clave) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=calificaciones", "root", "lake18");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = :username');
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $clave === $usuario["password"]) {
            return $usuario;
        } else {
            return false;
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

if (isset($_POST["username"]) && isset($_POST["clave"])) {
    $usuario = validaLogin($_POST["username"], $_POST["clave"]); 

    if ($usuario) {
        $_SESSION["activa"] = true;
        $_SESSION["usuario"] = $usuario;

        if ($usuario['tipo_usuario'] === 'profesor') {
            header("Location: calificaciones.php");
            exit;
        } elseif ($usuario['tipo_usuario'] === 'alumno') {
            header("Location: perfil_alumno.php");
            exit;
        } else {
            echo "Tipo de usuario desconocido.";
        }

    } else {
        echo "Usuario o contraseña incorrectos. Intenta de nuevo.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Acceso Escolar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

  // Ejecutar cuando se cargue todo el DOM
  document.addEventListener("DOMContentLoaded", applySavedTheme);
</script>




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
    }

    .btn-toggle:hover {
      background-color: var(--color-secundario);
    }

    /* Modo oscuro */
    .dark-mode {
      --color-primario: #8B523A;  /* Más oscuro que #D98860 */
      --color-secundario: #B37D5A; /* Más oscuro que #F4C095 */
      --color-fondo: #E3C2A5;      /* Más apagado que #FFEEDB */
      --color-texto: #3F2C1F;      /* Más oscuro que #5F4B32 */
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
    for(let i = 0; i < ca.length; i++) {
      let c = ca[i].trim();
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
  }

  function validarFormulario(event) {
    event.preventDefault();

    let username = document.getElementById("username").value;
    let clave = document.getElementById("clave").value;

    let usernameCorrecto = "123456";
    let claveCorrecta = "password123";

    if (username !== usernameCorrecto) {
      Swal.fire({
        icon: "error",
        title: "Usuario incorrecto",
        text: "Verifica tu nombre de usuario e intenta nuevamente.",
        confirmButtonColor: "#D98860"
      });
      return;
    }

    if (clave !== claveCorrecta) {
      Swal.fire({
        icon: "error",
        title: "Contraseña incorrecta",
        text: "Verifica tu contraseña e intenta nuevamente.",
        confirmButtonColor: "#D98860"
      });
      return;
    }

    // Guardar datos en cookies
    setCookie("username", username, 7);
    setCookie("clave", clave, 7);

    Swal.fire({
      icon: "success",
      title: "Acceso concedido",
      text: "Tus datos han sido guardados.",
      confirmButtonColor: "#D98860"
    }).then(() => {
      event.target.submit();
    });
  }

  window.onload = function() {
    document.getElementById("username").value = getCookie("username") || "";
    document.getElementById("clave").value = getCookie("clave") || "";
  };

  function borrarCookies() {
    setCookie("username", "", -1);
    setCookie("clave", "", -1);
    Swal.fire({
      icon: "info",
      title: "Sesión cerrada",
      text: "Las cookies han sido eliminadas.",
      confirmButtonColor: "#D98860"
    });
  }
</script>

</head>
<body>
  <button class="btn-toggle" onclick="toggleMode()">Día/Noche</button>

  <nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container d-flex align-items-center">
    <a href="#" onclick="toggleMode(event)" class="me-3 order-first">
      <img id="mode-icon" src="moon_icon.png" alt="Modo oscuro" width="30" height="30">
    </a>
    <a class="navbar-brand text-white fw-bold ms-3" href="#">Raíces del Saber</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link text-white" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="reglamento.html">Reglamento</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="ayuda.html">Ayuda</a></li>
      </ul>
    </div>
  </div>
</nav>

  <div class="d-flex justify-content-center align-items-center vh-100 form-container">
    <div class="login-box">
      <img src="tree_icon.png" alt="Logo" width="180" height="180" class="mb-3 d-block mx-auto">
      <h4 class="mb-3 text-center">Acceso a la Plataforma Escolar</h4>
      <form method="post">
  <div class="mb-3">
    <label for="username" class="form-label">Usuario</label>
    <input type="text" class="form-control" id="username" name="username" required>
  </div>
  <div class="mb-3">
    <label for="clave" class="form-label">Contraseña</label>
    <input type="password" class="form-control" id="clave" name="clave" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Ingresar</button>
</form>

      <button onclick="borrarCookies()" class="btn btn-danger w-100 mt-3">Cerrar sesión</button>
    </div>
  </div>

  <footer><p>&copy; 2025 Raíces del Saber | Todos los derechos reservados</p></footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>