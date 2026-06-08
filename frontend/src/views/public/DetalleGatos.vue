<script setup>
import { onMounted, ref } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";
import { API_URL, buildCatImage } from "../../config/api.js";
import { useAuthStore } from "../../stores/auth.js";

const router = useRouter();
const auth = useAuthStore();
const route = useRoute();

const gato = ref(null);
const cargando = ref(true);
const error = ref("");

const mostrarCastrado = (valor) => {
  const estaCastrado =
    valor === true ||
    valor === 1 ||
    valor === "1" ||
    valor === "si" ||
    valor === "sí";

  return estaCastrado ? "Sí" : "No";
};

const solicitar = (tipo) => {
  if (!auth.logueado) {
    router.push(`/iniciarSesion?redirect=/adoptar/${gato.value.id}?tipo=${tipo}`);
    return;
  }

  router.push(`/adoptar/${gato.value.id}?tipo=${tipo}`);
};

onMounted(async () => {
  try {
    const id = route.params.id;
    const res = await fetch(`${API_URL}/api/gatos/detalleGato.php?id=${id}`);
    const data = await res.json();

    if (data.error) {
      error.value = data.message || "No se pudo cargar el gato";
      gato.value = null;
    } else {
      gato.value = data;
    }
  } catch (err) {
    error.value = "Error al conectar con el servidor";
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

      <RouterLink to="/gatos" class="btn btn-primary"> Volver a gatos </RouterLink>
    </div>

    <div v-else-if="gato" class="row align-items-start">
      <div class="col-lg-6 mb-4">
        <img
          v-if="gato.imagen"
          :src="buildCatImage(gato.imagen)"
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
              <li><strong>Nombre:</strong> {{ gato.nombre || "No indicado" }}</li>
              <li><strong>Edad:</strong> {{ gato.edad || "No indicada" }}</li>
              <li><strong>Sexo:</strong> {{ gato.sexo || "No indicado" }}</li>
              <li><strong>Castrado:</strong> {{ mostrarCastrado(gato.castrado) }}</li>
              <li>
                <strong>Vacunas:</strong>
                {{ gato.vacunas?.length ? gato.vacunas.join(", ") : "No indicado" }}
              </li>
            </ul>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-primary" @click="solicitar('adopcion')">
                Adoptar
              </button>

              <button class="btn btn-acoger" @click="solicitar('acogida')">
                Acoger
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
  color: #ffffff;
  font-family: "lemonMilk";
  font-size: clamp(2.5rem, 5vw, 4.25rem);
  letter-spacing: 3px;
  line-height: 1.05;
  text-transform: uppercase;
  text-shadow:
    0 4px 10px rgba(0, 0, 0, 0.35),
    0 8px 25px rgba(0, 0, 0, 0.2);
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
  font-family: "coolvetica";
  font-size: 20px;
}

.detalle-info {
  color: #654236;
  font-family: "coolvetica";
  font-size: 18px;
}

.detalle-info li {
  margin-bottom: 0.5rem;
}

.btn-primary {
  background-color: #f09014;
  border: none;
  font-family: "coolvetica";
}

.btn-primary:hover {
  background-color: #503e3e;
}

.btn-outline-primary {
  border-color: #f09014;
  color: #f09014;
  font-family: "coolvetica";
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

.btn-acoger {
  background-color: #8bbf6a;
  border: none;
  color: white;
  font-family: "lemonMilk";
}

.btn-acoger:hover {
  background-color: #6ea34f;
}
</style>
