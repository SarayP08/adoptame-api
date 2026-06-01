<script setup>
import { ref, computed, onMounted } from 'vue';

import {
  API_URL,
  buildCatImage
} from '../config/api';

const gatos = ref([]);
const cargando = ref(true);
const error = ref('');

const busqueda = ref('');

const cargarGatos = async () => {

  try {

    cargando.value = true;

    const res = await fetch(
        `${API_URL}/api/despliegueGatos.php`
    );

    const data = await res.json();

    gatos.value = data;

  } catch (err) {

    console.error(err);

    error.value = 'No se pudieron cargar los gatos';

  } finally {

    cargando.value = false;
  }
};

onMounted(() => {
  cargarGatos();
});

const gatosFiltrados = computed(() => {

  return gatos.value.filter(gato => {

    return gato.nombre
        .toLowerCase()
        .includes(busqueda.value.toLowerCase());
  });
});

const eliminarGato = async (id) => {

  const confirmar = confirm(
      '¿Seguro que quieres eliminar este gato?'
  );

  if (!confirmar) {
    return;
  }

  try {

    const res = await fetch(
        `${API_URL}/api/eliminarGato.php?id=${id}`,
        {
          method: 'DELETE'
        }
    );

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.error);
    }

    gatos.value = gatos.value.filter(
        gato => gato.id !== id
    );

  } catch (err) {

    console.error(err);

    alert('No se pudo eliminar el gato');
  }
};
</script>

<template>

  <main class="admin-gatos-page">

    <section class="top-section">

      <div>

        <p class="section-label">
          Panel administración
        </p>

        <h1>
          Gestión de gatos
        </h1>

        <p class="section-description">
          Consulta, edita y elimina gatos publicados.
        </p>

      </div>

      <RouterLink
          to="/añadirGato"
          class="add-btn"
      >
        <i class="bi bi-plus-circle"></i>
        Añadir gato
      </RouterLink>

    </section>

    <section class="filters-section">

      <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar gato por nombre..."
          class="search-input"
      >

    </section>

    <section v-if="cargando" class="feedback-box">
      Cargando gatos...
    </section>

    <section v-else-if="error" class="feedback-box error">
      {{ error }}
    </section>

    <section
        v-else
        class="table-section"
    >

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

        <tr
            v-for="gato in gatosFiltrados"
            :key="gato.id"
        >

          <td>

            <img
                :src="buildCatImage(gato.imagen)"
                :alt="gato.nombre"
                class="cat-image"
            >

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

              <RouterLink
                  :to="`/editarGato/${gato.id}`"
                  class="table-btn edit-btn"
              >
                <i class="bi bi-pencil-square"></i>
              </RouterLink>

              <button
                  class="table-btn delete-btn"
                  @click="eliminarGato(gato.id)"
              >
                <i class="bi bi-trash3"></i>
              </button>

            </div>

          </td>

        </tr>

        </tbody>

      </table>

    </section>

  </main>

</template>

<style scoped>
.admin-gatos-page {
  min-height: calc(100vh - 80px);

  padding: 40px;

  background:
      linear-gradient(135deg, #fff8d6, #f8e8d8);
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

  box-shadow:
      0 12px 30px rgba(66, 52, 48, 0.12);
}

.section-label {
  margin-bottom: 8px;

  color: #df9800;

  font-family: 'coolvetica';
  font-size: 18px;
}

.top-section h1 {
  margin: 0;

  color: #423430;

  font-family: 'coolvetica';
  font-size: 42px;
}

.section-description {
  margin-top: 10px;

  color: #654236;

  font-size: 17px;
}

.add-btn {
  display: flex;
  align-items: center;
  gap: 10px;

  padding: 14px 20px;

  color: white;

  background-color: #df9800;

  border-radius: 999px;

  text-decoration: none;

  font-family: 'coolvetica';
  font-size: 18px;

  transition:
      background-color 0.2s ease,
      transform 0.2s ease;
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

  box-shadow:
      0 12px 30px rgba(66, 52, 48, 0.12);
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

  font-family: 'coolvetica';
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

  box-shadow:
      0 12px 30px rgba(66, 52, 48, 0.12);
}

.feedback-box.error {
  color: white;

  background-color: #b85c4c;
}

@media (max-width: 900px) {

  .admin-gatos-page {
    padding: 24px 16px;
  }

  .top-section {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>