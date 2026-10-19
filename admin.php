<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

include('conexion.php');
?>


<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/admin.css">
    <title>Panel de administración</title>
</head>


<body>
<div class="panel-admin">
    <nav class="navbar-admin">
        <h2>MiniAppKet Admin</h2>

        <a href="#">Inicio</a>
        <a href="#">Solicitudes</a>
        <a href="#">Minimarkets registrados</a>
        <a href="#">Usuarios</a>
        <a href="cerrar_sesion.php" class="logout-admin">Cerrar sesión</a>
    </nav>

    <main class="contenido-admin">

        <h2>Panel de administrador</h2>
        <h3>Bienvenido, admin</h3>

        <div class="inicio-admin">
            <div class="solicitudes-admin">
                <h2>Solicitudes pendientes</h2>
                <h3>Revisa los nuevos negocios que quieren unirse</h3>
                <a href="solicitudes_minimarket.php">Ver solicitudes</a>
            </div>

            <div class="minimarkets-admin">
                <h2>Minimarkets registrados</h2>
                <h3>Revisa los minimarkets que trabajan con nosotros</h3>
                <a href="#">Ver minimarkets</a>
            </div>

            <div class="usuarios-admin">
                <h2>Usuarios</h2>
                <h3>Ve qué tipos de usuarios están registrados en la plataforma</h3>
                <a href="#">Ver usuarios</a>
            </div>
        </div>
    </main>
</div>
</body>

</html>