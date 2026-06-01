<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { API_URL } from '../../../config/api.js';

const router = useRouter();

const formulario = ref({
  nombre: '',
  edad: '',
  sexo: '',
  castrado: '',
  estado: '',
  descripcion: '',
  vacunas: [''],
  imagen: null
});

const vistaPrevia = ref('');
const cargando = ref(false);
const mensaje = ref('');
const error = ref('');

const añadirVacuna = () => {
  formulario.value.vacunas.push('');
};

const eliminarVacuna = (index) => {
  formulario.value.vacunas.splice(index, 1);

  if (formulario.value.vacunas.length === 0) {
    formulario.value.vacunas.push('');
  }
};

const seleccionarImagen = (event) => {
  const archivo = event.target.files[0];

  formulario.value.imagen = archivo || null;

  if (archivo) {
    vistaPrevia.value = URL.createObjectURL(archivo);
  } else {
    vistaPrevia.value = '';
  }
};

const añadirGato = async () => {
  mensaje.value = '';
  error.value = '';

  if (
    !formulario.value.nombre ||
    !formulario.value.edad ||
    !formulario.value.sexo ||
    !formulario.value.castrado ||
    !formulario.value.estado ||
    !formulario.value.descripcion ||
    !formulario.value.imagen
  ) {
    error.value = 'Por favor, rellena todos los campos obligatorios.';
    return;
  }

  if (formulario.value.descripcion.length > 500) {
    error.value = 'La descripción no puede superar los 500 caracteres.';
    return;
  }

  try {
    cargando.value = true;

    const datos = new FormData();
    datos.append('nombre', formulario.value.nombre);
    datos.append('edad', formulario.value.edad);
    datos.append('sexo', formulario.value.sexo);
    datos.append('castrado', formulario.value.castrado);
    datos.append('estado', formulario.value.estado);
    datos.append('descripcion', formulario.value.descripcion);
    datos.append('imagen', formulario.value.imagen);

    const vacunasLimpias = formulario.value.vacunas.filter(
        vacuna => vacuna.trim() !== ''
    );

    datos.append(
        'vacunas',
        JSON.stringify(vacunasLimpias)
    );

    const respuesta = await fetch(`${API_URL}/api/añadirGato.php`, {
      method: 'POST',
      body: datos
    });

    const resultado = await respuesta.json();

    if (!respuesta.ok || resultado.error) {
      throw new Error(resultado.message || 'No se pudo añadir el gato.');
    }

    mensaje.value = 'Gato añadido correctamente 🐱';

    formulario.value = {
      nombre: '',
      edad: '',
      sexo: '',
      castrado: '',
      descripcion: '',
      estado: '',
      imagen: null,
      vacunas: ['']
    };

    vistaPrevia.value = '';

    setTimeout(() => {
      router.push('/gatos');
    }, 1200);
  } catch (err) {
    error.value = err.message || 'Error al conectar con el servidor.';
  } finally {
    cargando.value = false;
  }
};
</script>

<template>
  <main class="add-cat-page">
    <section class="add-cat-card">
      <div class="add-cat-header">
        <p class="add-cat-label">Panel de administración</p>
        <h1>Añadir gato</h1>
        <p>
          Rellena los datos del gatete para publicarlo en la sección de gatos.
        </p>
      </div>

      <form class="add-cat-form" @submit.prevent="añadirGato">
        <div class="form-grid">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input
              id="nombre"
              v-model="formulario.nombre"
              type="text"
              placeholder="Ej: Michi"
            />
          </div>

          <div class="form-group">
            <label for="edad">Edad</label>
            <input
              id="edad"
              v-model="formulario.edad"
              type="text"
              placeholder="Ej: 2 años"
            />
          </div>

          <div class="form-group">
            <label for="sexo">Sexo</label>
            <select id="sexo" v-model="formulario.sexo">
              <option value="" disabled>Selecciona una opción</option>
              <option value="hembra">Hembra</option>
              <option value="macho">Macho</option>
            </select>
          </div>

          <div class="form-group">
            <label for="castrado">Castrado</label>
            <select id="castrado" v-model="formulario.castrado">
              <option value="" disabled>Selecciona una opción</option>
              <option value="si">Sí</option>
              <option value="no">No</option>
            </select>
          </div>

          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" v-model="formulario.estado">
              <option value="" disabled>Selecciona una opción</option>
              <option value="adopcion">Adopción</option>
              <option value="acogida">Acogida</option>
              <option value="preadopcion">Preadopción</option>
            </select>
          </div>

          <div class="form-group full-width">
            <label for="descripcion">Descripción</label>
            <textarea
              id="descripcion"
              v-model="formulario.descripcion"
              placeholder="Describe al gato (máx. 500 caracteres)"
            />
          </div>

          <div class="form-group full-width">
            <div class="label-row">
              <label>Vacunas</label>

              <span v-if="formulario.vacunas.length > 1">
                Añade o elimina vacunas
              </span>

              <button class="add-vaccine-btn" type="button" @click="añadirVacuna">
                +
              </button>
            </div>

            <div v-for="(vacuna, index) in formulario.vacunas" :key="index" class="vaccine-row">
              <input
                type="text"
                v-model="formulario.vacunas[index]"
                placeholder="Nombre de la vacuna"
              />

              <button
                v-if="formulario.vacunas.length > 1"
                class="remove-vaccine-btn"
                type="button"
                @click="eliminarVacuna(index)"
              >
                -
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="imagen">Imagen</label>
            <input
              id="imagen"
              type="file"
              accept="image/*"
              @change="seleccionarImagen"
            />
          </div>
        </div>

        <div v-if="vistaPrevia" class="preview-box">
          <p>Vista previa:</p>
          <img :src="vistaPrevia" alt="Vista previa del gato" />
        </div>

        <p v-if="error" class="feedback error">
          {{ error }}
        </p>

        <p v-if="mensaje" class="feedback success">
          {{ mensaje }}
        </p>

        <div class="form-actions">
          <RouterLink to="/panel-admin" class="btn-secondary">
            Volver
          </RouterLink>

          <button class="btn-primary" type="submit" :disabled="cargando">
            {{ cargando ? 'Añadiendo...' : 'Añadir gato' }}
          </button>
        </div>
      </form>
    </section>
  </main>
</template>

<style scoped>
.add-cat-page {
  min-height: calc(100vh - 80px);
  padding: 40px 16px;
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.add-cat-card {
  max-width: 900px;
  margin: 0 auto;
  padding: 32px;
  background-color: #ffffff;
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.add-cat-header {
  margin-bottom: 28px;
}

.add-cat-label {
  margin-bottom: 8px;
  color: #df9800;
  font-family: 'coolvetica', sans-serif;
  font-size: 18px;
}

.add-cat-header h1 {
  margin: 0;
  color: #423430;
  font-family: 'coolvetica', sans-serif;
  font-size: 42px;
}

.add-cat-header p {
  margin-top: 10px;
  margin-bottom: 0;
  color: #654236;
  font-size: 18px;
}

.add-cat-form {
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
  font-family: 'coolvetica', sans-serif;
  font-size: 18px;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 12px 14px;
  color: #423430;
  background-color: #fffdf2;
  border: 2px solid #f1dc7a;
  border-radius: 14px;
  font-size: 16px;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
  border-color: #df9800;
  box-shadow: 0 0 0 4px rgba(223, 152, 0, 0.15);
}

.form-group input[type='file'] {
  padding: 10px;
  cursor: pointer;
}

.form-group textarea {
  width: 100%;
  resize: vertical;
  min-height: 130px;
  padding: 12px 14px;
  color: #423430;
  background-color: #fffdf2;
  border: 2px solid #f1dc7a;
  border-radius: 14px;
  font-size: 16px;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group textarea:focus {
  border-color: #df9800;
  box-shadow: 0 0 0 4px rgba(223, 152, 0, 0.15);
}

.full-width {
  grid-column: 1 / -1;
}

.label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.label-row span {
  color: #df9800;
  font-family: 'coolvetica', sans-serif;
  font-size: 15px;
}

.add-vaccine-btn,
.remove-vaccine-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.add-vaccine-btn {
  width: 36px;
  height: 36px;
  color: #ffffff;
  background-color: #df9800;
  border-radius: 50%;
  font-size: 18px;
}

.add-vaccine-btn:hover {
  background-color: #c78300;
  transform: translateY(-2px);
}

.vaccine-row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 10px;
  margin-bottom: 10px;
}

.remove-vaccine-btn {
  width: 44px;
  height: 44px;
  color: #ffffff;
  background-color: #b85c4c;
  border-radius: 14px;
  font-size: 17px;
}

.remove-vaccine-btn:hover {
  background-color: #984638;
  transform: translateY(-2px);
}

.preview-box {
  padding: 18px;
  background-color: #fff8d6;
  border-radius: 18px;
}

.preview-box p {
  margin-bottom: 12px;
  color: #654236;
  font-family: 'coolvetica', sans-serif;
  font-size: 18px;
}

.preview-box img {
  width: 220px;
  height: 220px;
  object-fit: cover;
  border-radius: 18px;
  box-shadow: 0 8px 20px rgba(66, 52, 48, 0.16);
}

.feedback {
  margin: 0;
  padding: 12px 16px;
  border-radius: 14px;
  font-family: 'coolvetica', sans-serif;
  font-size: 17px;
}

.feedback.error {
  color: #ffffff;
  background-color: #b85c4c;
}

.feedback.success {
  color: #2f5d3a;
  background-color: #dff3d8;
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
  font-family: 'coolvetica', sans-serif;
  font-size: 17px;
  text-decoration: none;
  cursor: pointer;
  transition: background-color 0.2s ease, opacity 0.2s ease, transform 0.2s ease;
}

.btn-primary {
  color: #ffffff;
  background-color: #df9800;
}

.btn-primary:hover {
  background-color: #c78300;
  transform: translateY(-2px);
}

.btn-primary:disabled {
  cursor: not-allowed;
  opacity: 0.7;
  transform: none;
}

.btn-secondary {
  color: #654236;
  background-color: #faf8b3;
}

.btn-secondary:hover {
  background-color: #f1dc7a;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .add-cat-page {
    padding: 24px 16px;
  }

  .add-cat-card {
    padding: 24px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .add-cat-header h1 {
    font-size: 34px;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
    text-align: center;
  }

  .preview-box img {
    width: 100%;
    max-width: 260px;
  }

  .vaccine-row {
    grid-template-columns: 1fr;
  }

  .remove-vaccine-btn {
    width: 100%;
  }
}
</style>