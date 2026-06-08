<script setup>
import { onMounted, ref } from "vue";
import { RouterLink } from "vue-router";

import { useAuthStore } from "../../stores/auth.js";
import { API_URL, buildUserImage } from "../../config/api.js";
import { obtenerGatosGuardados } from "../../services/gatosServices.js";

const auth = useAuthStore();
const cargandoResumen = ref(true);
const resumen = ref({
  guardados: 0,
  mensajes: 0,
  solicitudes: 0,
});

const fotoPerfil = () => {
  if (!auth.usuario?.foto) {
    return buildUserImage("user_icon.jpg");
  }

  return buildUserImage(auth.usuario.foto);
};

onMounted(async () => {
  try {
    if (!auth.usuario?.id) {
      await auth.comprobarSesion();
    }

    resumen.value.guardados = obtenerGatosGuardados(auth.usuario?.id).length;

    const respuesta = await fetch(`${API_URL}/api/usuarios/resumenUsuario.php`, {
      credentials: "include",
    });
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.success) {
      throw new Error(datos.error || "No se pudo cargar el resumen");
    }

    resumen.value.mensajes = datos.resumen.mensajes_no_leidos;
    resumen.value.solicitudes = datos.resumen.solicitudes;
  } catch (error) {
    console.error("No se pudo cargar el resumen del usuario", error);
  } finally {
    cargandoResumen.value = false;
  }
});
</script>

<template>
  <main class="perfil-page">
    <!-- HERO -->

    <section class="perfil-hero">
      <div class="perfil-info">
        <img :src="fotoPerfil()" alt="Foto usuario" class="perfil-avatar" />

        <div>
          <p class="perfil-label">Área personal</p>

          <h1>
            Bienvenido,
            {{ auth.usuario?.nombre || "Usuario" }}
          </h1>

          <p class="perfil-description">Gestiona tus favoritos, mensajes y solicitudes desde tu perfil.</p>
        </div>
      </div>
    </section>

    <!-- RESUMEN -->

    <section class="stats-grid">
      <article class="stat-card">
        <i class="bi bi-heart-fill"></i>

        <div>
          <strong>{{ cargandoResumen ? "..." : resumen.guardados }}</strong>
          <p>Guardados</p>
        </div>
      </article>

      <article class="stat-card">
        <i class="bi bi-chat-dots-fill"></i>

        <div>
          <strong>{{ cargandoResumen ? "..." : resumen.mensajes }}</strong>
          <p>Mensajes sin leer</p>
        </div>
      </article>

      <article class="stat-card">
        <i class="bi bi-file-earmark-text-fill"></i>

        <div>
          <strong>{{ cargandoResumen ? "..." : resumen.solicitudes }}</strong>
          <p>Solicitudes</p>
        </div>
      </article>
    </section>

    <!-- ACCIONES -->

    <section class="acciones-grid">
      <!-- FAVORITOS -->

      <article class="accion-card">
        <div class="card-top">
          <div class="card-icon">
            <i class="bi bi-heart-fill"></i>
          </div>

          <span class="card-badge"> Solicitudes </span>
        </div>

        <h2>Solicitudes de adopción</h2>

        <p>Consulta las solicitudes de adopción que has realizado y sigue sus novedades.</p>

        <RouterLink to="/solicitudes" class="card-btn"> Ver solicitudes </RouterLink>
      </article>

      <!-- MENSAJES -->

      <article class="accion-card">
        <div class="card-top">
          <div class="card-icon">
            <i class="bi bi-chat-dots-fill"></i>
          </div>

          <span class="card-badge"> Mensajes </span>
        </div>

        <h2>Conversaciones</h2>

        <p>Gestiona mensajes relacionados con adopciones y contacto.</p>

        <RouterLink to="/mensajes" class="card-btn"> Ir a mensajes </RouterLink>
      </article>

      <!-- PERFIL -->

      <article class="accion-card">
        <div class="card-top">
          <div class="card-icon">
            <i class="bi bi-person-fill"></i>
          </div>

          <span class="card-badge"> Cuenta </span>
        </div>

        <h2>Configuración</h2>

        <p>Edita tus datos personales, contraseña y foto de perfil.</p>

        <button class="card-btn">Editar perfil</button>
      </article>
    </section>
  </main>
</template>

<style scoped>
.perfil-page {
  min-height: calc(100vh - 80px);

  padding: 40px;

  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

/* HERO */

.perfil-hero {
  padding: 32px;

  margin-bottom: 28px;

  background-color: white;

  border-radius: 24px;

  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.perfil-info {
  display: flex;
  align-items: center;
  gap: 24px;
}

.perfil-avatar {
  width: 120px;
  height: 120px;

  border-radius: 50%;

  object-fit: cover;

  border: 4px solid #df9800;
}

.perfil-label {
  margin-bottom: 8px;

  color: #df9800;

  font-family: "coolvetica";
  font-size: 18px;
}

.perfil-hero h1 {
  margin: 0;

  color: #423430;

  font-family: "coolvetica";
  font-size: 42px;
}

.perfil-description {
  margin-top: 10px;

  color: #654236;

  font-size: 17px;
}

/* STATS */

.stats-grid {
  display: grid;

  grid-template-columns: repeat(3, 1fr);

  gap: 18px;

  margin-bottom: 32px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;

  padding: 20px;

  background-color: white;

  border-radius: 22px;

  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.stat-card i {
  color: #df9800;

  font-size: 30px;
}

.stat-card strong {
  display: block;

  color: #423430;

  font-family: "coolvetica";
  font-size: 28px;
}

.stat-card p {
  margin: 2px 0 0;

  color: #654236;
}

/* ACCIONES */

.acciones-grid {
  display: grid;

  grid-template-columns: repeat(3, 1fr);

  gap: 24px;
}

.accion-card {
  padding: 28px;

  background-color: white;

  border-radius: 24px;

  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);

  transition:
    transform 0.2s ease,
    border-color 0.2s ease;

  border: 2px solid transparent;
}

.accion-card:hover {
  transform: translateY(-6px);

  border-color: #df9800;
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;

  margin-bottom: 20px;
}

.card-icon {
  width: 58px;
  height: 58px;

  display: grid;
  place-items: center;

  border-radius: 18px;

  background-color: #faf8b3;

  color: #654236;

  font-size: 28px;
}

.card-badge {
  padding: 6px 14px;

  border-radius: 999px;

  background-color: #fff3c4;

  color: #df9800;

  font-family: "coolvetica";
  font-size: 14px;
}

.accion-card h2 {
  margin-bottom: 12px;

  color: #423430;

  font-family: "coolvetica";
  font-size: 28px;
}

.accion-card p {
  min-height: 72px;

  color: #654236;

  font-size: 16px;
}

.card-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  width: 100%;

  padding: 12px 18px;

  border: none;
  border-radius: 999px;

  background-color: #df9800;

  color: white;

  text-decoration: none;

  font-family: "coolvetica";
  font-size: 17px;

  transition:
    background-color 0.2s ease,
    transform 0.2s ease;

  cursor: pointer;
}

.card-btn:hover {
  background-color: #c78300;

  transform: translateY(-2px);
}

/* RESPONSIVE */

@media (max-width: 1100px) {
  .acciones-grid {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .perfil-page {
    padding: 24px 16px;
  }

  .perfil-info {
    flex-direction: column;
    align-items: flex-start;
  }

  .perfil-hero h1 {
    font-size: 34px;
  }
}
</style>
