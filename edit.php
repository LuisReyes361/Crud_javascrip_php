<?php
if (isset($_GET['id']) && isset($_GET['db'])) {
    $id = $_GET['id'];
    $db = $_GET['db'];

    if ($db == "mysql") {
        $conn = new mysqli("localhost", "luis", "luis", "formulario_db");
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
    } elseif ($db == "mongodb") {
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $cursor = $manager->executeQuery('formulario_db.usuarios', $query);
        $result = (array)current($cursor->toArray());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="editar.css">
    <script>
        // Función para validar el formulario
        function validarFormulario() {
            var comentarios = document.getElementsByName('comentarios')[0].value;
            if (comentarios.trim() === "") {
                alert("El campo de comentarios no puede estar vacío.");
                return false;  // Evita que el formulario se envíe
            }

            var nombre = document.getElementsByName('nombre')[0].value;
            if (/\d/.test(nombre)) {
                alert("El campo de nombre no puede contener números.");
                return false;  // Evita que el formulario se envíe
            }

            var telefono = document.getElementsByName('telefono')[0].value;
            // Verifica que el teléfono tenga solo dígitos y exactamente 10 dígitos
            if (!/^\d{10}$/.test(telefono)) {
                alert("El teléfono debe contener exactamente 10 dígitos y solo números.");
                return false;  // Evita que el formulario se envíe
            }

            return true;  // Permite que el formulario se envíe si todo es válido
        }
    </script>
</head>
<body>
    
    <form action="update.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="db" value="<?= $db ?>">
        <div>
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $result['nombre'] ?>" required><br>

            <label>Contraseña:</label>
            <input type="password" name="contraseña" value="<?= $result['contraseña'] ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $result['email'] ?>" required><br>

            <label>Edad:</label>
            <input type="number" name="edad" value="<?= $result['edad'] ?>" required min="10" max="100"><br>
        </div>

        <div>
            <label for="comentarios">Comentarios:</label><br><br>
            <textarea name="comentarios" rows="4" cols="50" required><?= $result['comentarios'] ?></textarea><br><br>

            <label>Teléfono:</label>
            <input type="tel" name="telefono" value="<?= $result['telefono'] ?>" required><br>

            <label>Dirección:</label>
            <input type="text" name="direccion" value="<?= $result['direccion'] ?>" required><br>

            <label>Ciudad:</label>
            <input type="text" name="ciudad" value="<?= $result['ciudad'] ?>" required><br>
        </div>

        <label for="imagen">Imagen actual:</label>
        <?php if (!empty($result['imagen'])): ?>
            <img src="uploads/<?= $result['imagen'] ?>" width="100" style="display: block; margin: 10px 0;">
        <?php else: ?>
            <p>No hay imagen cargada.</p>
        <?php endif; ?>

        <label for="imagen">Subir nueva imagen:</label>
        <input type="file" id="imagen" name="imagen" accept="image/*"><br><br>

        <br><br>
        <input type="submit" value="Actualizar">
    </form>

</body>
</html>
