<script setup>
import { ref } from 'vue'

const nombre = ref('')
const apellidos = ref('')
const edad = ref('')
const genero = ref('')
const email = ref('')
const movil = ref('')
const password = ref('')
const confirmPassword = ref('')

const handleSubmit = () => {
  if (password.value !== confirmPassword.value) {
    alert('Las contraseñas no coinciden')
    return
  }

  console.log({
    nombre: nombre.value,
    apellidos: apellidos.value,
    edad: edad.value,
    genero: genero.value,
    email: email.value,
    movil: movil.value,
    password: password.value
  })
}
</script>

<template>
  <div class="contenedor">
    <main class="form-signin w-100 m-auto">
      <form @submit.prevent="handleSubmit">

        <div class="text-center">
          <i class="mb-4 bi bi-person-plus" style="font-size: 4.5rem;"></i>
        </div>

        <h1 class="h3 mb-3 fw-normal text-center">Crear Cuenta</h1>

        <div class="row">
          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="nombre" type="text" class="form-control" placeholder="Nombre" required />
              <label>Nombre</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="apellidos" type="text" class="form-control" placeholder="Apellidos" required />
              <label>Apellidos</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="edad" type="number" class="form-control" placeholder="Edad" required />
              <label>Edad</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select v-model="genero" class="form-control" required>
                <option value="" disabled selected>Selecciona</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
                <option value="no_decir">Prefiero no decirlo</option>
              </select>
              <label>Género</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="email" type="email" class="form-control" placeholder="Email" required />
              <label>Correo electrónico</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="movil" type="tel" class="form-control" placeholder="Móvil" required />
              <label>Teléfono móvil</label>
            </div>
          </div>
        </div>

        <div class="form-floating">
          <input v-model="password" type="password" class="form-control" placeholder="Password" required />
          <label>Contraseña</label>
        </div>

        <div class="form-floating">
          <input v-model="confirmPassword" type="password" class="form-control" placeholder="Confirm Password" required />
          <label>Confirmar contraseña</label>
        </div>

        <div class="form-check text-start my-3">
          <input class="form-check-input" type="checkbox" required />
          <label class="form-check-label">
            <a href="/terminos" target="_blank">Acepto los términos y condiciones</a>
          </label>
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit">
          Registrarse
        </button>

        <br /><br />

        <p class="text-body-secondary text-center">
          ¿Ya tienes cuenta? <a href="/login">Inicia sesión</a>
        </p>

        <p class="mt-5 mb-3 text-body-secondary text-center">
          &copy; Pawtita - 2026
        </p>
      </form>
    </main>
  </div>
</template>

<style scoped>
h1 {
  font-family: 'lemonMilk';
  color: #654236;
}

.contenedor {
  background-image: url(../assets/img/img_inicioS/gato_fondo.png);
  background-size: cover;
  background-position: center;
  min-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.contenedor::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(56, 45, 45, 0.6);
}

.form-signin {
  max-width: 520px;
  padding: 2.5rem;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  position: relative;
  z-index: 2;
  margin-top: 2rem;
  margin-bottom: 2rem;
}

.form-floating {
  margin-bottom: 1.2rem;
}

button {
  background-color: #f09014;
  border: none;
}

button:hover {
  background-color: #d97f10;
}

.form-control {
  border-radius: 8px;
  border: 1px solid #ddd;
}

.form-control:focus {
  border-color: #f09014;
  box-shadow: 0 0 0 0.2rem rgba(240, 144, 20, 0.25);
}

i {
  color: #f09014;
}

a {
  color: #ad6119;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>