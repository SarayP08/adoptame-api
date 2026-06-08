ALTER TABLE conversaciones
MODIFY solicitud_id INT NULL,
ADD COLUMN usuario_id INT NULL AFTER solicitud_id,
ADD COLUMN asunto VARCHAR(150) NULL AFTER usuario_id,
ADD CONSTRAINT fk_conversacion_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE;

UPDATE conversaciones c
INNER JOIN solicitudes s ON s.id = c.solicitud_id
SET c.usuario_id = s.usuario_id
WHERE c.usuario_id IS NULL;

ALTER TABLE conversaciones
MODIFY usuario_id INT NOT NULL;
