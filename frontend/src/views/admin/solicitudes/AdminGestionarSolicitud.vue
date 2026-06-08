<script setup>
import { computed, onMounted, ref } from "vue";
import { RouterLink, useRoute } from "vue-router";
import { API_URL, buildCatImage } from "../../../config/api.js";

const route = useRoute();
const solicitud = ref(null);
const cargando = ref(true);
const guardando = ref(false);
const error = ref("");
const mensaje = ref("");
const comentarioRechazo = ref("");
const mostrarRechazo = ref(false);
const mostrarAceptacion = ref(false);
const datosAceptacion = ref({
  fecha: "",
  direccion: "",
  contenido: "",
});

const nombreUsuario = computed(() => {
  if (!solicitud.value) return "";
  return `${solicitud.value.usuario_nombre || ""} ${solicitud.value.usuario_apellidos || ""}`.trim();
});

const cargarSolicitud = async () => {
  try {
    cargando.value = true;
    const respuesta = await fetch(
      `${API_URL}/api/gatos/solicitudGato.php?id=${route.params.id}`,
      { credentials: "include" },
    );
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.success) {
      throw new Error(datos.error || "No se pudo cargar la solicitud");
    }

    solicitud.value = datos.solicitud;
    comentarioRechazo.value = datos.solicitud.comentario_admin || "";
  } catch (err) {
    error.value = err.message;
  } finally {
    cargando.value = false;
  }
};

const actualizarEstado = async (estado) => {
  error.value = "";
  mensaje.value = "";

  if (estado === "rechazada" && !comentarioRechazo.value.trim()) {
    error.value = "Debes escribir el motivo del rechazo.";
    return;
  }

  if (
    estado === "aceptada" &&
    (!datosAceptacion.value.fecha ||
      !datosAceptacion.value.direccion.trim() ||
      !datosAceptacion.value.contenido.trim())
  ) {
    error.value = "Debes completar la fecha, la dirección y el contenido del mensaje.";
    return;
  }

  try {
    guardando.value = true;
    const respuesta = await fetch(`${API_URL}/api/gatos/solicitudGato.php`, {
      method: "PATCH",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: Number(route.params.id),
        estado,
        comentario_admin: estado === "rechazada" ? comentarioRechazo.value.trim() : "",
        fecha_cita: estado === "aceptada" ? datosAceptacion.value.fecha : "",
        direccion: estado === "aceptada" ? datosAceptacion.value.direccion.trim() : "",
        contenido_mensaje:
          estado === "aceptada" ? datosAceptacion.value.contenido.trim() : "",
      }),
    });
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.success) {
      throw new Error(datos.error || "No se pudo actualizar la solicitud");
    }

    solicitud.value.estado = datos.estado;
    solicitud.value.comentario_admin = datos.comentario_admin;
    mostrarRechazo.value = false;
    mostrarAceptacion.value = false;
    mensaje.value =
      estado === "aceptada"
        ? "Solicitud aceptada."
        : "Solicitud rechazada y comentario enviado al usuario.";
  } catch (err) {
    error.value = err.message;
  } finally {
    guardando.value = false;
  }
};

onMounted(cargarSolicitud);
</script>

<template>
  <main class="gestionar-page">
    <div class="cabecera">
      <div>
        <p>Panel de administración</p>
        <h1>Gestionar solicitud</h1>
      </div>
      <RouterLink to="/admin/solicitudes" class="volver-btn">Volver</RouterLink>
    </div>

    <p v-if="cargando" class="mensaje-panel">Cargando solicitud...</p>
    <p v-else-if="error && !solicitud" class="mensaje-panel error">{{ error }}</p>

    <template v-else-if="solicitud">
      <section class="resumen">
        <div>
          <span>Estado actual</span>
          <strong :class="`estado estado-${solicitud.estado}`">{{ solicitud.estado }}</strong>
        </div>
        <div>
          <span>Tipo</span>
          <strong>{{ solicitud.estado_gato }}</strong>
        </div>
        <div>
          <span>Fecha</span>
          <strong>{{ solicitud.fecha }}</strong>
        </div>
      </section>

      <div class="datos-grid">
        <section class="info-card">
          <h2><i class="bi bi-person-fill"></i> Solicitante</h2>
          <dl>
            <div><dt>Nombre</dt><dd>{{ nombreUsuario }}</dd></div>
            <div><dt>Email</dt><dd>{{ solicitud.usuario_email }}</dd></div>
            <div><dt>Móvil</dt><dd>{{ solicitud.usuario_movil || "No indicado" }}</dd></div>
            <div><dt>Edad</dt><dd>{{ solicitud.usuario_edad || "No indicada" }}</dd></div>
            <div><dt>Sexo</dt><dd>{{ solicitud.usuario_sexo || "No indicado" }}</dd></div>
          </dl>
        </section>

        <section class="info-card gato-card">
          <img :src="buildCatImage(solicitud.gato_imagen)" :alt="solicitud.gato_nombre" />
          <div>
            <h2><i class="bi bi-heart-fill"></i> {{ solicitud.gato_nombre }}</h2>
            <dl>
              <div><dt>Edad</dt><dd>{{ solicitud.gato_edad }}</dd></div>
              <div><dt>Sexo</dt><dd>{{ solicitud.gato_sexo }}</dd></div>
              <div><dt>Castrado</dt><dd>{{ solicitud.gato_castrado }}</dd></div>
              <div><dt>Situación</dt><dd>{{ solicitud.estado_gato }}</dd></div>
            </dl>
            <p>{{ solicitud.gato_descripcion }}</p>
            <RouterLink :to="`/detalleGato/${solicitud.gato_id}`" class="ver-gato-btn">
              Ver ficha completa
            </RouterLink>
          </div>
        </section>
      </div>

      <section class="info-card respuestas">
        <h2>Respuestas del formulario</h2>
        <dl>
          <div><dt>Motivo</dt><dd>{{ solicitud.motivo }}</dd></div>
          <div><dt>Vivienda</dt><dd>{{ solicitud.vivienda }}</dd></div>
          <div><dt>Tiempo disponible</dt><dd>{{ solicitud.tiempo }}</dd></div>
          <div><dt>Experiencia</dt><dd>{{ solicitud.experiencia || "No indicada" }}</dd></div>
          <div><dt>Otros animales</dt><dd>{{ solicitud.otros_animales || "No indicado" }}</dd></div>
          <div><dt>Comentario</dt><dd>{{ solicitud.comentario || "Sin comentario" }}</dd></div>
        </dl>
      </section>

      <section class="gestion-card">
        <h2>Decisión</h2>

        <div v-if="mostrarAceptacion" class="decision-form">
          <label for="fecha-cita">Fecha y hora</label>
          <input id="fecha-cita" v-model="datosAceptacion.fecha" type="datetime-local" />

          <label for="direccion-cita">Dirección</label>
          <input
            id="direccion-cita"
            v-model="datosAceptacion.direccion"
            type="text"
            placeholder="Lugar de la cita o entrega"
          />

          <label for="contenido-mensaje">Contenido del mensaje</label>
          <textarea
            id="contenido-mensaje"
            v-model="datosAceptacion.contenido"
            placeholder="Información que recibirá el usuario"
          ></textarea>
        </div>

        <div v-if="mostrarRechazo" class="decision-form">
          <label for="comentario-rechazo">Motivo del rechazo</label>
          <textarea
            id="comentario-rechazo"
            v-model="comentarioRechazo"
            placeholder="Explica al usuario por qué se rechaza su solicitud"
          ></textarea>
        </div>

        <p v-if="error" class="feedback error">{{ error }}</p>
        <p v-if="mensaje" class="feedback success">{{ mensaje }}</p>

        <div class="acciones">
          <button
            v-if="!mostrarAceptacion"
            type="button"
            class="aceptar-btn"
            :disabled="guardando"
            @click="mostrarAceptacion = true; mostrarRechazo = false"
          >
            Aceptar solicitud
          </button>
          <button
            v-else
            type="button"
            class="aceptar-btn"
            :disabled="guardando"
            @click="actualizarEstado('aceptada')"
          >
            Confirmar aceptación y enviar
          </button>
          <button
            v-if="!mostrarRechazo"
            type="button"
            class="rechazar-btn"
            :disabled="guardando"
            @click="mostrarRechazo = true; mostrarAceptacion = false"
          >
            Rechazar solicitud
          </button>
          <button
            v-else
            type="button"
            class="rechazar-btn"
            :disabled="guardando"
            @click="actualizarEstado('rechazada')"
          >
            Confirmar rechazo
          </button>
        </div>
      </section>
    </template>
  </main>
</template>

<style scoped>
.gestionar-page {
  min-height: calc(100vh - 160px);
  padding: 48px max(24px, 7vw);
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.cabecera,
.resumen,
.acciones {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.cabecera {
  margin-bottom: 30px;
}

.cabecera p {
  margin: 0;
  color: #df9800;
  font-family: "coolvetica";
}

h1,
h2 {
  color: #423430;
  font-family: "coolvetica";
}

h1 {
  margin: 5px 0 0;
}

.volver-btn,
.ver-gato-btn {
  padding: 10px 18px;
  border-radius: 999px;
  background: #df9800;
  color: white;
  text-decoration: none;
}

.resumen,
.info-card,
.gestion-card,
.mensaje-panel {
  padding: 24px;
  border-radius: 22px;
  background: white;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.resumen {
  margin-bottom: 24px;
}

.resumen > div {
  display: grid;
  gap: 5px;
}

.resumen span,
dt {
  color: #9a6b56;
}

.estado {
  width: fit-content;
  padding: 5px 12px;
  border-radius: 999px;
  background: #fff3c4;
  color: #9a6500;
  text-transform: capitalize;
}

.estado-aceptada {
  background: #dff3d8;
  color: #2f6b3d;
}

.estado-rechazada {
  background: #f8dddd;
  color: #9c3434;
}

.datos-grid {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 24px;
  margin-bottom: 24px;
}

.info-card h2,
.gestion-card h2 {
  margin-top: 0;
}

dl {
  display: grid;
  gap: 12px;
  margin: 0;
}

dl > div {
  display: grid;
  gap: 3px;
}

dd {
  margin: 0;
  color: #423430;
}

.gato-card {
  display: grid;
  grid-template-columns: 210px 1fr;
  gap: 22px;
}

.gato-card > img {
  width: 100%;
  height: 100%;
  min-height: 260px;
  border-radius: 16px;
  object-fit: cover;
}

.respuestas,
.gestion-card {
  margin-bottom: 24px;
}

.respuestas dl {
  grid-template-columns: repeat(2, 1fr);
}

.decision-form {
  display: grid;
  gap: 8px;
  margin-bottom: 18px;
}

.decision-form label {
  color: #423430;
  font-family: "coolvetica";
}

.decision-form input,
.decision-form textarea {
  padding: 12px;
  border: 2px solid #f1dc7a;
  border-radius: 14px;
}

.decision-form textarea {
  min-height: 120px;
  padding: 12px;
  border: 2px solid #f1dc7a;
  border-radius: 14px;
  resize: vertical;
}

.acciones {
  justify-content: flex-end;
}

.acciones button {
  padding: 12px 20px;
  border: none;
  border-radius: 999px;
  color: white;
  font-family: "coolvetica";
  cursor: pointer;
}

.aceptar-btn {
  background: #4f8a5b;
}

.rechazar-btn {
  background: #b85c4c;
}

.feedback {
  padding: 12px;
  border-radius: 12px;
}

.feedback.error {
  background: #f8dddd;
  color: #9c3434;
}

.feedback.success {
  background: #dff3d8;
  color: #2f6b3d;
}

@media (max-width: 850px) {
  .datos-grid,
  .gato-card,
  .respuestas dl {
    grid-template-columns: 1fr;
  }

  .resumen,
  .cabecera,
  .acciones {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
