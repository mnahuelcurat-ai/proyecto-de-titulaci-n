<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: solicitar_minimarket.php");
    exit;
}

$nombre_minimarket = trim($_POST["nombre_minimarket"] ?? "");
$nombre_dueno = trim($_POST["nombre_dueno"] ?? "");
$rut_dueno = trim($_POST["rut_dueno"] ?? "");
$gmail = trim($_POST["gmail"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");
$id_zona = filter_input(INPUT_POST, "id_zona", FILTER_VALIDATE_INT);
$descripcion = trim($_POST["descripcion"] ?? "");

if (
    $nombre_minimarket === "" ||
    $nombre_dueno === "" ||
    $rut_dueno === "" ||
    $gmail === "" ||
    $telefono === "" ||
    $direccion === "" ||
    !$id_zona ||
    !filter_var($gmail, FILTER_VALIDATE_EMAIL)
) {
    exit("Error: completa correctamente los campos obligatorios.");
}

$consulta_zona = $conexion->prepare(
    "SELECT id FROM zona WHERE id = ?"
);
$consulta_zona->bind_param("i", $id_zona);
$consulta_zona->execute();
$resultado_zona = $consulta_zona->get_result();

if ($resultado_zona->num_rows === 0) {
    exit("Error: la zona seleccionada no existe.");
}

$consulta_zona->close();


$imagen = null;

if (
    isset($_FILES["imagen"]) &&
    $_FILES["imagen"]["error"] !== UPLOAD_ERR_NO_FILE
) {
    if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
        exit("Error al recibir la imagen.");
    }

    if ($_FILES["imagen"]["size"] > 2 * 1024 * 1024) {
        exit("Error: la imagen no debe superar los 2 MB.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipo = $finfo->file($_FILES["imagen"]["tmp_name"]);

    $tipos_permitidos = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($tipos_permitidos[$tipo])) {
        exit("Error: utiliza una imagen JPG, PNG o WEBP.");
    }

    $carpeta = __DIR__ . "/img/minimarkets/";

    if (
        !is_dir($carpeta) &&
        !mkdir($carpeta, 0755, true)
    ) {
        exit("Error al crear la carpeta de imágenes.");
    }

    $nombre_archivo = bin2hex(random_bytes(16))
        . "." . $tipos_permitidos[$tipo];

    $destino = $carpeta . $nombre_archivo;

    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino)) {
        exit("Error al guardar la imagen.");
    }

    $imagen = "img/minimarkets/" . $nombre_archivo;
}

$sql = "INSERT INTO solicitudes_minimarket
        (nombre_minimarket, nombre_dueno, rut_dueno,
         gmail, telefono, direccion, id_zona,
         imagen, descripcion, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "ssssssiss",
    $nombre_minimarket,
    $nombre_dueno,
    $rut_dueno,
    $gmail,
    $telefono,
    $direccion,
    $id_zona,
    $imagen,
    $descripcion
);

if ($stmt->execute()) {
    $stmt->close();
    $conexion->close();

    echo "¡Solicitud enviada correctamente! "
       . "El administrador revisará tu solicitud.";
} else {

    if ($imagen !== null && isset($destino) && is_file($destino)) {
        unlink($destino);
    }

    $stmt->close();
    $conexion->close();

    exit("Error al guardar la solicitud. Inténtalo nuevamente.");
}
?>
