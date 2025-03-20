<?php
$mysql_conn = new mysqli("localhost", "luis", "luis", "formulario_db");

$mongo_manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");


$mysql_data = [];
if (!$mysql_conn->connect_error) {
    $result = $mysql_conn->query("SELECT id, nombre, contraseña, email, edad, comentarios, telefono, direccion, ciudad, imagen FROM usuarios");
    while ($row = $result->fetch_assoc()) {
        $row['origen'] = 'mysql';
        $mysql_data[] = $row;
    }
}


$mongo_query = new MongoDB\Driver\Query([]);
$mongo_data = [];
$cursor = $mongo_manager->executeQuery('formulario_db.usuarios', $mongo_query);
foreach ($cursor as $doc) {
    $doc = (array) $doc;
    $doc['_id'] = (string) $doc['_id']; 
    $doc['origen'] = 'mongodb';
    $mongo_data[] = $doc;
}


$data = array_merge($mysql_data, $mongo_data);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="tabla.css">
</head>
<body>
<a href="http://localhost:8000/login_crud.html" class="btn">FORMULARIO</a>
    <h2>Usuarios Registrados</h2>

<div class="table-container">
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>contraseña</th>
            <th>Email</th>
            <th>Edad</th>
            <th>comentarios</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Ciudad</th>
            <th>Imagen</th>
            <th>Base de Datos</th>
            <th>Acciones</th>
            
        </tr>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?= $row['nombre'] ?></td>
                <td><?= $row['contraseña'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['edad'] ?></td>
                <td class="comentarios"><?= $row['comentarios'] ?></td>
                <td><?= $row['telefono'] ?></td>
                <td><?= $row['direccion'] ?></td>
                <td><?= $row['ciudad'] ?></td>
                <td><img src="uploads/<?= $row['imagen'] ?>" width="50"></td>
                <td><?= $row['origen'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['origen'] == 'mysql' ? $row['id'] : $row['_id'] ?>&db=<?= $row['origen'] ?>">Editar</a> | 
                    <a href="delete.php?id=<?= $row['origen'] == 'mysql' ? $row['id'] : $row['_id'] ?>&db=<?= $row['origen'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
    
</body>
</html>