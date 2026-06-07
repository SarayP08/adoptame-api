<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$nombre = trim($_POST["nombre"] ?? "");
$edad = intval($_POST["edad"] ?? 0);
$sexo = trim($_POST["sexo"] ?? "");
$castrado = trim($_POST["castrado"] ?? "");
$estado = trim($_POST["estado"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

$imagen = "";

/* SUBIDA DE IMAGEN */

if (
    isset($_FILES["imagen"]) &&
    $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
) {

    $carpetaDestino = __DIR__ . "/../../uploads/gatos/";

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $nombreImagen =
        time() . "_" . basename($_FILES["imagen"]["name"]);

    $rutaCompleta = $carpetaDestino . $nombreImagen;

    if (
        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            $rutaCompleta
        )
    ) {
        $imagen = $nombreImagen;
    }
}

/* INSERTAR GATO */

$sql = "
INSERT INTO gatos
(
    nombre,
    edad,
    sexo,
    castrado,
    descripcion,
    estado,
    imagen
)
VALUES (?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);

    exit;
}

$stmt->bind_param(
    "sisssss",
    $nombre,
    $edad,
    $sexo,
    $castrado,
    $descripcion,
    $estado,
    $imagen
);

if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);

    exit;
}

$idGato = $conn->insert_id;

/* INSERTAR VACUNAS */

$vacunasJson = $_POST["vacunas"] ?? "[]";

$vacunas = json_decode($vacunasJson, true);

if (is_array($vacunas)) {

    $sqlVacuna = "
    INSERT INTO vacunas
    (
        nombre,
        gato_id
    )
    VALUES (?, ?)
    ";

    $stmtVacuna = $conn->prepare($sqlVacuna);

    foreach ($vacunas as $vacuna) {

        $vacuna = trim($vacuna);

        if ($vacuna === "") {
            continue;
        }

        $stmtVacuna->bind_param(
            "si",
            $vacuna,
            $idGato
        );

        $stmtVacuna->execute();
    }

    $stmtVacuna->close();
}

$stmt->close();

echo json_encode([
    "success" => true,
    "id" => $idGato
]);

$conn->close();