<script setup>
import { ref, computed, onMounted } from "vue";

import { API_URL, buildCatImage } from "../../../config/api.js";

const gatos = ref([]);
const cargando = ref(true);
const error = ref("");

const busqueda = ref("");
const gatoAEliminar = ref(null);
const eliminando = ref(false);
const errorEliminar = ref("");

const cargarGatos = async () => {
  try {
    cargando.value = true;

    const res = await fetch(`${API_URL}/api/gatos/despliegueGatos.php`);

    const data = await res.json();

    gatos.value = data;
  } catch (err) {
    console.error(err);

    error.value = "No se pudieron cargar los gatos";
  } finally {
    cargando.value = false;
  }
};

onMounted(() => {
  cargarGatos();
});

const gatosFiltrados = computed(() => {
  return gatos.value.filter((gato) => {
    return gato.nombre.toLowerCase().includes(busqueda.value.toLowerCase());
  });
});

const abrirModalEliminar = (gato) => {
  gatoAEliminar.value = gato;
  errorEliminar.value = "";
};

const cerrarModalEliminar = () => {
  if (eliminando.value) {
    return;
  }

  gatoAEliminar.value = null;
  errorEliminar.value = "";
};

const confirmarEliminarGato = async () => {
  if (!gatoAEliminar.value) {
    return;
  }

  try {
    eliminando.value = true;
    errorEliminar.value = "";

    const id = gatoAEliminar.value.id;
    const res = await fetch(`${API_URL}/api/gatos/eliminarGato.php?id=${id}`, {
      method: "DELETE",
    });

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.error);
    }

    gatos.value = gatos.value.filter((gato) => gato.id !== id);
    gatoAEliminar.value = null;
  } catch (err) {
    console.error(err);

    errorEliminar.value = "No se pudo eliminar el gato. Inténtalo de nuevo.";
  } finally {
    eliminando.value = false;
  }
};
</script>

<template>
  <main class="admin-gatos-page">
    <section class="top-section">
      <div>
        <p class="section-label">Panel administración</p>

        <h1>Gestión de gatos</h1>

        <p class="section-description">Consulta, edita y elimina gatos publicados.</p>
      </div>

      <RouterLink to="/panel-admin" class="back-btn">
        <i class="bi bi-arrow-left-circle"></i>
        Volver
      </RouterLink>

      <RouterLink to="/añadirGato" class="add-btn">
        <i class="bi bi-plus-circle"></i>
        Añadir gato
      </RouterLink>
    </section>

    <section class="filters-section">
      <input v-model="busqueda" type="text" placeholder="Buscar gato por nombre..." class="search-input" />
    </section>

    <section v-if="cargando" class="feedback-box">Cargando gatos...</section>

    <section v-else-if="error" class="feedback-box error">
      {{ error }}
    </section>

    <section v-else class="table-section">
      <table class="cats-table">
        <thead>
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Sexo</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="gato in gatosFiltrados" :key="gato.id">
            <td>
              <img :src="buildCatImage(gato.imagen)" :alt="gato.nombre" class="cat-image" />
            </td>

            <td>
              {{ gato.nombre }}
            </td>

            <td>
              {{ gato.edad }}
            </td>

            <td>
              {{ gato.sexo }}
            </td>

            <td>
              {{ gato.estado }}
            </td>

            <td>
              <div class="actions-cell">
                <RouterLink :to="`/editarGato/${gato.id}`" class="table-btn edit-btn">
                  <i class="bi bi-pencil-square"></i>
                </RouterLink>

                <button class="table-btn delete-btn" @click="abrirModalEliminar(gato)">
                  <i class="bi bi-trash3"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <Teleport to="body">
      <div v-if="gatoAEliminar" class="delete-modal-overlay" @click.self="cerrarModalEliminar">
        <section class="delete-modal" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
          <div class="delete-modal-icon">
            <i class="bi bi-trash3"></i>
          </div>

          <h2 id="delete-modal-title">Eliminar gato</h2>

          <p>
            Vas a eliminar a
            <strong>{{ gatoAEliminar.nombre }}</strong>
            del listado. Esta acción no se puede deshacer.
          </p>

          <p v-if="errorEliminar" class="delete-error">{{ errorEliminar }}</p>

          <div class="delete-modal-actions">
            <button type="button" class="modal-btn secondary" :disabled="eliminando" @click="cerrarModalEliminar">Cancelar</button>

            <button type="button" class="modal-btn danger" :disabled="eliminando" @click="confirmarEliminarGato">
              <span v-if="eliminando">Eliminando...</span>
              <span v-else>Eliminar</span>
            </button>
          </div>
        </section>
      </div>
    </Teleport>
  </main>
</template>

<style scoped>
.admin-gatos-page {
  min-height: calc(100vh - 80px);

  padding: 40px;

  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.top-section {
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 24px;

  padding: 28px;
  margin-bottom: 24px;

  background-color: white;

  border-radius: 24px;

  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.section-label {
  margin-bottom: 8px;

  color: #df9800;

  font-family: "coolvetica";
  font-size: 18px;
}

.top-section h1 {
  margin: 0;

  color: #423430;

  font-family: "coolvetica";
  font-size: 42px;
}

.section-description {
  margin-top: 10px;

  color: #654236;

  font-size: 17px;
}

.back-btn,
.add-btn {
  display: flex;
  align-items: center;
  gap: 10px;

  padding: 14px 20px;

  color: white;

  background-color: #df9800;

  border-radius: 999px;

  text-decoration: none;

  font-family: "coolvetica";
  font-size: 18px;

  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
}

.back-btn {
  margin-left: auto;

  color: #654236;

  background-color: #faf8b3;
}

.back-btn:hover {
  background-color: #f1dc7a;

  transform: translateY(-2px);
}

.add-btn:hover {
  background-color: #c78300;

  transform: translateY(-2px);
}

.filters-section {
  margin-bottom: 24px;
}

.search-input {
  width: 100%;

  padding: 14px 18px;

  border: 2px solid #f1dc7a;
  border-radius: 18px;

  background-color: white;

  font-size: 16px;

  outline: none;
}

.search-input:focus {
  border-color: #df9800;
}

.table-section {
  overflow-x: auto;

  background-color: white;

  border-radius: 24px;

  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.cats-table {
  width: 100%;

  border-collapse: collapse;
}

.cats-table thead {
  background-color: #fff5c4;
}

.cats-table th {
  padding: 18px;

  color: #423430;

  font-family: "coolvetica";
  font-size: 18px;

  text-align: left;
}

.cats-table td {
  padding: 18px;

  border-top: 1px solid #f3e7b0;

  color: #654236;
}

.cat-image {
  width: 72px;
  height: 72px;

  border-radius: 18px;

  object-fit: cover;
}

.actions-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.table-btn {
  width: 42px;
  height: 42px;

  display: grid;
  place-items: center;

  border: none;
  border-radius: 12px;

  color: white;

  text-decoration: none;

  cursor: pointer;
}

.edit-btn {
  background-color: #df9800;
}

.delete-btn {
  background-color: #b85c4c;
}

.feedback-box {
  padding: 24px;

  background-color: white;

  border-radius: 24px;

  color: #654236;

  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.feedback-box.error {
  color: white;

  background-color: #b85c4c;
}

.delete-modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 3000;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 20px;

  background: rgba(66, 52, 48, 0.45);
  backdrop-filter: blur(4px);
}

.delete-modal {
  width: min(100%, 430px);

  padding: 32px;

  background: #ffffff;

  border-radius: 24px;

  text-align: center;

  box-shadow: 0 18px 45px rgba(66, 52, 48, 0.25);
}

.delete-modal-icon {
  width: 72px;
  height: 72px;

  display: grid;
  place-items: center;

  margin: 0 auto 18px;

  color: #b85c4c;

  background-color: #fff3c4;

  border-radius: 22px;

  font-size: 32px;
}

.delete-modal h2 {
  margin: 0 0 12px;

  color: #423430;

  font-family: "coolvetica", sans-serif;
  font-size: 34px;
}

.delete-modal p {
  margin: 0;

  color: #654236;

  font-size: 17px;
  line-height: 1.5;
}

.delete-modal strong {
  color: #423430;
}

.delete-error {
  margin-top: 16px !important;

  color: #b85c4c !important;

  font-family: "coolvetica", sans-serif;
}

.delete-modal-actions {
  display: flex;
  gap: 12px;

  margin-top: 28px;
}

.modal-btn {
  flex: 1;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  min-height: 48px;

  padding: 12px 18px;

  border: none;
  border-radius: 999px;

  font-family: "coolvetica", sans-serif;
  font-size: 17px;

  cursor: pointer;

  transition:
    background-color 0.2s ease,
    transform 0.2s ease,
    opacity 0.2s ease;
}

.modal-btn:hover:not(:disabled) {
  transform: translateY(-2px);
}

.modal-btn:disabled {
  cursor: not-allowed;
  opacity: 0.7;
}

.modal-btn.secondary {
  color: #654236;

  background-color: #faf8b3;
}

.modal-btn.secondary:hover:not(:disabled) {
  background-color: #f1dc7a;
}

.modal-btn.danger {
  color: white;

  background-color: #b85c4c;
}

.modal-btn.danger:hover:not(:disabled) {
  background-color: #984638;
}

@media (max-width: 900px) {
  .admin-gatos-page {
    padding: 24px 16px;
  }

  .top-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .back-btn {
    margin-left: 0;
  }

  .delete-modal-actions {
    flex-direction: column-reverse;
  }
}
</style>

