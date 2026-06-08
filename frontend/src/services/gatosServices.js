const CLAVE_GATOS_GUARDADOS = "pawtita:gatos-guardados";

const claveUsuario = (usuarioId) => `${CLAVE_GATOS_GUARDADOS}:${usuarioId}`;

export const obtenerGatosGuardados = (usuarioId) => {
  if (!usuarioId) return [];

  try {
    const guardados = JSON.parse(localStorage.getItem(claveUsuario(usuarioId)) || "[]");
    return Array.isArray(guardados) ? guardados : [];
  } catch {
    return [];
  }
};

export const estaGatoGuardado = (usuarioId, gatoId) =>
  obtenerGatosGuardados(usuarioId).some(
    (gato) => String(gato.id) === String(gatoId),
  );

export const alternarGatoGuardado = (usuarioId, gato) => {
  const guardados = obtenerGatosGuardados(usuarioId);
  const indice = guardados.findIndex(
    (gatoGuardado) => String(gatoGuardado.id) === String(gato.id),
  );

  if (indice >= 0) {
    guardados.splice(indice, 1);
  } else {
    guardados.push(gato);
  }

  localStorage.setItem(claveUsuario(usuarioId), JSON.stringify(guardados));
  return indice < 0;
};
