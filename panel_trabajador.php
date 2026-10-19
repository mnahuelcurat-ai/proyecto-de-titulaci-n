
<?php
session_start();

if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "trabajador"
) {
    header("Location: login.php");
    exit;
}

include("conexion.php");

$id_minimarket = $_SESSION["id_minimarket"];

$sql = "SELECT nombre FROM minimarkets WHERE id_minimarket = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_minimarket);
$stmt->execute();
$resultado = $stmt->get_result();

$minimarket = $resultado->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del trabajador | MiniAppKet</title>
    <link rel="stylesheet" href="css/panel_trabajador.css">
</head>
<body>

<header class="navbar-trabajador">
    <div class="logo-panel">
        <a href="index.php">
            <img src="img/logo-miniappket.png" alt="MiniAppKet">
        </a>
        <span>Panel de administración</span>
    </div>

    <div class="usuario-panel">
        <span>Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>
        <a href="cerrar_sesion.php" class="btn-cerrar">Cerrar sesión</a>
    </div>
</header>

<main class="contenedor-panel">

    <section class="bienvenida-panel">
        <p class="subtitulo">PANEL DEL TRABAJADOR</p>
        <h1><?php echo htmlspecialchars($minimarket["nombre"] ?? "Mi minimarket"); ?></h1>
        <p>Administra los productos y mantén tu minimarket al día.</p>
    </section>

    <section class="resumen-panel">
        <h2>Administración de tu minimarket</h2>
        <p>Selecciona una opción para comenzar.</p>

        <div class="tarjetas-panel">
            <article class="tarjeta-panel">
                <div class="icono-tarjeta">📦</div>
                <h3>Productos</h3>
                <p>Agrega productos, actualiza sus precios y administra su información.</p>
                <a href="gestionar_productos.php" class="btn-panel">Gestionar productos</a>
            </article>

            <article class="tarjeta-panel">
                <div class="icono-tarjeta">🗃️</div>
                <h3>Inventario</h3>
                <p>Consulta las existencias y mantén el stock de tus productos actualizado.</p>
                <a href="gestionar_productos.php" class="btn-panel">Ver inventario</a>
            </article>

            <article class="tarjeta-panel">
                <div class="icono-tarjeta">📊</div>
                <h3>Estadísticas</h3>
                <p>Prepara el seguimiento de productos y el rendimiento de tu minimarket.</p>
                <span class="etiqueta-pendiente">Próximamente</span>
            </article>

        </div>
    </section>
</main>

<footer class="footer-panel">
    <p>MiniAppKet © 2026 · Administración de minimarkets</p>
</footer>

</body>
</html>
