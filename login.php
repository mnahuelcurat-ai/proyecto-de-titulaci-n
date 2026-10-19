
<?php

include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email-login"] ?? "";
    $contrasena = $_POST["contrasena-login"] ?? "";

    $sql = "SELECT * FROM usuarios WHERE gmail = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($contrasena, $usuario["contrasena"])) {
            session_start();

            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["rol"] = $usuario["rol"];

                if ($usuario["rol"] === "admin") {
                    header("Location: admin.php");
                    exit;
                }

                if ($usuario["rol"] === "trabajador") {
                    $_SESSION["id_minimarket"] = $usuario["id_minimarket"];
                    header("Location: panel_trabajador.php");
                    exit;
                }

                header("Location: index.php");
                exit;

        } else {
            echo "Correo o contraseña incorrectos.";
        }

    } else {
        echo "Correo o contraseña incorrectos.";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/login.css">
<title>Inicia Sesión</title>
<style>
    body{
        background-image: url('img/fondo-login.jpg');
        background-size:  cover;      
    }
</style>
</head>

<body>
    
    <form class="login-form" method="POST">
        <h1>Inicia Sesión Miniappket</h1>
            <div class="card-login">
                <label for="email-login">Correo electrónico:</label>
                <input type="email" class="email" id="email-login" name="email-login" required>
            
                <label for="contrasena-login">Contraseña:</label>
                <input type="password" class="contrasena-login" id="contrasena-login" name="contrasena-login">
            </div>
        <a href="registro.php">¿No tienes cuenta? Registrate clickeando aquí!</a>
        <button type="submit" class="login-success">Iniciar sesión</button>
    </form>
</body>
</html>