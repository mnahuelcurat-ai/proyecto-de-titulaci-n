<?php
include("conexion.php");

$id_minimarket = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

$minimarket = null;
$productos = null;

if ($id_minimarket !== false && $id_minimarket !== null && $id_minimarket > 0) {

    $sql = "SELECT id_minimarket, nombre
            FROM minimarkets
            WHERE id_minimarket = ? AND actividad = 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_minimarket);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $minimarket = $resultado->fetch_assoc();
    $stmt->close();

    if ($minimarket) {
        $sql = "SELECT id_producto, nombre, descripcion,
                       precio, stock, categoria, imagen
                FROM productos
                WHERE id_minimarket = ? AND actividad = 1
                ORDER BY nombre ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_minimarket);
        $stmt->execute();

        $productos = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | MiniAppKet</title>
    <link rel="stylesheet" href="css/productos.css">
</head>

<body>

<section>
    <nav>
        <div class="logo-navbar">
            <a href="index.php">
                <img src="img/logo-miniappket.png" alt="MiniAppKet">
            </a>
        </div>

        <div class="boton-navbar">
            <a href="minimarkets.php">Minimarkets</a>
            <a href="minimarkets.php">Productos</a>
            <a href="#">Pedidos</a>
            <a href="#">Carrito</a>
        </div>

        <form class="buscador-navbar" action="productos.php" method="GET">
            <?php if ($minimarket): ?>
                <input type="hidden" name="id"
                       value="<?php echo (int)$minimarket["id_minimarket"]; ?>">
            <?php endif; ?>

            <input type="search" name="buscar"
                   placeholder="Buscar productos..."
                   value="<?php echo htmlspecialchars($_GET["buscar"] ?? ""); ?>">

            <button type="submit" aria-label="Buscar">🔍</button>
        </form>

        <div class="boton-login">
            <a href="login.php">Iniciar sesión</a>
        </div>
    </nav>
</section>

<main class="catalogo">

    <?php if (!$minimarket): ?>

        <section class="mensaje-catalogo">
            <h1>¡Encuentra tus productos favoritos!</h1>
            <p>Primero selecciona un minimarket para ver su catálogo.</p>
            <a href="minimarkets.php" class="btn-volver">
                Explorar minimarkets
            </a>
        </section>

    <?php else: ?>

        <header class="encabezado-catalogo">
            <p class="subtitulo-catalogo">CATÁLOGO DE PRODUCTOS</p>

            <h1><?php echo htmlspecialchars($minimarket["nombre"]); ?></h1>
            <p>Explora los productos disponibles en este minimarket.</p>
            <a href="minimarkets.php" class="enlace-volver">
                ← Ver otros minimarkets
            </a>
        </header>

        <?php
        $lista_productos = [];

        while ($producto = $productos->fetch_assoc()) {
            $buscar = trim($_GET["buscar"] ?? "");

            if (
                $buscar === "" ||
                stripos($producto["nombre"], $buscar) !== false
            ) {
                $lista_productos[] = $producto;
            }
        }
        ?>

        <?php if (count($lista_productos) > 0): ?>

            <div class="productos-grid">
                <?php foreach ($lista_productos as $producto): ?>
                    <article class="producto-card">
                        <div class="imagen-producto">
                            <?php if (!empty($producto["imagen"])): ?>
                                <img
                                    src="<?php echo htmlspecialchars($producto["imagen"]); ?>"
                                    alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                                >
                            <?php else: ?>
                                <div class="sin-imagen">📦</div>
                            <?php endif; ?>
                        </div>

                        <div class="contenido-producto">

                            <?php if (!empty($producto["categoria"])): ?>
                                <span class="categoria-producto">
                                    <?php echo htmlspecialchars($producto["categoria"]); ?>
                                </span>
                            <?php endif; ?>

                            <h2>
                                <?php echo htmlspecialchars($producto["nombre"]); ?>
                            </h2>

                            <?php if (!empty($producto["descripcion"])): ?>
                                <p class="descripcion-producto">
                                    <?php echo htmlspecialchars($producto["descripcion"]); ?>
                                </p>
                            <?php endif; ?>

                            <p class="precio-producto">
                                $<?php echo number_format($producto["precio"], 0, ",", "."); ?>
                            </p>

                            <?php if ((int)$producto["stock"] > 0): ?>
                                <p class="stock-disponible">
                                    Disponible: <?php echo (int)$producto["stock"]; ?> unidades
                                </p>
                            <?php else: ?>
                                <p class="stock-agotado">Producto agotado</p>
                            <?php endif; ?>
                        </div>
                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="mensaje-catalogo">
                <h2>No encontramos productos</h2>
                <p>
                    <?php if (!empty($_GET["buscar"])): ?>
                        No hay productos que coincidan con tu búsqueda.
                    <?php else: ?>
                        Este minimarket todavía no tiene productos registrados.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>

<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
?>