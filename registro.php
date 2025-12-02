<?php
require_once __DIR__ . '/conexion.php';

$mensaje = "";

if(isset($_POST['registrar'])){
    $nombre   = $_POST['nombre'];
    $usuario  = $_POST['usuario'];
    $password = $_POST['password'];
    $tipo     = $_POST['tipo'];

    // Validar si el usuario ya existe
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario=? LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){
        $mensaje = "El usuario ya existe.";
    } else {
        // Encriptar la contraseña (mejor práctica)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios(nombre, usuario, password, tipo) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $nombre, $usuario, $password_hash, $tipo);

        if($stmt->execute()){
            $mensaje = "Usuario registrado correctamente.";
        } else {
            $mensaje = "Error al registrar usuario.";
        }
    }

    $stmt->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Usuario</title>
</head>
<body>
<h2>Registrar Usuario</h2>

<?php if($mensaje != ""): ?>
    <p><?= $mensaje ?></p>
<?php endif; ?>

<form method="post">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Usuario:</label><br>
    <input type="text" name="usuario" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Tipo de usuario:</label><br>
    <select name="tipo">
        <option value="usuario">Usuario</option>
        <option value="admin">Administrador</option>
    </select><br><br>

    <button type="submit" name="registrar">Registrar</button>
</form>
</body>
</html>
