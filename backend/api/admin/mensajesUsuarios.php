<?php

session_start();

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
    responder(["success" => false, "error" => "No tienes permisos"], 403);
}

require_once __DIR__ . "/../config/conexion.php";

$adminId = (int) $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos = json_decode(file_get_contents("php://input"), true) ?? [];
    $conversacionId = (int) ($datos["conversacion_id"] ?? 0);
    $contenido = trim($datos["contenido"] ?? "");

    if ($conversacionId <= 0 || $contenido === "") {
        responder(["success" => false, "error" => "Conversación y mensaje son obligatorios"], 400);
    }

    $stmtConversacion = $conn->prepare("
        SELECT id FROM conversaciones WHERE id = ? LIMIT 1
    ");
    $stmtConversacion->bind_param("i", $conversacionId);
    $stmtConversacion->execute();

    if (!$stmtConversacion->get_result()->fetch_assoc()) {
        responder(["success" => false, "error" => "Conversación no encontrada"], 404);
    }

    $stmt = $conn->prepare("
        INSERT INTO mensajes (conversacion_id, remitente_id, mensaje)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $conversacionId, $adminId, $contenido);

    if (!$stmt->execute()) {
        responder(["success" => false, "error" => "No se pudo enviar la respuesta"], 500);
    }

    responder(["success" => true, "id" => $conn->insert_id], 201);
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    responder(["success" => false, "error" => "Método no permitido"], 405);
}

$sql = "
    SELECT
        m.id,
        m.conversacion_id,
        m.remitente_id,
        m.mensaje,
        m.fecha,
        m.leido,
        c.asunto,
        c.usuario_id,
        s.id AS solicitud_id,
        s.tipo_solicitud,
        g.id AS gato_id,
        g.nombre AS gato_nombre,
        g.imagen AS gato_imagen,
        usuario.nombre AS usuario_nombre,
        usuario.apellidos AS usuario_apellidos,
        usuario.email AS usuario_email,
        remitente.nombre AS remitente_nombre,
        remitente.rol AS remitente_rol
    FROM mensajes m
    INNER JOIN conversaciones c ON c.id = m.conversacion_id
    INNER JOIN usuarios usuario ON usuario.id = c.usuario_id
    INNER JOIN usuarios remitente ON remitente.id = m.remitente_id
    LEFT JOIN solicitudes s ON s.id = c.solicitud_id
    LEFT JOIN gatos g ON g.id = s.gato_id
    ORDER BY m.fecha ASC, m.id ASC
";

$resultado = $conn->query($sql);

if (!$resultado) {
    responder(["success" => false, "error" => "No se pudieron cargar los mensajes"], 500);
}

$mensajes = [];

while ($fila = $resultado->fetch_assoc()) {
    $contenido = json_decode($fila["mensaje"], true);
    $fila["contenido"] = is_array($contenido)
        ? $contenido
        : ["tipo" => "texto", "contenido" => $fila["mensaje"]];
    unset($fila["mensaje"]);
    $mensajes[] = $fila;
}

responder(["success" => true, "mensajes" => $mensajes]);
