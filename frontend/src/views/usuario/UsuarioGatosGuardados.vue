<script setup>
import { onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import { buildCatImage } from "../../config/api.js";
import {
  alternarGatoGuardado,
  obtenerGatosGuardados,
} from "../../services/gatosServices.js";
import { useAuthStore } from "../../stores/auth.js";

const auth = useAuthStore();
const gatosGuardados = ref([]);

const cargarGuardados = () => {
  gatosGuardados.value = obtenerGatosGuardados(auth.usuario?.id);
};

const quitarGuardado = (gato) => {
  alternarGatoGuardado(auth.usuario?.id, gato);
  cargarGuardados();
};

onMounted(async () => {
  if (!auth.usuario?.id) {
    await auth.comprobarSesion();
  }

  cargarGuardados();
});
</script>

<template>
  <main class="guardados-page">
    <div class="guardados-header">
      <div>
        <p class="seccion-label">Tu selección</p>
        <h1>Gatos guardados</h1>
      </div>

      <RouterLink to="/gatos" class="buscar-btn">Buscar gatos</RouterLink>
    </div>

    <section v-if="gatosGuardados.length" class="guardados-grid">
      <article v-for="gato in gatosGuardados" :key="gato.id" class="gato-guardado">
        <img :src="buildCatImage(gato.imagen)" :alt="`Imagen de ${gato.nombre}`" />

        <div class="gato-contenido">
          <div class="gato-titulo">
            <h2>{{ gato.nombre }}</h2>
            <button
              type="button"
              :aria-label="`Quitar a ${gato.nombre} de guardados`"
              title="Quitar de guardados"
              @click="quitarGuardado(gato)"
            >
              <i class="bi bi-heart-fill"></i>
            </button>
          </div>

          <p>{{ gato.descripcion }}</p>

          <RouterLink :to="`/detalleGato/${gato.id}`" class="detalle-btn">
            Ver detalles
          </RouterLink>
        </div>
      </article>
    </section>

    <section v-else class="guardados-vacio">
      <i class="bi bi-heart"></i>
      <h2>Aún no has guardado ningún gato</h2>
      <p>Usa el corazón de las cartas para añadirlos a esta lista.</p>
      <RouterLink to="/gatos" class="buscar-btn">Ver gatos</RouterLink>
    </section>
  </main>
</template>

<style scoped>
.guardados-page {
  min-height: calc(100vh - 160px);
  padding: 48px max(24px, 7vw);
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.guardados-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 32px;
}

.seccion-label {
  margin: 0 0 4px;
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
  margin: 0;
  font-size: clamp(36px, 5vw, 54px);
}

.guardados-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 320px));
  justify-content: center;
  gap: 24px;
}

.gato-guardado {
  width: 100%;
  max-width: 320px;
  overflow: hidden;
  background: white;
  border-radius: 22px;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.gato-guardado > img {
  width: 100%;
  height: 260px;
  object-fit: cover;
}

.gato-contenido {
  padding: 22px;
}

.gato-titulo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.gato-titulo h2 {
  margin: 0;
}

.gato-titulo button {
  border: none;
  background: transparent;
  color: #c84d5a;
  font-size: 24px;
  cursor: pointer;
}

.gato-contenido p,
.guardados-vacio p {
  color: #654236;
}

.buscar-btn,
.detalle-btn {
  display: inline-flex;
  justify-content: center;
  padding: 11px 20px;
  border-radius: 999px;
  background: #df9800;
  color: white;
  font-family: "coolvetica";
  text-decoration: none;
}

.detalle-btn {
  width: 100%;
}

.guardados-vacio {
  max-width: 620px;
  margin: 70px auto;
  padding: 48px 24px;
  background: white;
  border-radius: 24px;
  text-align: center;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.guardados-vacio > i {
  color: #df9800;
  font-size: 58px;
}

@media (max-width: 600px) {
  .guardados-header {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
