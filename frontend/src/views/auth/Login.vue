<script setup>
import { ref } from 'vue';
import { API_URL } from '../../config/api.js';
import { useAuthStore } from '../../stores/auth.js';
const router = useRouter();
import {
  useRouter,
  useRoute
} from 'vue-router';
const auth = useAuthStore();
const email = ref('');
const password = ref('');
const error = ref('');
const cargando = ref(false);

const route = useRoute();
const login = async () => {

  error.value = '';
  cargando.value = true;

  const resultado = await auth.login(
      email.value,
      password.value
  );

  if (resultado.ok) {

    const redirect = route.query.redirect;

    if (redirect) {

      router.push(redirect);

    } else {

      if (auth.usuario.rol === 'admin') {

        router.push('/panel-admin');

      } else {

        router.push('/usuario');
      }
    }

  } else {

    error.value = resultado.message;
  }

  cargando.value = false;
};
</script>

<template>
  <div class="contenedor">
  <main class="form-signin w-100 m-auto">
      <form @submit.prevent="login">
        <div class="text-center">
          <i class="mb-4 bi bi-person" style="font-size: 4.5rem; color: orange;"></i>
        </div>

        <h1 class="h3 mb-3 fw-normal text-center">Iniciar Sesión</h1>

        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <div class="form-floating">
          <input
            v-model="email"
            type="email"
            class="form-control"
            id="floatingInput"
            placeholder="admin@example.com"
            required
          />
          <label for="floatingInput">Dirección de correo electrónico</label>
        </div>

        <div class="form-floating mt-2">
          <input
            v-model="password"
            type="password"
            class="form-control"
            id="floatingPassword"
            placeholder="Contraseña"
            required
          />
          <label for="floatingPassword">Contraseña</label>
        </div>

        <div class="form-check text-start my-3">
          <input
            class="form-check-input"
            type="checkbox"
            value="remember-me"
            id="checkDefault"
          />
          <label class="form-check-label" for="checkDefault">
            Recordarme
          </label>
        </div>

        <p class="text-body-secondary text-center">
          <a href="/contra-olvidada">¿Olvidaste tu contraseña?</a>
        </p>

        <button
          class="btn btn-primary w-100 py-2"
          type="submit"
          :disabled="cargando"
        >
          {{ cargando ? 'Entrando...' : 'Iniciar Sesión' }}
        </button>

        <br><br>

        <p class="text-body-secondary text-center">
          ¿No tienes una cuenta? <a href="/registro">Regístrate aquí</a>
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
  background-image: url(../../assets/img/img_inicioS/gato_fondo.png);
  background-size: cover;
  background-position: center;
  min-height: 100vh;

  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.contenedor::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(56, 45, 45, 0.6); /* tono tierra */
}

.form-signin {
  max-width: 420px;
  padding: 2rem;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  position: relative;
  z-index: 2;
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