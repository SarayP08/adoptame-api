<?php

session_start();

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS");
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
$esAdmin = ($_SESSION["usuario_rol"] ?? "") === "admin";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $sql = "
        SELECT
            s.*,
            g.nombre AS gato_nombre,
            g.imagen AS gato_imagen,
            g.estado AS estado_gato,
            g.edad AS gato_edad,
            g.sexo AS gato_sexo,
            g.castrado AS gato_castrado,
            g.descripcion AS gato_descripcion,
            u.nombre AS usuario_nombre,
            u.apellidos AS usuario_apellidos,
            u.edad AS usuario_edad,
            u.sexo AS usuario_sexo,
            u.email AS usuario_email,
            u.movil AS usuario_movil
        FROM solicitudes s
        INNER JOIN gatos g ON g.id = s.gato_id
        INNER JOIN usuarios u ON u.id = s.usuario_id
    ";

    $solicitudId = (int) ($_GET["id"] ?? 0);

    if ($esAdmin && $solicitudId > 0) {
        $sql .= " WHERE s.id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $solicitudId);
        }
    } elseif ($esAdmin) {
        $sql .= " ORDER BY s.fecha DESC";
        $stmt = $conn->prepare($sql);
    } else {
        $sql .= " WHERE s.usuario_id = ? ORDER BY s.fecha DESC";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $usuarioId);
        }
    }

    if (!$stmt || !$stmt->execute()) {
        responder([
            "success" => false,
            "error" => "No se pudieron obtener las solicitudes"
        ], 500);
    }

    $resultado = $stmt->get_result();
    $solicitudes = [];

    while ($solicitud = $resultado->fetch_assoc()) {
        $solicitudes[] = $solicitud;
    }

    if ($solicitudId > 0 && $esAdmin) {
        if (!$solicitudes) {
            responder([
                "success" => false,
                "error" => "Solicitud no encontrada"
            ], 404);
        }

        responder([
            "success" => true,
            "solicitud" => $solicitudes[0]
        ]);
    }

    responder([
        "success" => true,
        "solicitudes" => $solicitudes
    ]);
}

if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
    if (!$esAdmin) {
        responder([
            "success" => false,
            "error" => "No tienes permisos para gestionar solicitudes"
        ], 403);
    }

    $datos = json_decode(file_get_contents("php://input"), true) ?? [];
    $solicitudId = (int) ($datos["id"] ?? 0);
    $estado = trim($datos["estado"] ?? "");
    $comentarioAdmin = trim($datos["comentario_admin"] ?? "");
    $fechaCita = trim($datos["fecha_cita"] ?? "");
    $direccion = trim($datos["direccion"] ?? "");
    $contenidoMensaje = trim($datos["contenido_mensaje"] ?? "");

    if ($solicitudId <= 0 || !in_array($estado, ["aceptada", "rechazada"], true)) {
        responder([
            "success" => false,
            "error" => "Solicitud o estado no válidos"
        ], 400);
    }

    if ($estado === "rechazada" && $comentarioAdmin === "") {
        responder([
            "success" => false,
            "error" => "Debes indicar el motivo del rechazo"
        ], 400);
    }

    if ($estado === "aceptada") {
        if ($fechaCita === "" || $direccion === "" || $contenidoMensaje === "") {
            responder([
                "success" => false,
                "error" => "Fecha, dirección y contenido son obligatorios al aceptar"
            ], 400);
        }

        $comentarioAdmin = "";
    }

    $conn->begin_transaction();

    $sql = "
        UPDATE solicitudes
        SET estado = ?, comentario_admin = NULLIF(?, '')
        WHERE id = ?
    ";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        responder([
            "success" => false,
            "error" => "No se pudo preparar la actualización"
        ], 500);
    }

    $stmt->bind_param("ssi", $estado, $comentarioAdmin, $solicitudId);

    if (!$stmt->execute()) {
        $conn->rollback();
        responder([
            "success" => false,
            "error" => "No se pudo actualizar la solicitud"
        ], 500);
    }

    if ($stmt->affected_rows === 0) {
        $comprobar = $conn->prepare("SELECT id FROM solicitudes WHERE id = ? LIMIT 1");
        $comprobar->bind_param("i", $solicitudId);
        $comprobar->execute();

        if (!$comprobar->get_result()->fetch_assoc()) {
            $conn->rollback();
            responder([
                "success" => false,
                "error" => "Solicitud no encontrada"
            ], 404);
        }
    }

    if ($estado === "aceptada") {
        $stmtConversacion = $conn->prepare("
            INSERT INTO conversaciones (solicitud_id, usuario_id, asunto)
            SELECT id, usuario_id, CONCAT('Solicitud de ', tipo_solicitud)
            FROM solicitudes
            WHERE id = ?
            ON DUPLICATE KEY UPDATE
                id = LAST_INSERT_ID(id),
                usuario_id = VALUES(usuario_id)
        ");

        if (!$stmtConversacion) {
            $conn->rollback();
            responder([
                "success" => false,
                "error" => "No se pudo preparar la conversación"
            ], 500);
        }

        $stmtConversacion->bind_param("i", $solicitudId);

        if (!$stmtConversacion->execute()) {
            $conn->rollback();
            responder([
                "success" => false,
                "error" => "No se pudo crear la conversación"
            ], 500);
        }

        $conversacionId = (int) $conn->insert_id;
        $mensajeEstructurado = json_encode([
            "tipo" => "solicitud_aceptada",
            "fecha" => $fechaCita,
            "direccion" => $direccion,
            "contenido" => $contenidoMensaje
        ], JSON_UNESCAPED_UNICODE);

        $stmtMensaje = $conn->prepare("
            INSERT INTO mensajes (conversacion_id, remitente_id, mensaje)
            VALUES (?, ?, ?)
        ");

        if (!$stmtMensaje) {
            $conn->rollback();
            responder([
                "success" => false,
                "error" => "No se pudo preparar el mensaje"
            ], 500);
        }

        $stmtMensaje->bind_param("iis", $conversacionId, $usuarioId, $mensajeEstructurado);

        if (!$stmtMensaje->execute()) {
            $conn->rollback();
            responder([
                "success" => false,
                "error" => "No se pudo enviar el mensaje"
            ], 500);
        }
    }

    $conn->commit();

    responder([
        "success" => true,
        "estado" => $estado,
        "comentario_admin" => $comentarioAdmin ?: null
    ]);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder([
        "success" => false,
        "error" => "Método no permitido"
    ], 405);
}

if ($esAdmin) {
    responder([
        "success" => false,
        "error" => "Los administradores no pueden enviar solicitudes"
    ], 403);
}

$gatoId = (int) ($_POST["gato_id"] ?? 0);
$tipoSolicitud = trim($_POST["tipo_solicitud"] ?? "");
$motivo = trim($_POST["motivo"] ?? "");
$vivienda = trim($_POST["vivienda"] ?? "");
$experiencia = trim($_POST["experiencia"] ?? "");
$tiempo = trim($_POST["tiempo"] ?? "");
$otrosAnimales = trim($_POST["otros_animales"] ?? "");
$comentario = trim($_POST["comentario"] ?? "");

if (
    $gatoId <= 0 ||
    !in_array($tipoSolicitud, ["adopcion", "acogida"], true) ||
    $motivo === "" ||
    $vivienda === "" ||
    $tiempo === ""
) {
    responder([
        "success" => false,
        "error" => "Gato, tipo de solicitud, motivo, vivienda y tiempo son obligatorios"
    ], 400);
}

if (strlen($vivienda) > 50 || strlen($tiempo) > 50) {
    responder([
        "success" => false,
        "error" => "Vivienda y tiempo no pueden superar los 50 caracteres"
    ], 400);
}

$stmtGato = $conn->prepare("SELECT id FROM gatos WHERE id = ? LIMIT 1");

if (!$stmtGato) {
    responder([
        "success" => false,
        "error" => "No se pudo validar el gato"
    ], 500);
}

$stmtGato->bind_param("i", $gatoId);
$stmtGato->execute();

if (!$stmtGato->get_result()->fetch_assoc()) {
    responder([
        "success" => false,
        "error" => "El gato indicado no existe"
    ], 404);
}

$sql = "
    INSERT INTO solicitudes (
        usuario_id,
        gato_id,
        tipo_solicitud,
        motivo,
        vivienda,
        experiencia,
        tiempo,
        otros_animales,
        comentario
    )
    VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, NULLIF(?, ''), NULLIF(?, ''))
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    responder([
        "success" => false,
        "error" => "Existe un error en la solicitud"
    ], 500);
}

$stmt->bind_param(
    "iisssssss",
    $usuarioId,
    $gatoId,
    $tipoSolicitud,
    $motivo,
    $vivienda,
    $experiencia,
    $tiempo,
    $otrosAnimales,
    $comentario
);

if (!$stmt->execute()) {
    responder([
        "success" => false,
        "error" => "No se pudo guardar la solicitud"
    ], 500);
}

responder([
    "success" => true,
    "id" => $conn->insert_id,
    "message" => "Solicitud guardada correctamente"
], 201);
