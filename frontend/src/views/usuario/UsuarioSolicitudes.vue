<script setup>
import { computed, onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import { API_URL, buildCatImage } from "../../config/api.js";
import { useAuthStore } from "../../stores/auth.js";

const auth = useAuthStore();
const solicitudes = ref([]);
const cargando = ref(true);

const normalizarTipo = (solicitud) =>
  String(solicitud.tipo ?? solicitud.tipo_solicitud ?? solicitud.estado_gato ?? "")
    .toLowerCase()
    .trim();

const solicitudesAdopcion = computed(() =>
  solicitudes.value.filter((solicitud) => normalizarTipo(solicitud) === "adopcion"),
);

const solicitudesAcogida = computed(() =>
  solicitudes.value.filter((solicitud) => normalizarTipo(solicitud) === "acogida"),
);

const nombreGato = (solicitud) =>
  solicitud.gato_nombre ?? solicitud.nombre_gato ?? solicitud.nombre ?? "Gato";

const imagenGato = (solicitud) =>
  buildCatImage(solicitud.gato_imagen ?? solicitud.imagen ?? "");

const fechaSolicitud = (solicitud) => {
  const fecha = solicitud.fecha ?? solicitud.fecha_solicitud ?? solicitud.created_at;
  return fecha
    ? new Intl.DateTimeFormat("es-ES", { dateStyle: "medium" }).format(new Date(fecha))
    : "Fecha pendiente";
};

const claseEstado = (estado) =>
  `estado-${String(estado || "pendiente").toLowerCase().trim()}`;

onMounted(async () => {
  try {
    if (!auth.usuario?.id) {
      await auth.comprobarSesion();
    }

    const respuesta = await fetch(
      `${API_URL}/api/gatos/solicitudGato.php?usuario_id=${auth.usuario?.id}`,
      { credentials: "include" },
    );
    const texto = await respuesta.text();
    const datos = texto ? JSON.parse(texto) : [];

    solicitudes.value = Array.isArray(datos) ? datos : datos.solicitudes ?? [];
  } catch (error) {
    console.error("No se pudieron cargar las solicitudes", error);
    solicitudes.value = [];
  } finally {
    cargando.value = false;
  }
});
</script>

<template>
  <main class="solicitudes-page">
    <header class="solicitudes-header">
      <p>Área personal</p>
      <h1>Mis solicitudes</h1>
      <span>Consulta por separado tus solicitudes de adopción y acogida.</span>
    </header>

    <p v-if="cargando" class="mensaje-estado">Cargando solicitudes...</p>

    <template v-else>
      <section class="tipo-solicitud">
        <div class="seccion-titulo">
          <div>
            <p>Adopción</p>
            <h2>Solicitudes de adopción</h2>
          </div>
          <span>{{ solicitudesAdopcion.length }}</span>
        </div>

        <div v-if="solicitudesAdopcion.length" class="solicitudes-grid">
          <article
            v-for="solicitud in solicitudesAdopcion"
            :key="solicitud.id"
            class="solicitud-card"
          >
            <img :src="imagenGato(solicitud)" :alt="`Imagen de ${nombreGato(solicitud)}`" />
            <div class="card-contenido">
              <div class="card-cabecera">
                <h3>{{ nombreGato(solicitud) }}</h3>
                <span :class="['estado', claseEstado(solicitud.estado)]">
                  {{ solicitud.estado || "Pendiente" }}
                </span>
              </div>
              <p><i class="bi bi-calendar3"></i> {{ fechaSolicitud(solicitud) }}</p>
              <div
                v-if="solicitud.estado === 'rechazada' && solicitud.comentario_admin"
                class="comentario-rechazo"
              >
                <strong>Motivo del rechazo</strong>
                <p>{{ solicitud.comentario_admin }}</p>
              </div>
              <RouterLink
                v-if="solicitud.gato_id"
                :to="`/detalleGato/${solicitud.gato_id}`"
                class="detalle-btn"
              >
                Ver gato
              </RouterLink>
            </div>
          </article>
        </div>

        <div v-else class="seccion-vacia">
          <i class="bi bi-house-heart"></i>
          <p>No tienes solicitudes de adopción.</p>
        </div>
      </section>

      <section class="tipo-solicitud">
        <div class="seccion-titulo">
          <div>
            <p>Acogida</p>
            <h2>Solicitudes de acogida</h2>
          </div>
          <span>{{ solicitudesAcogida.length }}</span>
        </div>

        <div v-if="solicitudesAcogida.length" class="solicitudes-grid">
          <article
            v-for="solicitud in solicitudesAcogida"
            :key="solicitud.id"
            class="solicitud-card"
          >
            <img :src="imagenGato(solicitud)" :alt="`Imagen de ${nombreGato(solicitud)}`" />
            <div class="card-contenido">
              <div class="card-cabecera">
                <h3>{{ nombreGato(solicitud) }}</h3>
                <span :class="['estado', claseEstado(solicitud.estado)]">
                  {{ solicitud.estado || "Pendiente" }}
                </span>
              </div>
              <p><i class="bi bi-calendar3"></i> {{ fechaSolicitud(solicitud) }}</p>
              <div
                v-if="solicitud.estado === 'rechazada' && solicitud.comentario_admin"
                class="comentario-rechazo"
              >
                <strong>Motivo del rechazo</strong>
                <p>{{ solicitud.comentario_admin }}</p>
              </div>
              <RouterLink
                v-if="solicitud.gato_id"
                :to="`/detalleGato/${solicitud.gato_id}`"
                class="detalle-btn"
              >
                Ver gato
              </RouterLink>
            </div>
          </article>
        </div>

        <div v-else class="seccion-vacia">
          <i class="bi bi-clock-history"></i>
          <p>No tienes solicitudes de acogida.</p>
        </div>
      </section>
    </template>
  </main>
</template>

<style scoped>
.solicitudes-page {
  min-height: calc(100vh - 160px);
  padding: 48px max(24px, 7vw);
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.solicitudes-header {
  margin-bottom: 36px;
}

.solicitudes-header p,
.seccion-titulo p {
  margin: 0 0 5px;
  color: #df9800;
  font-family: "coolvetica";
  font-size: 18px;
}

.solicitudes-header h1,
.seccion-titulo h2,
.solicitud-card h3 {
  color: #423430;
  font-family: "coolvetica";
}

.solicitudes-header h1 {
  margin: 0 0 8px;
  font-size: clamp(38px, 5vw, 54px);
}

.solicitudes-header > span {
  color: #654236;
}

.tipo-solicitud {
  margin-bottom: 42px;
}

.seccion-titulo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.seccion-titulo h2 {
  margin: 0;
  font-size: 30px;
}

.seccion-titulo > span {
  min-width: 42px;
  padding: 8px 12px;
  border-radius: 999px;
  background: #df9800;
  color: white;
  text-align: center;
  font-family: "coolvetica";
}

.solicitudes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 320px));
  gap: 24px;
}

.solicitud-card {
  overflow: hidden;
  background: white;
  border-radius: 22px;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.solicitud-card > img {
  width: 100%;
  height: 210px;
  object-fit: cover;
}

.card-contenido {
  padding: 20px;
}

.card-cabecera {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.card-cabecera h3 {
  margin: 0;
  font-size: 24px;
}

.card-contenido p {
  margin: 14px 0;
  color: #654236;
}

.estado {
  padding: 5px 10px;
  border-radius: 999px;
  background: #fff3c4;
  color: #9a6500;
  font-size: 13px;
  text-transform: capitalize;
}

.estado-aprobada,
.estado-aceptada {
  background: #dff3d8;
  color: #2f6b3d;
}

.estado-rechazada {
  background: #f8dddd;
  color: #9c3434;
}

.comentario-rechazo {
  margin: 14px 0;
  padding: 12px;
  border-radius: 12px;
  background: #f8dddd;
  color: #7d2929;
}

.comentario-rechazo p {
  margin: 5px 0 0;
  color: inherit;
}

.detalle-btn {
  display: inline-flex;
  width: 100%;
  justify-content: center;
  padding: 10px 18px;
  border-radius: 999px;
  background: #df9800;
  color: white;
  font-family: "coolvetica";
  text-decoration: none;
}

.seccion-vacia,
.mensaje-estado {
  padding: 34px 24px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.82);
  color: #654236;
  text-align: center;
}

.seccion-vacia i {
  display: block;
  margin-bottom: 8px;
  color: #df9800;
  font-size: 38px;
}

.seccion-vacia p {
  margin: 0;
}
</style>
