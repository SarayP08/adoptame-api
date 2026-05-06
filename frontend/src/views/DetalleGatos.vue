<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { API_URL, buildUrl } from '../config/api';

const route = useRoute();

const gato = ref(null);
const cargando = ref(true);
const error = ref('');

const mostrarCastrado = (valor) => {
  return valor === true || valor === 1 || valor === '1' || valor === 'si' || valor === 'sí'
    ? 'Sí'
    : 'No';
};

onMounted(async () => {
  try {
    const id = route.params.id;
    const res = await fetch(`${API_URL}/api/detalleGato.php?id=${id}`);
    const data = await res.json();

    if (data.error) {
      error.value = data.message || 'No se pudo cargar el gato';
      gato.value = null;
    } else {
      gato.value = data;
    }
  } catch (err) {
    error.value = 'Error al conectar con el servidor';
    console.error(err);
  } finally {
    cargando.value = false;
  }
});
</script>

<template>
  <div class="container mt-5 detalle-gato-page">
    <div v-if="cargando" class="text-center">
      <p>Cargando gatete...</p>
    </div>

    <div v-else-if="error" class="text-center">
      <h1>Ups...</h1>
      <p>{{ error }}</p>

      <RouterLink to="/gatos" class="btn btn-primary">
        Volver a gatos
      </RouterLink>
    </div>

    <div v-else-if="gato" class="row align-items-start">
      <div class="col-lg-6 mb-4">
        <img
          v-if="gato.imagen"
          :src="buildUrl(gato.imagen)"
          :alt="`Imagen de ${gato.nombre}`"
          class="img-fluid rounded shadow detalle-imagen"
        />
      </div>

      <div class="col-lg-6">
        <h1 class="titulo-detalle">{{ gato.nombre }}</h1>

        <div class="card shadow-sm detalle-card">
          <div class="card-body">
            <p v-if="gato.descripcion" class="descripcion">
              {{ gato.descripcion }}
            </p>

            <ul class="list-unstyled detalle-info">
              <li>
                <strong>Nombre:</strong> {{ gato.nombre || 'No indicado' }}
              </li>
              <li>
                <strong>Edad:</strong> {{ gato.edad || 'No indicada' }}
              </li>
              <li>
                <strong>Sexo:</strong> {{ gato.sexo || 'No indicado' }}
              </li>
              <li>
                <strong>Castrado:</strong> {{ mostrarCastrado(gato.castrado) }}
              </li>
            </ul>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-primary">
                Adoptar
              </button>

              <RouterLink to="/gatos" class="btn btn-outline-primary">
                Volver
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.detalle-gato-page {
  min-height: 100vh;
}

.titulo-detalle {
  color: #654236;
  font-family: 'lemonMilk';
  margin-bottom: 1.5rem;
}

.detalle-imagen {
  width: 100%;
  max-height: 520px;
  object-fit: cover;
}

.detalle-card {
  border: none;
  border-radius: 12px;
}

.descripcion {
  color: #654236;
  font-family: 'coolvetica';
  font-size: 20px;
}

.detalle-info {
  color: #654236;
  font-family: 'coolvetica';
  font-size: 18px;
}

.detalle-info li {
  margin-bottom: 0.5rem;
}

.btn-primary {
  background-color: #f09014;
  border: none;
  font-family: 'lemonMilk';
}

.btn-primary:hover {
  background-color: #503e3e;
}

.btn-outline-primary {
  border-color: #f09014;
  color: #f09014;
  font-family: 'lemonMilk';
}

.btn-outline-primary:hover {
  background-color: #503e3e;
  border-color: #503e3e;
  color: #ffffff;
}

.debug-campos {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 1rem;
  font-size: 14px;
}
</style>