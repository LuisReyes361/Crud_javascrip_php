<?php
$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];
$email = $_POST['email'];
$edad = $_POST['edad'];
$comentarios = $_POST['comentarios'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$ciudad = $_POST['ciudad'];
$imagen = $_FILES['imagen']['name'];
$base_datos = $_POST['base_datos'];


/*$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["imagen"]["name"]);
move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}
    */

$target_dir = "uploads/";  // Asegúrate de incluir la barra al final

// Verifica si la carpeta existe, si no, créala
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);  // Usa permisos 0755 por seguridad
}

// Construye la ruta completa del archivo
$target_file = $target_dir . basename($_FILES["imagen"]["name"]);

// Mueve el archivo subido a la carpeta de destino
if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
    echo "El archivo se ha subido correctamente.";
} else {
    echo "Hubo un error al subir el archivo.";
}


if ($base_datos == "mysql") {
    
    $conn = new mysqli("localhost", "luis", "luis", "formulario_db");

    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Insertar datos en MySQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, contraseña, email, edad, comentarios, telefono, direccion, ciudad, imagen) VALUES (?, ?, ?, ?, ?, ?, ?,?,?)");
    $stmt->bind_param("ssissssss", $nombre, $contraseña, $email, $edad, $comentarios, $telefono, $direccion, $ciudad, $imagen);

    if ($stmt->execute()) {
        echo "Datos guardados en MySQL ";
        header("Location: http://localhost:8000/login_crud.html"); 
        //exit(); //
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} 
elseif ($base_datos == "mongodb") {
    // Conexión a MongoDB
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    // Crear un documento
    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = [
        'nombre' => $nombre,
        'contraseña' => $contraseña,
        'email' => $email,
        'edad' => $edad,
        'comentarios' => $comentarios,
        'telefono' => $telefono,
        'direccion' => $direccion,
        'ciudad' => $ciudad,
        'imagen' => $imagen
    ];

    $bulk->insert($doc);

    // Ejecutar la inserción
    $manager->executeBulkWrite('formulario_db.usuarios', $bulk);
    echo "Datos guardados en MongoDB " ;
    header("Location: http://localhost:8000/login_crud.html"); 
    exit(); //
    
}

?>