<?php
session_start();
require_once 'conexion.php'; // conexión a MySQL

$mensaje = "";
$modo = "login"; // por defecto mostramos login

// ================= LOGIN =================
if(isset($_POST['iniciar'])){
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // PREPARAR CONSULTA LOGIN
    $stmt = $conexion->prepare("SELECT * FROM INICIO WHERE usuario=? LIMIT 1");
    if(!$stmt){
        die("Error en la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows == 1){
        $row = $resultado->fetch_assoc();

        if($row['password'] == $password){
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['tipo'] = $row['tipo'];

            // Registrar login exitoso
            $stmt2 = $conexion->prepare("INSERT INTO logins(usuario, fecha_hora, exito) VALUES(?, NOW(), 1)");
            $stmt2->bind_param("s", $usuario);
            $stmt2->execute();
            $stmt2->close();

            if($row['tipo'] === "admin"){
    header("Location: admin.html");
} else {
    header("Location: index.html");
}
exit;

            exit;
        } else {
            $mensaje = "Contraseña incorrecta.";

            $stmt2 = $conexion->prepare("INSERT INTO logins(usuario, fecha_hora, exito) VALUES(?, NOW(), 0)");
            $stmt2->bind_param("s", $usuario);
            $stmt2->execute();
            $stmt2->close();
        }
    } else {
        $mensaje = "Usuario no encontrado.";

        $stmt2 = $conexion->prepare("INSERT INTO logins(usuario, fecha_hora, exito) VALUES(?, NOW(), 0)");
       $stmt2 = $conexion->prepare("INSERT INTO logins(usuario, fecha_hora, exito) VALUES(?, NOW(), 1)");
        $stmt2->execute();
        $stmt2->close();
    }
}

// ================= MODO REGISTRO =================
if(isset($_POST['modo']) && $_POST['modo'] === "registro"){
    $modo = "registro"; // mostrar formulario de registro
}

// ================= REGISTRO =================
if(isset($_POST['registrar'])){
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $tipo = $_POST['tipo'];

    // Verificar si el usuario ya existe
    $stmt_check = $conexion->prepare("SELECT * FROM INICIO WHERE usuario=? LIMIT 1");
    if(!$stmt_check){
        die("Error en la consulta: " . $conn->error);
    }
    $stmt_check->bind_param("s", $usuario);
    $stmt_check->execute();
    $resultado_check = $stmt_check->get_result();

    if($resultado_check->num_rows > 0){
        $mensaje = "El usuario ya existe.";
        $modo = "registro"; 
    } else {
        // Insertar nuevo usuario
        $stmt_insert = $conexion->prepare("INSERT INTO INICIO(nombre, usuario, password, tipo) VALUES(?, ?, ?, ?)");
        if(!$stmt_insert){
            die("Error al preparar INSERT: " . $conn->error);
        }
        $stmt_insert->bind_param("ssss", $nombre, $usuario, $password, $tipo);
        if($stmt_insert->execute()){
    if($tipo === "admin"){
        header("Location: admin.html");
        exit;
    } else {
        $mensaje = "Usuario registrado correctamente. Ahora puedes iniciar sesión.";
        $modo = "login"; 
    }
} else {
    $mensaje = "Error al registrar el usuario: " . $stmt_insert->error;
    $modo = "registro";
}

        $stmt_insert->close();
    }
    $stmt_check->close();
}


$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Registro</title>
<style>
.login-seccion { display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f0f4f8; }
.login-container { background:#fff; padding:45px 40px; border-radius:15px; width:100%; max-width:400px; box-shadow:0 12px 25px rgba(0,0,0,0.2); text-align:center; }
.login-container h2 { font-size:28px; margin-bottom:20px; color:#1a73e8; }
.login-container label { display:block; text-align:left; margin:10px 0 5px; color:#333; }
.login-container input, .login-container select, .login-container button { width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:none; font-size:15px; }
.login-container input, .login-container select { background:#e9ecef; color:#333; }
.login-container button { background:#1a73e8; color:#fff; cursor:pointer; }
.login-container button:hover { background:#155ab6; }
.mensaje-error { background:#e53e3e; color:#fff; padding:10px; border-radius:5px; margin-bottom:10px; }
.toggle-btn { background:transparent; border:2px solid #1a73e8; color:#1a73e8; cursor:pointer; padding:10px; width:100%; border-radius:8px; }
.toggle-btn:hover { background:#1a73e8; color:#fff; }
</style>
</head>
<body>
<section class="login-seccion">
<div class="login-container">
    <h2><?= $modo === "login" ? "Iniciar Sesión" : "Registrar Nuevo Usuario" ?></h2>

    <?php if($mensaje != ""): ?>
        <p class="mensaje-error"><?= $mensaje ?></p>
    <?php endif; ?>

    <?php if($modo === "login"): ?>
    <form method="post">
        <input type="hidden" name="modo" value="login">
        <label>Usuario</label>
        <input type="text" name="usuario" required placeholder="Tu usuario">
        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="Tu contraseña">
        <button type="submit" name="iniciar">Iniciar Sesión</button>
    </form>
    <form method="post">
        <input type="hidden" name="modo" value="registro">
        <button type="submit" class="toggle-btn">Agregar Nuevo Usuario</button>
    </form>
    <?php else: ?>
    <form method="post">
        <input type="hidden" name="modo" value="registro">
        <label>Nombre</label>
        <input type="text" name="nombre" required placeholder="Nombre completo">
        <label>Usuario</label>
        <input type="text" name="usuario" required placeholder="Nombre de usuario">
        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="Contraseña">
        <label>Tipo</label>
        <select name="tipo">
            <option value="usuario">Usuario</option>
            <option value="admin">Administrador</option>
        </select>
        <button type="submit" name="registrar">Registrar</button>
    </form>
    <form method="post">
        <input type="hidden" name="modo" value="login">
        <button type="submit" class="toggle-btn">Volver a Iniciar Sesión</button>
    </form>
    <?php endif; ?>
</div>
</section>
</body>
</html>
