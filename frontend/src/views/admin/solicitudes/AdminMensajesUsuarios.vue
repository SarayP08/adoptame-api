<script setup>
import { computed, nextTick, onMounted, ref } from "vue";
import { RouterLink, useRoute } from "vue-router";
import { API_URL, buildCatImage } from "../../../config/api.js";

const route = useRoute();
const mensajes = ref([]);
const respuestas = ref({});
const cargando = ref(true);
const enviando = ref(false);
const error = ref("");
const historiales = ref({});

const conversaciones = computed(() => {
  const agrupadas = new Map();

  mensajes.value.forEach((mensaje) => {
    if (!agrupadas.has(mensaje.conversacion_id)) {
      agrupadas.set(mensaje.conversacion_id, {
        id: mensaje.conversacion_id,
        usuario_id: mensaje.usuario_id,
        asunto: mensaje.asunto || `Solicitud de ${mensaje.tipo_solicitud}`,
        usuario_nombre: `${mensaje.usuario_nombre} ${mensaje.usuario_apellidos}`.trim(),
        usuario_email: mensaje.usuario_email,
        gato_id: mensaje.gato_id,
        gato_nombre: mensaje.gato_nombre,
        gato_imagen: mensaje.gato_imagen,
        mensajes: [],
      });
    }

    agrupadas.get(mensaje.conversacion_id).mensajes.push(mensaje);
  });

  return [...agrupadas.values()]
    .map((conversacion) => ({
      ...conversacion,
      pendiente:
        conversacion.mensajes.at(-1)?.remitente_rol === "usuario",
      ultimaFecha: conversacion.mensajes.at(-1)?.fecha,
    }))
    .sort((a, b) => new Date(b.ultimaFecha) - new Date(a.ultimaFecha));
});

const conversacionesFiltradas = computed(() => {
  if (route.query.estado === "pendientes") {
    return conversaciones.value.filter((conversacion) => conversacion.pendiente);
  }

  if (route.query.estado === "respondidos") {
    return conversaciones.value.filter((conversacion) => !conversacion.pendiente);
  }

  return conversaciones.value;
});

const usuariosConConversaciones = computed(() => {
  const usuarios = new Map();

  conversacionesFiltradas.value.forEach((conversacion) => {
    const clave = conversacion.usuario_id;

    if (!usuarios.has(clave)) {
      usuarios.set(clave, {
        nombre: conversacion.usuario_nombre,
        email: conversacion.usuario_email,
        id: conversacion.usuario_id,
        conversaciones: [],
      });
    }

    usuarios.get(clave).conversaciones.push(conversacion);
  });

  return [...usuarios.values()];
});

const titulo = computed(() => {
  if (route.query.estado === "pendientes") return "Mensajes sin responder";
  if (route.query.estado === "respondidos") return "Conversaciones respondidas";
  return "Mensajes de usuarios";
});

const formatearFecha = (fecha) =>
  new Intl.DateTimeFormat("es-ES", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(new Date(fecha));

const cargarMensajes = async () => {
  try {
    cargando.value = true;
    error.value = "";
    const respuesta = await fetch(`${API_URL}/api/admin/mensajesUsuarios.php`, {
      credentials: "include",
    });
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.success) {
      throw new Error(datos.error || "No se pudieron cargar los mensajes");
    }

    mensajes.value = datos.mensajes;
    await nextTick();

    Object.values(historiales.value).forEach((historial) => {
      if (historial) {
        historial.scrollTop = historial.scrollHeight;
      }
    });
  } catch (err) {
    error.value = err.message;
  } finally {
    cargando.value = false;
  }
};

const responder = async (conversacionId) => {
  const contenido = respuestas.value[conversacionId]?.trim();

  if (!contenido) {
    error.value = "Escribe una respuesta antes de enviarla.";
    return;
  }

  try {
    enviando.value = true;
    error.value = "";
    const respuesta = await fetch(`${API_URL}/api/admin/mensajesUsuarios.php`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        conversacion_id: conversacionId,
        contenido,
      }),
    });
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.success) {
      throw new Error(datos.error || "No se pudo enviar la respuesta");
    }

    respuestas.value[conversacionId] = "";
    await cargarMensajes();
  } catch (err) {
    error.value = err.message;
  } finally {
    enviando.value = false;
  }
};

onMounted(cargarMensajes);
</script>

<template>
  <main class="mensajes-page">
    <header class="pagina-cabecera">
      <div>
        <p>Panel de administración</p>
        <h1>{{ titulo }}</h1>
        <span>Consulta y responde las conversaciones iniciadas por usuarios.</span>
      </div>
      <RouterLink to="/panel-admin" class="volver-btn">Volver al panel</RouterLink>
    </header>

    <nav class="filtros">
      <RouterLink to="/admin/mensajes">Todas</RouterLink>
      <RouterLink to="/admin/mensajes?estado=pendientes">Sin responder</RouterLink>
      <RouterLink to="/admin/mensajes?estado=respondidos">Respondidas</RouterLink>
    </nav>

    <p v-if="cargando" class="estado-vacio">Cargando conversaciones...</p>
    <p v-else-if="error && !mensajes.length" class="estado-vacio error">{{ error }}</p>

    <section v-else-if="usuariosConConversaciones.length" class="usuarios-lista">
      <details
        v-for="usuario in usuariosConConversaciones"
        :key="usuario.id"
        class="usuario-desplegable"
      >
        <summary class="usuario-resumen">
          <div>
            <i class="bi bi-person-circle"></i>
            <span>
              <strong>{{ usuario.nombre }}</strong>
              <small>{{ usuario.email }}</small>
            </span>
          </div>
          <span class="contador">{{ usuario.conversaciones.length }}</span>
        </summary>

        <div class="conversaciones-usuario">
          <details
            v-for="conversacion in usuario.conversaciones"
            :key="conversacion.id"
            class="conversacion-desplegable"
          >
            <summary class="conversacion-resumen">
              <div>
                <strong>{{ conversacion.asunto }}</strong>
                <small>
                  {{ conversacion.gato_id ? `Solicitud: ${conversacion.gato_nombre}` : "Consulta general" }}
                </small>
              </div>
              <span :class="['estado', { pendiente: conversacion.pendiente }]">
                {{ conversacion.pendiente ? "Sin responder" : "Respondida" }}
              </span>
            </summary>

            <div class="conversacion-card">
              <aside>
                <img
                  v-if="conversacion.gato_imagen"
                  :src="buildCatImage(conversacion.gato_imagen)"
                  :alt="conversacion.gato_nombre"
                />
                <div v-else class="icono-general">
                  <i class="bi bi-chat-dots-fill"></i>
                </div>

                <RouterLink
                  v-if="conversacion.gato_id"
                  :to="`/detalleGato/${conversacion.gato_id}`"
                  class="gato-btn"
                >
                  Ver {{ conversacion.gato_nombre }}
                </RouterLink>
              </aside>

              <div class="contenido">
                <div
                  :ref="(elemento) => { historiales[conversacion.id] = elemento; }"
                  class="historial"
                >
                  <div
                    v-for="mensaje in conversacion.mensajes"
                    :key="mensaje.id"
                    :class="['burbuja', { admin: mensaje.remitente_rol === 'admin' }]"
                  >
                    <strong>{{ mensaje.remitente_nombre }}</strong>
                    <template v-if="mensaje.contenido.tipo === 'solicitud_aceptada'">
                      <p><i class="bi bi-calendar-event"></i> {{ mensaje.contenido.fecha }}</p>
                      <p><i class="bi bi-geo-alt-fill"></i> {{ mensaje.contenido.direccion }}</p>
                    </template>
                    <p>{{ mensaje.contenido.contenido }}</p>
                    <small>{{ formatearFecha(mensaje.fecha) }}</small>
                  </div>
                </div>

                <div class="respuesta-form">
                  <textarea
                    v-model="respuestas[conversacion.id]"
                    placeholder="Escribe una respuesta"
                  ></textarea>
                  <button
                    type="button"
                    :disabled="enviando"
                    @click="responder(conversacion.id)"
                  >
                    Responder
                  </button>
                </div>
              </div>
            </div>
          </details>
        </div>
      </details>
    </section>

    <section v-else class="estado-vacio">
      <i class="bi bi-inbox"></i>
      <h2>No hay conversaciones en esta sección</h2>
    </section>

    <p v-if="error && mensajes.length" class="feedback-error">{{ error }}</p>
  </main>
</template>

<style scoped>
.mensajes-page {
  min-height: calc(100vh - 160px);
  padding: 48px max(24px, 6vw);
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.pagina-cabecera {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
}

.pagina-cabecera p {
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
  margin: 5px 0;
}

.pagina-cabecera span,
aside p {
  color: #654236;
}

.volver-btn,
.gato-btn,
.respuesta-form button {
  padding: 10px 18px;
  border: none;
  border-radius: 999px;
  background: #df9800;
  color: white;
  font-family: "coolvetica";
  text-decoration: none;
}

.filtros {
  display: flex;
  gap: 10px;
  margin: 28px 0;
}

.filtros a {
  padding: 8px 16px;
  border-radius: 999px;
  background: white;
  color: #654236;
  text-decoration: none;
}

.filtros a.router-link-exact-active {
  background: #df9800;
  color: white;
}

.usuarios-lista,
.conversaciones-usuario {
  display: grid;
  gap: 16px;
}

.usuario-desplegable,
.conversacion-desplegable {
  overflow: hidden;
  border-radius: 20px;
  background: white;
  box-shadow: 0 8px 24px rgba(66, 52, 48, 0.1);
}

.usuario-resumen,
.conversacion-resumen {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 20px 24px;
  color: #423430;
  cursor: pointer;
  list-style: none;
}

.usuario-resumen::-webkit-details-marker,
.conversacion-resumen::-webkit-details-marker {
  display: none;
}

.usuario-resumen > div,
.conversacion-resumen > div {
  display: flex;
  align-items: center;
  gap: 14px;
}

.usuario-resumen i {
  color: #df9800;
  font-size: 34px;
}

.usuario-resumen span,
.conversacion-resumen > div {
  display: grid;
}

summary small {
  color: #80695f;
}

.contador {
  min-width: 38px;
  padding: 7px 10px;
  border-radius: 999px;
  background: #df9800;
  color: white;
  text-align: center;
}

.conversaciones-usuario {
  padding: 0 20px 20px;
}

.conversacion-desplegable {
  border: 1px solid #f1dc7a;
  box-shadow: none;
}

.conversacion-card {
  display: grid;
  grid-template-columns: 250px 1fr;
  overflow: hidden;
  border-top: 1px solid #f1dc7a;
}

aside {
  padding: 24px;
  background: #fff8d6;
}

aside img,
.icono-general {
  width: 100%;
  height: 170px;
  border-radius: 16px;
  object-fit: cover;
}

.icono-general {
  display: grid;
  place-items: center;
  background: #f1dc7a;
  color: #654236;
  font-size: 48px;
}

.estado {
  display: inline-block;
  margin: 0;
  padding: 5px 10px;
  border-radius: 999px;
  background: #dff3d8;
  color: #2f6b3d;
}

.estado.pendiente {
  background: #fff3c4;
  color: #9a6500;
}

.gato-btn {
  display: flex;
  justify-content: center;
  margin-top: 14px;
}

.contenido {
  padding: 24px;
}

.contenido > h2 {
  margin-top: 0;
}

.historial {
  display: grid;
  gap: 12px;
  max-height: 420px;
  padding-right: 6px;
  overflow-y: auto;
}

.burbuja {
  max-width: 78%;
  padding: 12px 14px;
  border-radius: 14px;
  background: #fff8d6;
  color: #423430;
}

.burbuja.admin {
  justify-self: end;
  background: #f1dc7a;
}

.burbuja p {
  margin: 5px 0;
}

.burbuja small {
  color: #80695f;
}

.respuesta-form {
  display: grid;
  gap: 10px;
  margin-top: 18px;
}

.respuesta-form textarea {
  min-height: 90px;
  padding: 12px;
  border: 2px solid #f1dc7a;
  border-radius: 14px;
  resize: vertical;
}

.respuesta-form button {
  justify-self: end;
  cursor: pointer;
}

.estado-vacio {
  padding: 38px 24px;
  border-radius: 22px;
  background: white;
  color: #654236;
  text-align: center;
}

.estado-vacio > i {
  color: #df9800;
  font-size: 48px;
}

.estado-vacio.error,
.feedback-error {
  color: #9c3434;
}

@media (max-width: 800px) {
  .conversacion-card {
    grid-template-columns: 1fr;
  }

  .pagina-cabecera {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
