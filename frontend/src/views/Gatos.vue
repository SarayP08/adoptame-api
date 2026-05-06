<script setup>
import { ref, computed, onMounted } from 'vue';
import { API_URL, buildUrl } from '../config/api';

const gatos = ref([]);
const paginaActual = ref(1);
const gatosPorPagina = 16;

onMounted(async () => {
  const res = await fetch("http://localhost/adoptame-api/backend/api/despliegueGatos.php");
  gatos.value = await res.json();
});

const totalPaginas = computed(() => {
  return Math.ceil(gatos.value.length / gatosPorPagina);
});

const gatosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * gatosPorPagina;
  const fin = inicio + gatosPorPagina;

  return gatos.value.slice(inicio, fin);
});

const cambiarPagina = (pagina) => {
  if (pagina >= 1 && pagina <= totalPaginas.value) {
    paginaActual.value = pagina;
  }
};

const mostrarCastrado = (valor) => {
  return valor === true || valor === 1 || valor === '1' || valor === 'si' || valor === 'sí'
    ? 'Sí'
    : 'No';
};
</script>


<template>

  <div class="container mt-5 gatos-page">
    <h1 class="mb-4 text-center titulo-gatos">Gatetes en adopción 🐱</h1>

    <div v-if="gatosPaginados.length" class="row">
      <div
        v-for="gato in gatosPaginados"
        :key="gato.id"
        class="col-12 col-sm-6 col-lg-3 mb-4"
      >
        <div class="card h-100 shadow-sm gato-card">
          <img
            :src="buildUrl(gato.imagen)"
            class="card-img-top gato-imagen"
            :alt="`Imagen de ${gato.nombre}`"
          />


          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ gato.nombre }}</h5>

            <ul class="list-unstyled gato-info">
              <li>
                <strong>Edad:</strong> {{ gato.edad }}
              </li>
              <li>
                <strong>Sexo:</strong> {{ gato.sexo }}
              </li>
              <li>
                <strong>Castrado:</strong> {{ mostrarCastrado(gato.castrado) }}
              </li>
            </ul>

            <div class="mt-auto d-flex gap-2">
              <button class="btn btn-primary flex-fill">
                Adoptar
              </button>

              <RouterLink
                :to="`/detalleGato/${gato.id}`"
                class="btn btn-outline-primary flex-fill"
              >
                Ver más
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-else class="text-center mt-4">
      Ahora mismo no hay gatetes disponibles.
    </p>

    <nav
      v-if="totalPaginas > 1"
      class="d-flex justify-content-center mt-4"
      aria-label="Paginación de gatos"
    >
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: paginaActual === 1 }">
          <button
            class="page-link"
            type="button"
            @click="cambiarPagina(paginaActual - 1)"
          >
            Anterior
          </button>
        </li>

        <li
          v-for="pagina in totalPaginas"
          :key="pagina"
          class="page-item"
          :class="{ active: paginaActual === pagina }"
        >
          <button
            class="page-link"
            type="button"
            @click="cambiarPagina(pagina)"
          >
            {{ pagina }}
          </button>
        </li>

        <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
          <button
            class="page-link"
            type="button"
            @click="cambiarPagina(paginaActual + 1)"
          >
            Siguiente
          </button>
        </li>
      </ul>
    </nav>
  </div>
</template>