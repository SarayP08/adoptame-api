<script setup>
import { ref } from 'vue'

const email = ref('')

const handleSubmit = () => {
  if (!email.value) {
    alert('Introduce tu correo electrónico')
    return
  }

  alert(`Se ha enviado el enlace de recuperación a ${email.value}`)
  email.value = ''
}
</script>

<template>
  <div class="contenedor">
    <main class="form-reset w-100 m-auto">
      <form @submit.prevent="handleSubmit">
        <div class="text-center">
          <i class="bi bi-lock-fill mb-4" style="font-size: 4rem; color: #f09014"></i>
        </div>

        <h1 class="h3 mb-3 fw-normal text-center">Recupera tu contraseña</h1>
        <p class="text-center text-secondary mb-4">
          Introduce tu correo y te enviaremos un enlace de recuperación.
        </p>

        <div class="form-floating">
          <input v-model="email" type="email" class="form-control" id="email" placeholder="Correo electrónico" required />
          <label for="email">Correo electrónico</label>
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit">
          Enviar enlace
        </button>

        <p class="mt-4 mb-0 text-center text-secondary">
          Si no recibes el correo, revisa la carpeta de spam.
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

.form-reset {
  max-width: 520px;
  padding: 2.5rem;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  position: relative;
  z-index: 2;
}

.form-floating {
  margin-bottom: 1.2rem;
}

.form-control {
  border-radius: 8px;
  border: 1px solid #ddd;
}

.form-control:focus {
  border-color: #f09014;
  box-shadow: 0 0 0 0.2rem rgba(240, 144, 20, 0.25);
}

button {
  background-color: #f09014;
  border: none;
}

button:hover {
  background-color: #d97f10;
}

a {
  color: #ad6119;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>