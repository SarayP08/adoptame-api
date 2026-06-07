<?php

header("Content-Type: application/json");

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/config/conexion.php';

$rol = "usuario";

$foto = "user_icon.jpg";

$nombre = $_POST["nombre"] ?? "";
$apellidos = $_POST["apellidos"] ?? "";
$edad = $_POST["edad"] ?? "";
$sexo = $_POST["sexo"] ?? "";
$email = $_POST["email"] ?? "";
$movil = $_POST["movil"] ?? "";

$passwordPlano = $_POST["password"] ?? "";

$password = password_hash(
    $passwordPlano,
    PASSWORD_DEFAULT
);

$sql = "INSERT INTO usuarios (

    nombre,
    apellidos,
    edad,
    sexo,
    email,
    movil,
    password,
    rol,
    foto

) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);

    exit;
}

$stmt->bind_param(
    "sssssssss",
    $nombre,
    $apellidos,
    $edad,
    $sexo,
    $email,
    $movil,
    $password,
    $rol,
    $foto
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true
    ]);

} else {

    if ($conn->errno === 1062) {

        echo json_encode([
            "success" => false,
            "error" => "El email ya existe"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "error" => $stmt->error
        ]);
    }
}

$stmt->close();

$conn->close();