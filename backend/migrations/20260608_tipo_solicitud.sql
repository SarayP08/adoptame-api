ALTER TABLE solicitudes
ADD COLUMN tipo_solicitud ENUM('adopcion', 'acogida') NOT NULL DEFAULT 'adopcion'
AFTER gato_id;
