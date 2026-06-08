<?php

session_start();

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if (!isset($_SESSION["usuario_id"])) {
    responder([
        "success" => false,
        "error" => "Debes iniciar sesión"
    ], 401);
}

require_once __DIR__ . "/../config/conexion.php";

$usuarioId = (int) $_SESSION["usuario_id"];

$sql = "
    SELECT
        (
            SELECT COUNT(*)
            FROM solicitudes
            WHERE usuario_id = ?
        ) AS solicitudes,
        (
            SELECT COUNT(*)
            FROM mensajes m
            INNER JOIN conversaciones c ON c.id = m.conversacion_id
            WHERE c.usuario_id = ?
              AND m.remitente_id <> ?
              AND m.leido = 0
        ) AS mensajes_no_leidos
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    responder([
        "success" => false,
        "error" => "No se pudo preparar el resumen"
    ], 500);
}

$stmt->bind_param("iii", $usuarioId, $usuarioId, $usuarioId);

if (!$stmt->execute()) {
    responder([
        "success" => false,
        "error" => "No se pudo cargar el resumen"
    ], 500);
}

$resumen = $stmt->get_result()->fetch_assoc();

responder([
    "success" => true,
    "resumen" => [
        "solicitudes" => (int) $resumen["solicitudes"],
        "mensajes_no_leidos" => (int) $resumen["mensajes_no_leidos"]
    ]
]);
