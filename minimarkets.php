<?php
include('conexion.php');
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/minimarkets.css">
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
            <a href="productos.php">Productos</a>
            <a href="#">Pedidos</a>
            <a href="#">Carrito</a>
        </div>

        <div class="buscador-navbar">
            <input type="search" placeholder="Buscar productos...">
            <button type="button" aria-label="Buscar">
                🔍
            
        </div>
        <div class="boton-login">
                <a href="login.php">Iniciar sesión</a>
                </button>
            </div>
    </nav>
</section>

<h1>Ver Minimarkets cerca de tu zona</h1>
<p>selecciona tu ciudad</p>

<details>
    <sumary>
        <a href="">Victoria</a>
        <a href="">Traiguen</a>
    </sumary>
</details>
<section class="minimarkets-card">

    <?php
    $consulta = "SELECT id_minimarket, nombre, imagen
                 FROM minimarkets
                 WHERE actividad = 1
                 ORDER BY id_minimarket DESC";

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        while ($minimarket = mysqli_fetch_assoc($resultado)) {
    ?>

        <div class="card-minimarket">

            <div class="img-minimarket">
                <img src="<?php echo !empty($minimarket['imagen']) ? 
                htmlspecialchars($minimarket['imagen']) : 'img/minimarket-default.jpg';?>"
                alt="<?php echo htmlspecialchars($minimarket['nombre']); ?>">
            </div>

            <div class="contenido-minimarket">
                <h2><?php echo htmlspecialchars($minimarket['nombre']); ?></h2>
                <p>¡Explora este minimarket y descubre sus productos!</p>
                <a href="productos.php?id=<?php echo (int) $minimarket['id_minimarket']; ?>">
                    Ver minimarket</a>
            </div>
        </div>

    <?php
        }

    } else {
        echo "<p>Pronto encontrarás minimarkets disponibles.</p>";
    }
    ?>
</section>

</body>
</html>