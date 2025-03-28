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
    <title>Buscar Usuarios</title>
    <link rel="stylesheet" href="tabla.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("searchInput");
            const warningMessage = document.getElementById("warningMessage");
            const tableContainer = document.getElementById("tableContainer");
            const tableRows = document.querySelectorAll(".user-row");

            // Ocultar la tabla al inicio
            tableContainer.style.display = "none";

            searchInput.addEventListener("input", function(event) {
                let searchTerm = searchInput.value;

                // No permitir números ni caracteres especiales
                if (/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ ]/.test(searchTerm)) {
                    warningMessage.textContent = "No se permiten números ni caracteres especiales.";
                    searchInput.value = searchTerm.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ ]/g, "");
                    return;
                } else {
                    warningMessage.textContent = ""; // Limpiar mensaje si es válido
                }

                // Si empieza con un espacio, mostrar mensaje y borrar espacio
                if (searchTerm.length === 1 && searchTerm === " ") {
                    warningMessage.textContent = "No se permite un espacio al inicio.";
                    searchInput.value = "";
                    return;
                } 

                let hasResults = false;
                let searchLower = searchInput.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    let nombre = row.querySelector(".nombre").textContent.toLowerCase();
                    
                    if (nombre.includes(searchLower) && searchLower !== "") {
                        row.style.display = "table-row";  // Mostrar coincidencia
                        hasResults = true;
                    } else {
                        row.style.display = "none";  // Ocultar si no coincide
                    }
                });

                // Mostrar tabla solo si hay resultados
                tableContainer.style.display = hasResults ? "block" : "none";
            });
        });
    </script>
</head>
<body>

<h2>Buscar Usuario</h2>

<!-- Barra de búsqueda -->
<input type="text" id="searchInput" placeholder="Escribe un nombre..." autocomplete="off">
<p id="warningMessage" style="color: red;"></p> <!-- Mensaje de advertencia -->

<!-- Contenedor de la tabla (se oculta al inicio) -->
<div id="tableContainer" class="table-container">
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Contraseña</th>
            <th>Email</th>
            <th>Edad</th>
            <th>Comentarios</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Ciudad</th>
            <th>Imagen</th>
            <th>Base de Datos</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($data as $row): ?>
            <tr class="user-row">
                <td class="nombre"><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['contraseña']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['edad']) ?></td>
                <td class="comentarios"><?= htmlspecialchars($row['comentarios']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['direccion']) ?></td>
                <td><?= htmlspecialchars($row['ciudad']) ?></td>
                <td><img src="uploads/<?= htmlspecialchars($row['imagen']) ?>" width="50"></td>
                <td><?= htmlspecialchars($row['origen']) ?></td>
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
