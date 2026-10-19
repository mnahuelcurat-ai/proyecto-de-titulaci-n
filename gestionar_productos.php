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
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = $_POST["precio"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $categoria = trim($_POST["categoria"] ?? "");
    $imagen = "";

    if (
        $nombre === "" ||
        $precio === "" ||
        $stock === "" ||
        !is_numeric($precio) ||
        !ctype_digit((string)$stock) ||
        (float)$precio < 0 ||
        (float)$precio != (int)$precio ||
        (int)$stock < 0
    ) {
        $mensaje = "Revisa los datos: el nombre, el precio y el stock son obligatorios.";
    } else {

        if (
            isset($_FILES["imagen"]) &&
            $_FILES["imagen"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
                $mensaje = "No se pudo subir la imagen.";
            } elseif ($_FILES["imagen"]["size"] > 3 * 1024 * 1024) {
                $mensaje = "La imagen no puede superar los 3 MB.";
            } else {
                $tipo = mime_content_type($_FILES["imagen"]["tmp_name"]);
                $tipos_permitidos = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (
                    !isset($tipos_permitidos[$tipo]) ||
                    getimagesize($_FILES["imagen"]["tmp_name"]) === false
                ) {
                    $mensaje = "Utiliza una imagen JPG, PNG o WEBP válida.";
                } else {
                    $carpeta = __DIR__ . "/img/productos/";

                    if (!is_dir($carpeta)) {
                        $mensaje = "No se encontró la carpeta img/productos/.";
                    } else {
                        $nombre_archivo = bin2hex(random_bytes(16))
                            . "." . $tipos_permitidos[$tipo];

                        if (
                            move_uploaded_file(
                                $_FILES["imagen"]["tmp_name"],
                                $carpeta . $nombre_archivo
                            )
                        ) {
                            $imagen = "img/productos/" . $nombre_archivo;
                        } else {
                            $mensaje = "No se pudo guardar la imagen.";
                        }
                    }
                }
            }
        }

        if ($mensaje === "") {
            $sql = "INSERT INTO productos
                    (id_minimarket, nombre, descripcion, precio, stock, categoria, imagen)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);
            $precio_entero = (int)$precio;
            $stock_entero = (int)$stock;

            $stmt->bind_param(
                "issiiss",
                $id_minimarket,
                $nombre,
                $descripcion,
                $precio_entero,
                $stock_entero,
                $categoria,
                $imagen
            );

            if ($stmt->execute()) {
                $mensaje = "¡Producto registrado correctamente!";
            } else {
                  $mensaje = "Error al registrar el producto: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$sql_productos = "SELECT id_producto, nombre, precio, stock,
                         categoria, imagen
                  FROM productos
                  WHERE id_minimarket = ?
                  ORDER BY id_producto DESC";

$stmt_productos = $conexion->prepare($sql_productos);
$stmt_productos->bind_param("i", $id_minimarket);
$stmt_productos->execute();
$productos = $stmt_productos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar productos | MiniAppKet</title>
    <link rel="stylesheet" href="css/gestionar_productos.css">
</head>
<body>

<header class="encabezado">
    <a href="panel_trabajador.php">← Volver al panel</a>
    <h1>Gestión de productos</h1>
    <a href="cerrar_sesion.php">Cerrar sesión</a>
</header>

<main class="contenedor">
    <section class="formulario-producto">
        <h2>Agregar un producto</h2>

        <?php if ($mensaje !== ""): ?>
            <p class="mensaje">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="nombre">Nombre del producto</label>
            <input type="text" id="nombre" name="nombre" maxlength="150" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion"></textarea>

            <label for="precio">Precio (CLP)</label>
            <input type="number" id="precio" name="precio" min="0" step="1" required>

            <label for="stock">Stock disponible</label>
            <input type="number" id="stock" name="stock" min="0" step="1" required>

            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria">
                <option value="">Selecciona una categoría</option>
                <option value="Bebidas">Bebidas</option>
                <option value="Lácteos">Lácteos</option>
                <option value="Verduras">Verduras</option>
                <option value="Snacks">Snacks</option>
                <option value="Abarrotes">Abarrotes</option>
                <option value="Limpieza">Limpieza</option>
                <option value="Otros">Otros</option>
            </select>

            <label for="imagen">Imagen del producto (opcional)</label>
            <input
                type="file"
                id="imagen"
                name="imagen"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <button type="submit">Registrar producto</button>
        </form>
    </section>

    <section class="lista-productos">
        <h2>Productos registrados</h2>

        <?php if ($productos->num_rows > 0): ?>
            <div class="productos-grid">

                <?php while ($producto = $productos->fetch_assoc()): ?>
                    <article class="producto-card">

                        <?php if (!empty($producto["imagen"])): ?>
                            <img
                                src="<?php echo htmlspecialchars($producto["imagen"]); ?>"
                                alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                            >
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($producto["nombre"]); ?></h3>

                        <p>
                            Precio:
                            $<?php echo number_format($producto["precio"], 0, ",", "."); ?>
                        </p>

                        <p>Stock: <?php echo (int)$producto["stock"]; ?></p>

                        <p>
                            Categoría:
                            <?php echo htmlspecialchars($producto["categoria"] ?? "Sin categoría"); ?>
                        </p>

                    </article>
                <?php endwhile; ?>

            </div>
        <?php else: ?>
            <p>Aún no hay productos registrados en este minimarket.</p>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
```