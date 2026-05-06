<?php
session_start();

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/config/conexion.php';


$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "ok" => false,
        "message" => "Email y contraseña son obligatorios"
    ]);
    exit;
}

$sql = "SELECT id, nombre, apellidos, email, password, rol FROM usuarios WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "message" => "Error preparando la consulta",
        "debug" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario || $password !== $usuario['password']) {
    http_response_code(401);
    echo json_encode([
        "ok" => false,
        "message" => "Credenciales incorrectas"
    ]);
    exit;
}

session_regenerate_id(true);

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_email'] = $usuario['email'];
$_SESSION['usuario_rol'] = $usuario['rol'];

echo json_encode([
    "ok" => true,
    "message" => "Login correcto",
    "usuario" => [
        "id" => $usuario['id'],
        "nombre" => $usuario['nombre'],
        "apellidos" => $usuario['apellidos'],
        "email" => $usuario['email'],
        "rol" => $usuario['rol']
    ]
]);