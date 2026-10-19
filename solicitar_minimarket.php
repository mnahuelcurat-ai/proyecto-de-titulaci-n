<?php
include('conexion.php');

$sql = "SELECT id, nombre FROM zona ORDER BY nombre ASC";
$resultado_zonas = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar minimarket | MiniAppKet</title>
    <link rel="stylesheet" href="css/solicitar_minimarket.css">
</head>
<body>

    <main class="formulario-solicitud">
        <h1>Incorpora tu minimarket</h1>
        <p>
            Completa los siguientes datos para solicitar que tu negocio
            forme parte de MiniAppKet.
        </p>

        <form action="guardar_solicitud.php" method="POST"
              enctype="multipart/form-data">

            <label for="nombre_minimarket">Nombre del minimarket</label>
            <input type="text" id="nombre_minimarket"
                   name="nombre_minimarket" maxlength="255" required>

            <label for="nombre_dueno">Nombre completo del dueño</label>
            <input type="text" id="nombre_dueno"
                   name="nombre_dueno" maxlength="100" required>

            <label for="rut_dueno">RUT del dueño</label>
            <input type="text" id="rut_dueno"
                   name="rut_dueno" maxlength="12"
                   placeholder="12.345.678-9" required>

            <label for="gmail">Correo electrónico</label>
            <input type="email" id="gmail" name="gmail"
                   maxlength="150" required>

            <label for="telefono">Teléfono de contacto</label>
            <input type="tel" id="telefono" name="telefono"
                   maxlength="15" required>

            <label for="direccion">Dirección del minimarket</label>
            <input type="text" id="direccion" name="direccion"
                   maxlength="255" required>

            <label for="id_zona">Comuna o zona</label>
            <select id="id_zona" name="id_zona" required>
                <option value="">Selecciona una comuna</option>

                <?php while ($zona = $resultado_zonas->fetch_assoc()) { ?>
                    <option value="<?php echo $zona['id']; ?>">
                        <?php echo htmlspecialchars($zona['nombre']); ?>
                    </option>
                <?php } ?>
            </select>

            <label for="descripcion">Descripción del minimarket</label>
            <textarea id="descripcion" name="descripcion"
                      rows="4" maxlength="2000"
                      placeholder="Cuéntanos brevemente sobre tu negocio"></textarea>

            <label for="imagen">Imagen del minimarket (opcional)</label>
            <input type="file" id="imagen" name="imagen"
                   accept="image/jpeg,image/png,image/webp">

            <button type="submit">Enviar solicitud</button>
        </form>

        <a href="index.php">Volver al inicio</a>
    </main>

</body>
</html>
