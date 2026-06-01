<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pawtita";

$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    echo json_encode(["error" => $conn->connect_error]);
    exit;
}

$conn->set_charset("utf8mb4");

$nombre = $_POST["nombre"] ?? "";
$edad = $_POST["edad"] ?? "";
$sexo = $_POST["sexo"] ?? "";
$castrado = $_POST["castrado"] ?? 0;
$descripcion = $_POST["descripcion"] ?? "";
$estado = $_POST["estado"] ?? "";
$imagen = "";
$vacunas = $_POST["vacunas"] ?? "";

if (isset($_FILES["imagen"])) {

    $carpetaDestino = "../uploads/gatos/";

    $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);

    $rutaCompleta = $carpetaDestino . $nombreImagen;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaCompleta)) {
        $imagen = $nombreImagen;
    }
}


$sql = "INSERT INTO gatos(nombre, edad, sexo, castrado, descripcion, estado, imagen, vacunas)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}


$stmt->bind_param(
    "ssssssss",
    $nombre,
    $edad,
    $sexo,
    $castrado,
    $descripcion,
    $estado,
    $imagen,
    $vacunas
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "id" => $conn->insert_id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();