<?php
header("Content-Type: application/json");

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$id = intval($_POST["id"] ?? 0);

$nombre = trim($_POST["nombre"] ?? "");
$edad = intval($_POST["edad"] ?? 0);
$sexo = trim($_POST["sexo"] ?? "");
$castrado = trim($_POST["castrado"] ?? "");
$estado = trim($_POST["estado"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "error" => "ID inválido"
    ]);

    exit;
}

/* IMAGEN ACTUAL */

$sqlImagen = "SELECT imagen FROM gatos WHERE id = ?";

$stmtImg = $conn->prepare($sqlImagen);

$stmtImg->bind_param("i", $id);

$stmtImg->execute();

$gatoActual = $stmtImg->get_result()->fetch_assoc();

$stmtImg->close();

$imagen = $gatoActual["imagen"];

/* NUEVA IMAGEN */

if (
    isset($_FILES["imagen"]) &&
    $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
) {

    $carpetaDestino = __DIR__ . "/../../uploads/gatos/";

    $nombreImagen =
        time() . "_" . basename($_FILES["imagen"]["name"]);

    $rutaCompleta = $carpetaDestino . $nombreImagen;

    if (
        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            $rutaCompleta
        )
    ) {

        if (!empty($imagen)) {

            $vieja = $carpetaDestino . $imagen;

            if (file_exists($vieja)) {
                unlink($vieja);
            }
        }

        $imagen = $nombreImagen;
    }
}

/* ACTUALIZAR GATO */

$sql = "
UPDATE gatos
SET
    nombre = ?,
    edad = ?,
    sexo = ?,
    castrado = ?,
    descripcion = ?,
    estado = ?,
    imagen = ?
WHERE id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sisssssi",
    $nombre,
    $edad,
    $sexo,
    $castrado,
    $descripcion,
    $estado,
    $imagen,
    $id
);

if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);

    exit;
}

$stmt->close();

/* ELIMINAR VACUNAS ANTIGUAS */

$sqlDelete = "DELETE FROM vacunas WHERE gato_id = ?";

$stmtDelete = $conn->prepare($sqlDelete);

$stmtDelete->bind_param("i", $id);

$stmtDelete->execute();

$stmtDelete->close();

/* INSERTAR NUEVAS VACUNAS */

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
            $id
        );

        $stmtVacuna->execute();
    }

    $stmtVacuna->close();
}

echo json_encode([
    "success" => true
]);

$conn->close();