<?php

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "error" => "ID inválido"
    ]);

    exit;
}

/* GATO */

$sql = "SELECT * FROM gatos WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$gato = $result->fetch_assoc();

$stmt->close();

if (!$gato) {

    echo json_encode([
        "success" => false,
        "error" => "Gato no encontrado"
    ]);

    exit;
}

/* VACUNAS */

$sqlVacunas = "
SELECT nombre
FROM vacunas
WHERE gato_id = ?
";

$stmtVacunas = $conn->prepare($sqlVacunas);

$stmtVacunas->bind_param("i", $id);

$stmtVacunas->execute();

$resultVacunas = $stmtVacunas->get_result();

$vacunas = [];

while ($fila = $resultVacunas->fetch_assoc()) {

    $vacunas[] = $fila["nombre"];
}

$stmtVacunas->close();

$gato["vacunas"] = $vacunas;

echo json_encode($gato);

$conn->close();