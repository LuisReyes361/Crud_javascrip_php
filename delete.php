<?php
if (isset($_GET['id']) && isset($_GET['db'])) {
    $id = $_GET['id'];
    $db = $_GET['db'];

    if ($db == "mysql") {
        $conn = new mysqli("localhost", "luis", "luis", "formulario_db");
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } elseif ($db == "mongodb") {
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $manager->executeBulkWrite('formulario_db.usuarios', $bulk);
    }
}
header("Location: tabla.php");
?>
