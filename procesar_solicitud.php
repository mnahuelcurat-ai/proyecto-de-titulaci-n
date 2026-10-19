
<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: solicitudes_minimarket.php");
    exit;
}

$id_solicitud = filter_input(INPUT_POST, "id_solicitud", FILTER_VALIDATE_INT);
$accion = $_POST["accion"] ?? "";

if (!$id_solicitud || !in_array($accion, ["aceptar", "rechazar"], true)) {
    die("Solicitud o acción no válida.");
}

try {
    $conexion->begin_transaction();

    $sql = "SELECT * FROM solicitudes_minimarket
            WHERE id_solicitud = ? AND estado = 'pendiente' FOR UPDATE";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_solicitud);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $solicitud = $resultado->fetch_assoc();
    $stmt->close();

    if (!$solicitud) {
        throw new Exception(
            "La solicitud no existe o ya fue procesada."
        );
    }

    if ($accion === "aceptar") {

        $sql_insertar = "INSERT INTO minimarkets
            (nombre, direccion, id_zona, telefono, imagen)
            VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql_insertar);

        $stmt->bind_param(
            "ssiss",
            $solicitud["nombre_minimarket"],
            $solicitud["direccion"],
            $solicitud["id_zona"],
            $solicitud["telefono"],
            $solicitud["imagen"]
        );

        $stmt->execute();
        $stmt->close();

        $sql_estado = "UPDATE solicitudes_minimarket
                       SET estado = 'aceptada'
                       WHERE id_solicitud = ?";

        $stmt = $conexion->prepare($sql_estado);
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === "rechazar") {

        $motivo = trim($_POST["motivo_rechazo"] ?? "");

        if ($motivo === "") {
            throw new Exception(
                "Debes indicar el motivo del rechazo."
            );
        }

        $sql_estado = "UPDATE solicitudes_minimarket
                       SET estado = 'rechazada',
                           motivo_rechazo = ?
                       WHERE id_solicitud = ?";

        $stmt = $conexion->prepare($sql_estado);
        $stmt->bind_param("si", $motivo, $id_solicitud);
        $stmt->execute();
        $stmt->close();
    }

    $conexion->commit();

    header("Location: solicitudes_minimarket.php");
    exit;

} catch (Throwable $e) {
    $conexion->rollback();

    die(
        "No se pudo procesar la solicitud: " .
        htmlspecialchars($e->getMessage())
    );
}
