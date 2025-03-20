<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $db = $_POST['db'];
    $nombre = $_POST['nombre'];
    $contraseña = $_POST['contraseña'];
    $email = $_POST['email'];
    $edad = $_POST['edad'];
    $comentarios = $_POST['comentarios'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];

    // Manejo de la imagen subida
    $imagenNombre = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen'];
        $nombreArchivo = basename($imagen['name']);
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreArchivoNuevo = uniqid("img_") . "." . $extension;

        // Verificar que la extensión sea válida
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($extension), $extensionesPermitidas)) {
            $directorioDestino = 'uploads/' . $nombreArchivoNuevo;
            if (move_uploaded_file($imagen['tmp_name'], $directorioDestino)) {
                $imagenNombre = $nombreArchivoNuevo;  // Asignamos el nombre de la imagen que se guardará en la base de datos
            } else {
                // Si la imagen no se pudo mover, se muestra un error
                echo "Error al mover la imagen.";
                exit;
            }
        } else {
            // Si la extensión no es válida, mostrar un error
            echo "Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
            exit;
        }
    }

    // Actualización en la base de datos
    if ($db == "mysql") {
        $conn = new mysqli("localhost", "luis", "luis", "formulario_db");
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Si se subió una imagen nueva, la variable $imagenNombre tendrá un valor
        // Si no se subió ninguna imagen nueva, usamos la imagen que ya existía en la base de datos
        if ($imagenNombre === "") {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, contraseña=?, email=?, edad=?, comentarios=?, telefono=?, direccion=?, ciudad=? WHERE id=?");
            $stmt->bind_param("sssissssi", $nombre, $contraseña, $email, $edad, $comentarios, $telefono, $direccion, $ciudad, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, contraseña=?, email=?, edad=?, comentarios=?, telefono=?, direccion=?, ciudad=?, imagen=? WHERE id=?");
            $stmt->bind_param("sssisssssi", $nombre, $contraseña, $email, $edad, $comentarios, $telefono, $direccion, $ciudad, $imagenNombre, $id);
        }
        
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } elseif ($db == "mongodb") {
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $bulk = new MongoDB\Driver\BulkWrite;

        // Si se subió una imagen nueva, se actualiza el campo 'imagen'
        if ($imagenNombre === "") {
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'nombre' => $nombre,
                    'contraseña' => $contraseña,
                    'email' => $email,
                    'edad' => $edad,
                    'comentarios' => $comentarios,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'ciudad' => $ciudad
                ]]
            );
        } else {
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'nombre' => $nombre,
                    'contraseña' => $contraseña,
                    'email' => $email,
                    'edad' => $edad,
                    'comentarios' => $comentarios,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'ciudad' => $ciudad,
                    'imagen' => $imagenNombre
                ]]
            );
        }
        
        $manager->executeBulkWrite('formulario_db.usuarios', $bulk);
    }
}

// Redirección a la página de tabla después de la actualización
header("Location: tabla.php");
exit;
?>
