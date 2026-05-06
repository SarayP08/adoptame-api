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

require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode([
        "ok" => false,
        "message" => "Email y contraseña son obligatorios"
    ]);
    exit;
}

$sql = "SELECT id, nombre, email, password FROM administradores WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
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
$admin = $result->fetch_assoc();

if (!$admin || !password_verify($password, $admin['password'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Credenciales incorrectas"
    ]);
    exit;
}

session_regenerate_id(true);

$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_email'] = $admin['email'];

echo json_encode([
    "ok" => true,
    "message" => "Login correcto",
    "admin" => [
        "id" => $admin['id'],
        "nombre" => $admin['nombre'],
        "email" => $admin['email']
    ]
]);