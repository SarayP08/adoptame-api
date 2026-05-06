<?php


$host = "localhost";
$user = "root";
$password = "123";
$database = "pawtita";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "message" => "Error de conexión con la base de datos"
    ]);
    exit;
}

$conn->set_charset("utf8mb4");