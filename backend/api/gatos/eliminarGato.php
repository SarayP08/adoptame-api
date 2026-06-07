<?php
header("Content-Type: application/json");

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/config/conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {

    echo json_encode([
        "success" => false,
        "error" => "ID inválido"
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

$sqlImagen = "SELECT imagen FROM gatos WHERE id = ?";

$stmtImagen = $conn->prepare($sqlImagen);

$stmtImagen->bind_param("i", $id);

$stmtImagen->execute();

$resultado = $stmtImagen->get_result();

$gato = $resultado->fetch_assoc();

if ($gato && $gato['imagen']) {

    $rutaImagen = "../uploads/gatos/" . $gato['imagen'];

    if (file_exists($rutaImagen)) {
        unlink($rutaImagen);
    }
}

$sql = "DELETE FROM gatos WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true
    ]);

} else {

    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();