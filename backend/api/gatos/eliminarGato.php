<?php

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "error" => "ID inválido"
    ]);

    exit;
}

/* OBTENER IMAGEN */

$sqlImagen = "SELECT imagen FROM gatos WHERE id = ?";

$stmtImagen = $conn->prepare($sqlImagen);

$stmtImagen->bind_param("i", $id);

$stmtImagen->execute();

$resultado = $stmtImagen->get_result();

$gato = $resultado->fetch_assoc();

$stmtImagen->close();

/* ELIMINAR VACUNAS DEL GATO */

$sqlVacunas = "DELETE FROM vacunas WHERE gato_id = ?";

$stmtVacunas = $conn->prepare($sqlVacunas);

$stmtVacunas->bind_param("i", $id);

$stmtVacunas->execute();

$stmtVacunas->close();

/* ELIMINAR GATO */

$sqlGato = "DELETE FROM gatos WHERE id = ?";

$stmtGato = $conn->prepare($sqlGato);

$stmtGato->bind_param("i", $id);

if (!$stmtGato->execute()) {

    echo json_encode([
        "success" => false,
        "error" => $stmtGato->error
    ]);

    exit;
}

$stmtGato->close();

/* ELIMINAR IMAGEN */

if (!empty($gato["imagen"])) {

    $rutaImagen = __DIR__ . "/../../uploads/gatos/" . $gato["imagen"];

    if (file_exists($rutaImagen)) {
        unlink($rutaImagen);
    }
}

echo json_encode([
    "success" => true
]);

$conn->close();