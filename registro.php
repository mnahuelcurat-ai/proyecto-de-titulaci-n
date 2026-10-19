<?php
require_once "conexion.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    
    $nombre = $_POST["nombre-usuario"];
    $rut = $_POST["rut-registro"];
    $email = $_POST["email-registro"];
    $contrasena = $_POST["contrasena-usuario"];
    $confirmar = $_POST["confirmar-contrasena-usuario"];

    if ($contrasena !== $confirmar){
    die("las contraseñas no coinciden");
    }

    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre, rut, gmail, contrasena, rol) VALUES (?,?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $rol = "cliente";
    $stmt->bind_param(
        "sssss",
        $nombre,
        $rut,
        $email,
        $contrasena_hash,
        $rol
    );

    if ($stmt->execute()){
        echo "usuario registrado correctamente";
    } else {
        echo "error de registro: " . $stmt->error; 
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>Registrate!</title>
<link rel="stylesheet" href="css/registro.css">
<style>
    body{
        background-image: url('img/register-imagen.jpg');
        background-size:  cover;
        width: 100vw;
        height: 100vh;
        overflow: hidden; 
        background-position: center center;
    }
</style>
</head>

<body>
<form class="form-registro" method="POST">

    <h1>Registrate en Miniappket</h1>
    <div class="login">
        <label for="nombre-usuario">Ingrese su nombre</label>
        <input type="text" class="usuario" id="nombre-usuario" 
        name="nombre-usuario" placeholder="Ingrese su nombre">
        
        <label for="rut-registro">Ingrese su RUT</label>
        <input type="text" class="rut-usuario" id="rut-registro" name="rut-registro"
        placeholder="Ej: 11.111.111-1">

        <label for="email-registro">Ingrese un correo electrónico</label>
        <input type="email" class="email" id="email-registro" name="email-registro" 
        placeholder="Ej: croquetas@gmail.com">

        <label for="contrasena-usuario">Cree una contraseña</label>
        <input type="password" class="contrasena" id="contrasena-usuario" name="contrasena-usuario" 
        placeholder="Es secreta!">

        <label for="confirmar-contrasena-usuario">Confirme su contraseña</label>
        <input type="password" class="confirmar-contrasena" id="confirmar-contrasena-usuario" 
        name="confirmar-contrasena-usuario">
    </div>
    <button type="submit" class="registrar">Registrarse</button>    
</form>
</body>

</html>