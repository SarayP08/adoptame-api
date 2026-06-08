<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { API_URL, buildCatImage } from "../../config/api.js";
import {
  alternarGatoGuardado,
  estaGatoGuardado,
} from "../../services/gatosServices.js";
import { useAuthStore } from "../../stores/auth.js";
import "../../assets/css/css_gatos.css";

const router = useRouter();
const auth = useAuthStore();

const mostrarModalAuth = ref(false);
const gatoPendiente = ref(null);
const filtroSexo = ref("");
const filtroCastrado = ref("");
const gatos = ref([]);
const paginaActual = ref(1);
const gatosPorPagina = 8;
const idsGuardados = ref(new Set());

/* CARGAR GATOS */

onMounted(async () => {
  const res = await fetch(`${API_URL}/api/gatos/despliegueGatos.php`);
  gatos.value = await res.json();
});

const solicitar = (id, tipo) => {
  if (!auth.logueado) {
    gatoPendiente.value = { id, tipo };
    mostrarModalAuth.value = true;
    return;
  }

  router.push(`/adoptar/${id}?tipo=${tipo}`);
};

const guardarGato = (gato) => {
  if (!auth.logueado) {
    gatoPendiente.value = { id: gato.id, tipo: "guardado" };
    mostrarModalAuth.value = true;
    return;
  }

  const guardado = alternarGatoGuardado(auth.usuario?.id, gato);
  const nuevosIds = new Set(idsGuardados.value);

  if (guardado) {
    nuevosIds.add(String(gato.id));
  } else {
    nuevosIds.delete(String(gato.id));
  }

  idsGuardados.value = nuevosIds;
};

const estaGuardado = (id) =>
  idsGuardados.value.has(String(id)) ||
  (auth.logueado && estaGatoGuardado(auth.usuario?.id, id));

const destinoLogin = computed(() =>
  gatoPendiente.value?.tipo === "guardado"
    ? "/iniciarSesion?redirect=/gatos"
    : `/iniciarSesion?redirect=/adoptar/${gatoPendiente.value?.id}?tipo=${gatoPendiente.value?.tipo}`,
);

const textoModal = computed(() =>
  gatoPendiente.value?.tipo === "guardado"
    ? "Inicia sesión o regístrate para guardar gatos en tu perfil."
    : "Inicia sesión o regístrate para enviar solicitudes de adopción.",
);

/* FILTRADO */

const gatosFiltrados = computed(() => {
  return gatos.value.filter((gato) => {
    const coincideSexo = !filtroSexo.value || gato.sexo === filtroSexo.value;
    const coincideCastrado =
      !filtroCastrado.value || gato.castrado === filtroCastrado.value;

    return coincideSexo && coincideCastrado;
  });
});

/* PAGINACIÓN */

const totalPaginas = computed(() => {
  return Math.ceil(gatosFiltrados.value.length / gatosPorPagina);
});

const gatosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * gatosPorPagina;
  const fin = inicio + gatosPorPagina;

  return gatosFiltrados.value.slice(inicio, fin);
});

const cambiarPagina = (pagina) => {
  if (pagina >= 1 && pagina <= totalPaginas.value) {
    paginaActual.value = pagina;
  }
};

/* HELPERS */

const mostrarCastrado = (valor) => {
  const estaCastrado =
    valor === true ||
    valor === 1 ||
    valor === "1" ||
    valor === "si" ||
    valor === "sí";

  return estaCastrado ? "Sí" : "No";
};
</script>

<template>
  <div class="container mt-5 gatos-page">
    <h1 class="mb-4 text-center titulo-gatos">Gatos en adopción</h1>

    <section class="filters-box">
      <div class="filter-group">
        <label>Por sexo</label>

        <select v-model="filtroSexo">
          <option value="">Todos</option>
          <option value="macho">Macho</option>
          <option value="hembra">Hembra</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Castrado</label>

        <select v-model="filtroCastrado">
          <option value="">Todos</option>
          <option value="si">Sí</option>
          <option value="no">No</option>
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
          <button
            type="button"
            class="guardar-gato-btn"
            :class="{ guardado: estaGuardado(gato.id) }"
            :aria-label="estaGuardado(gato.id) ? `Quitar a ${gato.nombre} de guardados` : `Guardar a ${gato.nombre}`"
            :title="estaGuardado(gato.id) ? 'Quitar de guardados' : 'Guardar gato'"
            @click="guardarGato(gato)"
          >
            <i :class="estaGuardado(gato.id) ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
          </button>

          <img
            :src="buildCatImage(gato.imagen)"
            class="card-img-top gato-imagen"
            :alt="`Imagen de ${gato.nombre}`"
          />

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ gato.nombre }}</h5>

            <ul class="list-unstyled gato-info">
              <li><strong>Descripción:</strong> {{ gato.descripcion }}</li>
              <li><strong>Edad:</strong> {{ gato.edad }}</li>
              <li><strong>Sexo:</strong> {{ gato.sexo }}</li>
              <li><strong>Castrado:</strong> {{ mostrarCastrado(gato.castrado) }}</li>
            </ul>

            <div class="mt-auto d-flex gap-2">
              <button class="btn btn-adoptar flex-fill" @click="solicitar(gato.id, 'adopcion')">
                Adoptar
              </button>

              <button class="btn btn-acoger flex-fill" @click="solicitar(gato.id, 'acogida')">
                Acoger
              </button>

              <RouterLink :to="`/detalleGato/${gato.id}`" class="btn btn-detalle flex-fill">
                Ver más
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-else class="text-center mt-4">Ahora mismo no hay gatos disponibles.</p>

    <nav
      v-if="totalPaginas > 1"
      class="d-flex justify-content-center mt-4"
      aria-label="Paginación de gatos"
    >
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: paginaActual === 1 }">
          <button class="page-link" type="button" @click="cambiarPagina(paginaActual - 1)">Anterior</button>
        </li>

        <li
          v-for="pagina in totalPaginas"
          :key="pagina"
          class="page-item"
          :class="{ active: paginaActual === pagina }"
        >
          <button class="page-link" type="button" @click="cambiarPagina(pagina)">
            {{ pagina }}
          </button>
        </li>

        <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
          <button class="page-link" type="button" @click="cambiarPagina(paginaActual + 1)">Siguiente</button>
        </li>
      </ul>
    </nav>
  </div>

  <div v-if="mostrarModalAuth" class="modal-overlay">
    <div class="auth-modal">
      <div class="modal-icon">
        <i class="bi bi-heart-fill"></i>
      </div>

      <h2>Necesitas una cuenta</h2>

      <p>{{ textoModal }}</p>

      <div class="modal-actions">
        <RouterLink
          :to="destinoLogin"
          class="modal-btn primary"
        >
          Iniciar sesión
        </RouterLink>
        <RouterLink to="/registro" class="modal-btn secondary"> Registrarse </RouterLink>
      </div>

      <button class="close-btn" @click="mostrarModalAuth = false">Cerrar</button>
    </div>
  </div>
</template>
