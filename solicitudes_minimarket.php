<?php
session_start();

if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

include('conexion.php');

$sql_contadores = "SELECT estado, COUNT(*) AS total FROM solicitudes_minimarket
                   GROUP BY estado";

$resultado_contadores = $conexion->query($sql_contadores);

$pendientes = 0;
$rechazadas = 0;
$aceptadas = 0;

if ($resultado_contadores) {
    while ($fila = $resultado_contadores->fetch_assoc()) {
        if ($fila["estado"] == "pendiente") {
            $pendientes = $fila["total"];
        } elseif ($fila["estado"] == "rechazada") {
            $rechazadas = $fila["total"];
        } elseif ($fila["estado"] == "aceptada") {
            $aceptadas = $fila["total"];
        }
    }
}

$sql = "SELECT s.*, z.nombre AS nombre_zona FROM solicitudes_minimarket s
        INNER JOIN zona z ON s.id_zona = z.id WHERE s.estado = 'pendiente'
        ORDER BY s.fecha_solicitud DESC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error al consultar solicitudes: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes | MiniAppKet Admin</title>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
<div class="panel-admin">
    <nav class="navbar-admin">
        <h2>MiniAppKet Admin</h2>

        <a href="#">Inicio</a>
        <a href="#">Solicitudes</a>
        <a href="#">Minimarkets registrados</a>
        <a href="#">Usuarios</a>
        <a href="#" class="logout-admin">Cerrar sesión</a>
    </nav>

<main>
    <h2>Solicitudes</h2>
    <!-- Estadísticas -->
    <div class="solicitudes-panel">

        <div class="solicitudes-pendientes">
            <h3>Solicitudes pendientes</h3>
            <h4><?php echo $pendientes; ?></h4>
        </div>

        <div class="solicitudes-rechazadas">
            <h3>Solicitudes rechazadas</h3>
            <h4><?php echo $rechazadas; ?></h4>
        </div>

        <div class="solicitudes-aceptadas">
            <h3>Solicitudes aceptadas</h3>
            <h4><?php echo $aceptadas; ?></h4>
        </div>
    </div>

    <div class="lista-solicitudes">
        <?php if ($resultado->num_rows > 0) { ?>
            <?php while ($solicitud = $resultado->fetch_assoc()) { ?>

                <div class="minimarket-solicitud">
                    <h2>
                        <?php echo htmlspecialchars($solicitud["nombre_minimarket"]); ?>
                    </h2>
                    <?php if (!empty($solicitud["imagen"])) { ?>
                        <img
                            src="<?php echo htmlspecialchars($solicitud["imagen"]); ?>"
                            alt="Imagen del minimarket"
                        >
                    <?php } ?>
                    <h3>Estado: pendiente</h3>
                    <p>
                        <strong>Dueño:</strong>
                        <?php echo htmlspecialchars($solicitud["nombre_dueno"]); ?>
                    </p>

                    <p>
                        <strong>Zona:</strong>
                        <?php echo htmlspecialchars($solicitud["nombre_zona"]); ?>
                    </p>

                    <p>
                        <strong>Fecha de solicitud:</strong>
                        <?php echo htmlspecialchars($solicitud["fecha_solicitud"]); ?>
                    </p>

                    <details>
                        <summary>Ver detalles</summary>
                        <p>
                            <strong>RUT:</strong>
                            <?php echo htmlspecialchars($solicitud["rut_dueno"]); ?>
                        </p>

                        <p>
                            <strong>Correo:</strong>
                            <?php echo htmlspecialchars($solicitud["gmail"]); ?>
                        </p>

                        <p>
                            <strong>Teléfono:</strong>
                            <?php echo htmlspecialchars($solicitud["telefono"]); ?>
                        </p>

                        <p>
                            <strong>Dirección:</strong>
                            <?php echo htmlspecialchars($solicitud["direccion"]); ?>
                        </p>

                        <p>
                            <strong>Descripción:</strong>
                            <?php echo htmlspecialchars($solicitud["descripcion"] ?? "Sin descripción"); ?>
                        </p>
                    </details>

                    <form action="procesar_solicitud.php" method="POST">
                        <input type="hidden" name="id_solicitud" value="<?php echo $solicitud["id_solicitud"]; ?>">
                        <button type="submit" name="accion" value="aceptar">Aceptar solicitud</button>
                    </form>

                    <form action="procesar_solicitud.php" method="POST">
                        <input type="hidden" name="id_solicitud" value="<?php echo $solicitud["id_solicitud"]; ?>">
                        <label for="motivo-<?php echo $solicitud["id_solicitud"]; ?>">Motivo del rechazo</label>
                            <textarea id="motivo-<?php echo $solicitud["id_solicitud"]; ?>" name="motivo_rechazo"
                                        rows="3" maxlength="2000" placeholder="Explica por qué se rechaza la solicitud"required>
                            </textarea>
                        <button type="submit" name="accion" value="rechazar">Rechazar solicitud</button>
                    </form>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No hay solicitudes pendientes por el momento.</p>
        <?php } ?>
    </div>
</main>
</body>
</html>