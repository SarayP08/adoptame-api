<script setup>
  import {
  ref,
  computed,
  onMounted
} from 'vue';
  import { useRoute } from 'vue-router';
  const route = useRoute();
  import {
  API_URL,
  buildCatImage
} from '../../config/api.js';

  import {
  useRouter
} from 'vue-router';

  import {
  useAuthStore
} from '../../stores/auth.js';

  const router = useRouter();

  const auth = useAuthStore();

  const mostrarModalAuth = ref(false);

  const gatoPendiente = ref(null);

  /* FILTROS */

  const filtroSexo = ref('');

  const filtroCastrado = ref('');

  /* DATOS */

  const gatos = ref([]);

  const paginaActual = ref(1);

  const gatosPorPagina = 8;

  /* CARGAR GATOS */

  onMounted(async () => {

  const res = await fetch(
  `${API_URL}/api/despliegueGatos.php`
  );

  gatos.value = await res.json();
});

  /* FILTRADO */

  const gatosFiltrados = computed(() => {

  return gatos.value.filter(gato => {

  const coincideSexo =

  !filtroSexo.value
  ||

  gato.sexo === filtroSexo.value;

  const coincideCastrado =

  !filtroCastrado.value
  ||

  gato.castrado === filtroCastrado.value;

  return (
  coincideSexo
  &&
  coincideCastrado
  );
});
});

  /* PAGINACIÓN */

  const totalPaginas = computed(() => {

  return Math.ceil(
  gatosFiltrados.value.length / gatosPorPagina
  );
});

  const gatosPaginados = computed(() => {

  const inicio =

  (paginaActual.value - 1)
  *
  gatosPorPagina;

  const fin = inicio + gatosPorPagina;

  return gatosFiltrados.value.slice(
  inicio,
  fin
  );
});

  const cambiarPagina = (pagina) => {

  if (
  pagina >= 1
  &&
  pagina <= totalPaginas.value
  ) {

  paginaActual.value = pagina;
}
};

  /* HELPERS */

  const mostrarCastrado = (valor) => {

  return (

  valor === true
  ||

  valor === 1
  ||

  valor === '1'
  ||

  valor === 'si'
  ||

  valor === 'sí'

  )

  ? 'Sí'

  : 'No';
};

  /* ADOPTAR */

  const adoptar = (id) => {

    if (!auth.logueado) {

      gatoPendiente.value = id;

      mostrarModalAuth.value = true;

      return;
    }
    router.push(`/adoptar/${id}`);
  };
</script>



<template>

  <div class="container mt-5 gatos-page">
    <h1 class="mb-4 text-center titulo-gatos">Gatos en adopción</h1>
    <section class="filters-box">

      <div class="filter-group">

        <label>Por sexo
        </label>

        <select v-model="filtroSexo">

          <option value="">
            Todos
          </option>

          <option value="macho">
            Macho
          </option>

          <option value="hembra">
            Hembra
          </option>

        </select>

      </div>

      <div class="filter-group">

        <label>
          Castrado
        </label>

        <select v-model="filtroCastrado">

          <option value="">
            Todos
          </option>

          <option value="si">
            Sí
          </option>

          <option value="no">
            No
          </option>

        </select>

      </div>

    </section>
    <div v-if="gatosPaginados.length" class="row">
      <div
        v-for="gato in gatosPaginados"
        :key="gato.id"
        class="col-12 col-sm-6 col-lg-3 mb-4"
      >
        <div class="card h-100 shadow-sm gato-card">
          <img
              :src="buildCatImage(gato.imagen)"
            class="card-img-top gato-imagen"
            :alt="`Imagen de ${gato.nombre}`"
          />


          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ gato.nombre }}</h5>

            <ul class="list-unstyled gato-info">
              <li>
                <strong>Descripción:</strong> {{ gato.descripcion }}
              </li>
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

              <button
                  class="btn btn-primary flex-fill"
                  @click="adoptar(gato.id)"
              >
                Adoptar
              </button>

              <button
                  class="btn btn-primary flex-fill"
                  @click="adoptar(gato.id)"
              >
                Acoger
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

  <!-- MODAL AUTH -->

  <div
      v-if="mostrarModalAuth"
      class="modal-overlay"
  >

    <div class="auth-modal">

      <div class="modal-icon">
        <i class="bi bi-heart-fill"></i>
      </div>

      <h2>
        Necesitas una cuenta
      </h2>

      <p>
        Inicia sesión o regístrate para enviar
        solicitudes de adopción.
      </p>

      <div class="modal-actions">

        <RouterLink
            :to="`/iniciarSesion?redirect=/adoptar/${gatoPendiente}`"
            class="modal-btn primary"
        >
          Iniciar sesión
        </RouterLink>

        <RouterLink
            to="/registro"
            class="modal-btn secondary"
        >
          Registrarse
        </RouterLink>

      </div>

      <button
          class="close-btn"
          @click="mostrarModalAuth = false"
      >
        Cerrar
      </button>

    </div>

  </div>
</template>

<style scoped>
.filters-box {
  display: flex;
  justify-content: center;
  gap: 20px;

  margin-bottom: 32px;
}

.filter-group {
  display: grid;
  gap: 8px;
}

.filter-group label {
  color: #423430;

  font-family: 'coolvetica';
  font-size: 18px;
}

.filter-group select {
  min-width: 180px;

  padding: 12px 14px;

  border: 2px solid #f1dc7a;
  border-radius: 14px;

  background-color: #fffdf2;

  color: #423430;

  font-size: 16px;

  outline: none;
}

.filter-group select:focus {
  border-color: #df9800;

  box-shadow:
      0 0 0 4px rgba(223, 152, 0, 0.15);
}

.modal-overlay {
  position: fixed;
  inset: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  background:
      rgba(0,0,0,0.45);

  z-index: 2000;
}

.auth-modal {
  width: 100%;
  max-width: 420px;

  padding: 32px;

  background-color: white;

  border-radius: 24px;

  text-align: center;

  box-shadow:
      0 12px 40px rgba(0,0,0,0.2);
}

.modal-icon {
  width: 74px;
  height: 74px;

  display: grid;
  place-items: center;

  margin: 0 auto 20px;

  border-radius: 50%;

  background-color: #fff3c4;

  color: #df9800;

  font-size: 34px;
}

.auth-modal h2 {
  margin-bottom: 12px;

  color: #423430;

  font-family: 'coolvetica';
  font-size: 34px;
}

.auth-modal p {
  margin-bottom: 24px;

  color: #654236;

  font-size: 17px;
}

.modal-actions {
  display: flex;
  gap: 12px;

  margin-bottom: 18px;
}

.modal-btn {
  flex: 1;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  padding: 12px 18px;

  border-radius: 999px;

  text-decoration: none;

  font-family: 'coolvetica';
  font-size: 17px;

  transition:
      background-color 0.2s ease,
      transform 0.2s ease;
}

.modal-btn.primary {
  background-color: #df9800;
  color: white;
}

.modal-btn.primary:hover {
  background-color: #c78300;

  transform: translateY(-2px);
}

.modal-btn.secondary {
  background-color: #faf8b3;
  color: #654236;
}

.modal-btn.secondary:hover {
  background-color: #f1dc7a;

  transform: translateY(-2px);
}

.close-btn {
  border: none;

  background: transparent;

  color: #654236;

  font-size: 15px;

  cursor: pointer;
}

.close-btn:hover {
  text-decoration: underline;
}
</style>