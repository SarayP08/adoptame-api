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
$pass = "123";
$db   = "pawtita";

$conn = new mysqli($host, $user, $pass, $db);

$id = $_GET['id'] ?? null;

if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

$sql = "SELECT * FROM gatos where id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$gatoDetalle = $result->fetch_assoc();
echo json_encode($gatoDetalle);
