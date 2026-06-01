# AdoptarUsuario.vue

```vue
<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import {
  API_URL,
  buildCatImage
} from '../../config/api.js';

import { useAuthStore } from '../../stores/auth.js';

const auth = useAuthStore();

const route = useRoute();
const router = useRouter();

const gatoId = route.params.id;

const cargando = ref(false);
const mensaje = ref('');
const error = ref('');

const formulario = ref({
  motivo: '',
  vivienda: '',
  experiencia: '',
  tiempo: '',
  otrosAnimales: '',
  comentario: ''
});

const enviarSolicitud = async () => {

  mensaje.value = '';
  error.value = '';

  if (
      !formulario.value.motivo ||
      !formulario.value.vivienda ||
      !formulario.value.tiempo
  ) {

    error.value = 'Completa los campos obligatorios';

    return;
  }

  try {

    cargando.value = true;

    const datos = new FormData();

    datos.append('usuario_id', auth.usuario.id);
    datos.append('gato_id', gatoId);

    datos.append('motivo', formulario.value.motivo);
    datos.append('vivienda', formulario.value.vivienda);
    datos.append('experiencia', formulario.value.experiencia);
    datos.append('tiempo', formulario.value.tiempo);
    datos.append('otrosAnimales', formulario.value.otrosAnimales);
    datos.append('comentario', formulario.value.comentario);

    const res = await fetch(
        `${API_URL}/api/crearSolicitud.php`,
        {
          method: 'POST',
          body: datos
        }
    );

    const data = await res.json();

    if (!data.success) {

      error.value = data.error || 'No se pudo enviar la solicitud';

      return;
    }

    mensaje.value = 'Solicitud enviada correctamente 🐱';

    setTimeout(() => {
      router.push('/perfil');
    }, 1500);

  } catch (err) {

    console.error(err);

    error.value = 'Error al conectar con el servidor';

  } finally {

    cargando.value = false;
  }
};
</script>

<template>

  <main class="adopt-page">

    <section class="adopt-card">

      <div class="adopt-header">

        <p class="adopt-label">
          Solicitud de adopción
        </p>

        <h1>
          Adoptar gatete
        </h1>

        <p>
          Completa el formulario para enviar tu solicitud.
        </p>

      </div>

      <form
          class="adopt-form"
          @submit.prevent="enviarSolicitud"
      >

        <div class="form-group">

          <label>
            ¿Por qué quieres adoptar?
          </label>

          <textarea
              v-model="formulario.motivo"
              placeholder="Cuéntanos tus motivos"
          />

        </div>

        <div class="form-grid">

          <div class="form-group">

            <label>
              Tipo de vivienda
            </label>

            <select v-model="formulario.vivienda">
              <option value="" disabled>
                Selecciona
              </option>

              <option value="piso">
                Piso
              </option>

              <option value="casa">
                Casa
              </option>

              <option value="casa_jardin">
                Casa con jardín
              </option>
            </select>

          </div>

          <div class="form-group">

            <label>
              Tiempo disponible diario
            </label>

            <select v-model="formulario.tiempo">
              <option value="" disabled>
                Selecciona
              </option>

              <option value="1-2h">
                1-2 horas
              </option>

              <option value="3-5h">
                3-5 horas
              </option>

              <option value="todo_dia">
                Gran parte del día
              </option>
            </select>

          </div>

        </div>

        <div class="form-group">

          <label>
            Experiencia previa con animales
          </label>

          <textarea
              v-model="formulario.experiencia"
              placeholder="Describe tu experiencia"
          />

        </div>

        <div class="form-group">

          <label>
            ¿Tienes otros animales?
          </label>

          <textarea
              v-model="formulario.otrosAnimales"
              placeholder="Opcional"
          />

        </div>

        <div class="form-group">

          <label>
            Comentarios adicionales
          </label>

          <textarea
              v-model="formulario.comentario"
              placeholder="Opcional"
          />

        </div>

        <p
            v-if="error"
            class="feedback error"
        >
          {{ error }}
        </p>

        <p
            v-if="mensaje"
            class="feedback success"
        >
          {{ mensaje }}
        </p>

        <div class="form-actions">

          <RouterLink
              to="/gatos"
              class="btn-secondary"
          >
            Volver
          </RouterLink>

          <button
              class="btn-primary"
              type="submit"
              :disabled="cargando"
          >
            {{ cargando
              ? 'Enviando...'
              : 'Enviar solicitud' }}
          </button>

        </div>

      </form>

    </section>

  </main>

</template>

<style scoped>
.adopt-page {
  min-height: calc(100vh - 80px);

  padding: 40px 16px;

  background:
      linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.adopt-card {
  max-width: 900px;

  margin: 0 auto;

  padding: 32px;

  background-color: white;

  border-radius: 24px;

  box-shadow:
      0 12px 30px rgba(66, 52, 48, 0.12);
}

.adopt-header {
  margin-bottom: 28px;
}

.adopt-label {
  margin-bottom: 8px;

  color: #df9800;

  font-family: 'coolvetica';
  font-size: 18px;
}

.adopt-header h1 {
  margin: 0;

  color: #423430;

  font-family: 'coolvetica';
  font-size: 42px;
}

.adopt-header p {
  margin-top: 10px;

  color: #654236;

  font-size: 18px;
}

.adopt-form {
  display: grid;

  gap: 24px;
}

.form-grid {
  display: grid;

  grid-template-columns: repeat(2, 1fr);

  gap: 20px;
}

.form-group {
  display: grid;

  gap: 8px;
}

.form-group label {
  color: #423430;

  font-family: 'coolvetica';
  font-size: 18px;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;

  padding: 12px 14px;

  border: 2px solid #f1dc7a;
  border-radius: 14px;

  background-color: #fffdf2;

  color: #423430;

  font-size: 16px;

  outline: none;
}

.form-group textarea {
  resize: vertical;

  min-height: 120px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #df9800;

  box-shadow:
      0 0 0 4px rgba(223, 152, 0, 0.15);
}

.feedback {
  padding: 12px 16px;

  border-radius: 14px;

  font-family: 'coolvetica';
  font-size: 17px;
}

.feedback.error {
  background-color: #b85c4c;
  color: white;
}

.feedback.success {
  background-color: #dff3d8;
  color: #2f5d3a;
}

.form-actions {
  display: flex;
  justify-content: flex-end;

  gap: 12px;
}

.btn-primary,
.btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  padding: 12px 22px;

  border: none;
  border-radius: 999px;

  text-decoration: none;

  font-family: 'coolvetica';
  font-size: 17px;

  cursor: pointer;
}

.btn-primary {
  background-color: #df9800;
  color: white;
}

.btn-primary:hover {
  background-color: #c78300;
}

.btn-secondary {
  background-color: #faf8b3;
  color: #654236;
}

.btn-secondary:hover {
  background-color: #f1dc7a;
}

@media (max-width: 768px) {

  .adopt-page {
    padding: 24px 16px;
  }

  .adopt-card {
    padding: 24px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }
}
</style>
```
