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

if (!isset($_SESSION["usuario_id"]) || ($_SESSION["usuario_rol"] ?? "") !== "admin") {
    responder([
        "success" => false,
        "error" => "No tienes permisos para consultar el resumen"
    ], 403);
}

require_once __DIR__ . "/../config/conexion.php";

$sql = "
    SELECT
        (SELECT COUNT(*) FROM gatos) AS gatos_publicados,
        (
            SELECT COUNT(*)
            FROM solicitudes
            WHERE estado = 'pendiente'
        ) AS solicitudes_pendientes,
        (
            SELECT COUNT(*)
            FROM conversaciones c
            INNER JOIN mensajes ultimo ON ultimo.id = (
                SELECT m.id
                FROM mensajes m
                WHERE m.conversacion_id = c.id
                ORDER BY m.fecha DESC, m.id DESC
                LIMIT 1
            )
            INNER JOIN usuarios u ON u.id = ultimo.remitente_id
            WHERE u.rol = 'usuario'
        ) AS mensajes_sin_responder
";

$resultado = $conn->query($sql);

if (!$resultado) {
    responder([
        "success" => false,
        "error" => "No se pudo cargar el resumen"
    ], 500);
}

$resumen = $resultado->fetch_assoc();

responder([
    "success" => true,
    "resumen" => [
        "gatos_publicados" => (int) $resumen["gatos_publicados"],
        "solicitudes_pendientes" => (int) $resumen["solicitudes_pendientes"],
        "mensajes_sin_responder" => (int) $resumen["mensajes_sin_responder"]
    ]
]);
