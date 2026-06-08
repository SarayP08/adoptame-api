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

if (!isset($_SESSION["usuario_id"])) {
    responder(["success" => false, "error" => "Debes iniciar sesión"], 401);
}

require_once __DIR__ . "/../config/conexion.php";

$usuarioId = (int) $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos = json_decode(file_get_contents("php://input"), true) ?? [];
    $conversacionId = (int) ($datos["conversacion_id"] ?? 0);
    $asunto = trim($datos["asunto"] ?? "");
    $contenido = trim($datos["contenido"] ?? "");

    if ($contenido === "") {
        responder(["success" => false, "error" => "El mensaje no puede estar vacío"], 400);
    }

    if ($conversacionId > 0) {
        $stmtConversacion = $conn->prepare("
            SELECT id FROM conversaciones
            WHERE id = ? AND usuario_id = ?
            LIMIT 1
        ");
        $stmtConversacion->bind_param("ii", $conversacionId, $usuarioId);
        $stmtConversacion->execute();

        if (!$stmtConversacion->get_result()->fetch_assoc()) {
            responder(["success" => false, "error" => "Conversación no encontrada"], 404);
        }
    } else {
        if ($asunto === "") {
            responder(["success" => false, "error" => "Debes indicar un asunto"], 400);
        }

        $stmtConversacion = $conn->prepare("
            INSERT INTO conversaciones (solicitud_id, usuario_id, asunto)
            VALUES (NULL, ?, ?)
        ");
        $stmtConversacion->bind_param("is", $usuarioId, $asunto);

        if (!$stmtConversacion->execute()) {
            responder(["success" => false, "error" => "No se pudo crear la conversación"], 500);
        }

        $conversacionId = (int) $conn->insert_id;
    }

    $stmtMensaje = $conn->prepare("
        INSERT INTO mensajes (conversacion_id, remitente_id, mensaje)
        VALUES (?, ?, ?)
    ");
    $stmtMensaje->bind_param("iis", $conversacionId, $usuarioId, $contenido);

    if (!$stmtMensaje->execute()) {
        responder(["success" => false, "error" => "No se pudo enviar el mensaje"], 500);
    }

    responder([
        "success" => true,
        "conversacion_id" => $conversacionId,
        "id" => $conn->insert_id
    ], 201);
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    responder(["success" => false, "error" => "Método no permitido"], 405);
}

$sql = "
    SELECT
        m.id,
        m.conversacion_id,
        m.mensaje,
        m.fecha,
        m.leido,
        m.remitente_id,
        c.asunto,
        s.id AS solicitud_id,
        s.tipo_solicitud,
        g.id AS gato_id,
        g.nombre AS gato_nombre,
        g.imagen AS gato_imagen,
        u.nombre AS remitente_nombre
    FROM mensajes m
    INNER JOIN conversaciones c ON c.id = m.conversacion_id
    LEFT JOIN solicitudes s ON s.id = c.solicitud_id
    LEFT JOIN gatos g ON g.id = s.gato_id
    INNER JOIN usuarios u ON u.id = m.remitente_id
    WHERE c.usuario_id = ?
    ORDER BY m.conversacion_id, m.fecha ASC, m.id ASC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    responder(["success" => false, "error" => "No se pudieron preparar los mensajes"], 500);
}

$stmt->bind_param("i", $usuarioId);

if (!$stmt->execute()) {
    responder(["success" => false, "error" => "No se pudieron cargar los mensajes"], 500);
}

$resultado = $stmt->get_result();
$mensajes = [];
$idsNoLeidos = [];

while ($fila = $resultado->fetch_assoc()) {
    $contenido = json_decode($fila["mensaje"], true);
    $fila["contenido"] = is_array($contenido)
        ? $contenido
        : ["tipo" => "texto", "contenido" => $fila["mensaje"]];
    unset($fila["mensaje"]);
    $mensajes[] = $fila;

    if ((int) $fila["leido"] === 0 && (int) $fila["remitente_id"] !== $usuarioId) {
        $idsNoLeidos[] = (int) $fila["id"];
    }
}

if ($idsNoLeidos) {
    $ids = implode(",", $idsNoLeidos);
    $conn->query("UPDATE mensajes SET leido = 1 WHERE id IN ($ids)");
}

responder(["success" => true, "mensajes" => $mensajes]);
