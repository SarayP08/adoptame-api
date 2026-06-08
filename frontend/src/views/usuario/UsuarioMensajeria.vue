<script setup>
import { computed, nextTick, onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import { API_URL, buildCatImage } from "../../config/api.js";
import { useAuthStore } from "../../stores/auth.js";

const auth = useAuthStore();
const mensajes = ref([]);
const cargando = ref(true);
const error = ref("");
const enviando = ref(false);
const mostrarNuevo = ref(false);
const nuevoMensaje = ref({ asunto: "", contenido: "" });
const respuestas = ref({});
const historiales = ref({});

const conversaciones = computed(() => {
  const agrupadas = new Map();

  mensajes.value.forEach((mensaje) => {
    if (!agrupadas.has(mensaje.conversacion_id)) {
      agrupadas.set(mensaje.conversacion_id, {
        id: mensaje.conversacion_id,
        asunto: mensaje.asunto || `Solicitud de ${mensaje.tipo_solicitud}`,
        gato_id: mensaje.gato_id,
        gato_nombre: mensaje.gato_nombre,
        gato_imagen: mensaje.gato_imagen,
        mensajes: [],
      });
    }

    agrupadas.get(mensaje.conversacion_id).mensajes.push(mensaje);
  });

  return [...agrupadas.values()];
});

const formatearFecha = (fecha) => {
  if (!fecha) return "Fecha por confirmar";

  return new Intl.DateTimeFormat("es-ES", {
    dateStyle: "long",
    timeStyle: "short",
  }).format(new Date(fecha));
};

const cargarMensajes = async () => {
  try {
    error.value = "";
    const respuesta = await fetch(`${API_URL}/api/usuarios/mensajesUsuario.php`, {
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

const enviarMensaje = async ({ conversacionId = 0, asunto = "", contenido = "" }) => {
  if (!contenido.trim()) {
    error.value = "Escribe un mensaje antes de enviarlo.";
    return;
  }

  try {
    enviando.value = true;
    error.value = "";
    const respuesta = await fetch(`${API_URL}/api/usuarios/mensajesUsuario.php`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        conversacion_id: conversacionId,
        asunto,
        contenido: contenido.trim(),
      }),
    });
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.success) {
      throw new Error(datos.error || "No se pudo enviar el mensaje");
    }

    respuestas.value[conversacionId] = "";
    nuevoMensaje.value = { asunto: "", contenido: "" };
    mostrarNuevo.value = false;
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
        <p>Área personal</p>
        <h1>Mensajes</h1>
        <span>Contacta con el equipo y responde a tus conversaciones.</span>
      </div>
      <button type="button" class="nuevo-btn" @click="mostrarNuevo = !mostrarNuevo">
        <i class="bi bi-plus-circle"></i> Nuevo mensaje
      </button>
    </header>

    <section v-if="mostrarNuevo" class="nuevo-form">
      <h2>Iniciar conversación</h2>
      <label for="nuevo-asunto">Asunto</label>
      <input id="nuevo-asunto" v-model="nuevoMensaje.asunto" type="text" />
      <label for="nuevo-contenido">Mensaje</label>
      <textarea id="nuevo-contenido" v-model="nuevoMensaje.contenido"></textarea>
      <button
        type="button"
        :disabled="enviando"
        @click="enviarMensaje({ asunto: nuevoMensaje.asunto, contenido: nuevoMensaje.contenido })"
      >
        Enviar mensaje
      </button>
    </section>

    <p v-if="cargando" class="estado-vacio">Cargando mensajes...</p>
    <p v-else-if="error" class="estado-vacio error">{{ error }}</p>

    <section v-else-if="conversaciones.length" class="mensajes-grid">
      <details
        v-for="conversacion in conversaciones"
        :key="conversacion.id"
        class="mensaje-card"
        :class="{ general: !conversacion.gato_id }"
      >
        <summary class="conversacion-resumen">
          <div>
            <span>{{ conversacion.gato_id ? "Conversación de solicitud" : "Consulta general" }}</span>
            <h2>{{ conversacion.asunto }}</h2>
          </div>
          <i class="bi bi-chevron-down"></i>
        </summary>

        <img
          v-if="conversacion.gato_imagen"
          :src="buildCatImage(conversacion.gato_imagen)"
          :alt="conversacion.gato_nombre"
        />

        <div class="mensaje-contenido">
          <div
            :ref="(elemento) => { historiales[conversacion.id] = elemento; }"
            class="historial"
          >
            <div
              v-for="mensaje in conversacion.mensajes"
              :key="mensaje.id"
              class="burbuja"
              :class="{ propia: Number(mensaje.remitente_id) === Number(auth.usuario?.id) }"
            >
              <template v-if="mensaje.contenido.tipo === 'solicitud_aceptada'">
                <strong>Tu solicitud ha sido aceptada</strong>
                <p><i class="bi bi-calendar-event"></i> {{ formatearFecha(mensaje.contenido.fecha) }}</p>
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
              @click="enviarMensaje({
                conversacionId: conversacion.id,
                contenido: respuestas[conversacion.id] || '',
              })"
            >
              Responder
            </button>
          </div>

          <RouterLink
            v-if="conversacion.gato_id"
            :to="`/detalleGato/${conversacion.gato_id}`"
            class="detalle-btn"
          >
            Ver gato
          </RouterLink>
        </div>
      </details>
    </section>

    <section v-else class="estado-vacio">
      <i class="bi bi-envelope"></i>
      <h2>No tienes mensajes</h2>
      <p>Las comunicaciones sobre tus solicitudes aparecerán aquí.</p>
    </section>
  </main>
</template>

<style scoped>
.mensajes-page {
  min-height: calc(100vh - 160px);
  padding: 48px max(24px, 7vw);
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.pagina-cabecera {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 32px;
}

.pagina-cabecera p {
  margin: 0;
  color: #df9800;
  font-family: "coolvetica";
  font-size: 18px;
}

h1,
h2 {
  color: #423430;
  font-family: "coolvetica";
}

h1 {
  margin: 5px 0;
  font-size: clamp(38px, 5vw, 54px);
}

.pagina-cabecera span {
  color: #654236;
}

.mensajes-grid {
  display: grid;
  gap: 24px;
}

.mensaje-card {
  display: grid;
  grid-template-columns: 240px 1fr;
  max-width: 900px;
  overflow: hidden;
  border-radius: 22px;
  background: white;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.mensaje-card.general {
  grid-template-columns: 1fr;
}

.conversacion-resumen {
  grid-column: 1 / -1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  cursor: pointer;
  list-style: none;
}

.conversacion-resumen::-webkit-details-marker {
  display: none;
}

.conversacion-resumen span {
  color: #df9800;
}

.conversacion-resumen h2 {
  margin: 3px 0 0;
}

.conversacion-resumen > i {
  color: #654236;
  transition: transform 0.2s ease;
}

.mensaje-card[open] .conversacion-resumen > i {
  transform: rotate(180deg);
}

.mensaje-card > img {
  width: 100%;
  height: 100%;
  min-height: 310px;
  object-fit: cover;
}

.mensaje-contenido {
  padding: 24px;
}

.nuevo-btn,
.nuevo-form button,
.respuesta-form button {
  padding: 11px 18px;
  border: none;
  border-radius: 999px;
  background: #df9800;
  color: white;
  font-family: "coolvetica";
  cursor: pointer;
}

.nuevo-form,
.respuesta-form {
  display: grid;
  gap: 10px;
}

.nuevo-form {
  max-width: 700px;
  margin-bottom: 24px;
  padding: 24px;
  border-radius: 22px;
  background: white;
}

.nuevo-form input,
.nuevo-form textarea,
.respuesta-form textarea {
  padding: 12px;
  border: 2px solid #f1dc7a;
  border-radius: 14px;
}

.nuevo-form textarea,
.respuesta-form textarea {
  min-height: 100px;
  resize: vertical;
}

.historial {
  display: grid;
  gap: 12px;
  margin: 18px 0;
  max-height: 420px;
  padding-right: 6px;
  overflow-y: auto;
}

.burbuja {
  max-width: 80%;
  padding: 12px 14px;
  border-radius: 14px;
  background: #fff8d6;
  color: #423430;
}

.burbuja.propia {
  justify-self: end;
  background: #f1dc7a;
}

.burbuja p {
  margin: 4px 0;
}

.burbuja small {
  color: #80695f;
}

.detalle-btn {
  display: inline-flex;
  margin-top: 10px;
  padding: 10px 18px;
  border-radius: 999px;
  background: #df9800;
  color: white;
  font-family: "coolvetica";
  text-decoration: none;
}

.estado-vacio {
  max-width: 700px;
  padding: 40px 24px;
  border-radius: 22px;
  background: white;
  color: #654236;
  text-align: center;
}

.estado-vacio > i {
  color: #df9800;
  font-size: 48px;
}

.estado-vacio.error {
  color: #9c3434;
}

@media (max-width: 700px) {
  .mensaje-card {
    grid-template-columns: 1fr;
  }

  .pagina-cabecera {
    align-items: flex-start;
    flex-direction: column;
  }

  .mensaje-card > img {
    height: 220px;
    min-height: auto;
  }
}
</style>
