<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";

import { buildUserImage } from "../config/api";

const auth = useAuthStore();
const router = useRouter();

const dropdownAbierto = ref(false);

const nav = [
  { name: "Adoptar", path: "/gatos" },
  { name: "Colabora", path: "/colabora" },
  { name: "Noticias", path: "/noticias" },
  { name: "Iniciar Sesión", path: "/iniciarSesion" },
  { name: "Registrarse", path: "/registro" },
  { name: "Contacto", path: "/contacto" },
];

const navAdmin = [{ name: "Panel de gestión", path: "/panel-admin" }];

const navUsuario = [
  { name: "Adoptar", path: "/gatos" },
  { name: "Guardados", path: "/guardados" },
  { name: "Mensajes", path: "/mensajes" },
  { name: "Noticias", path: "/noticias" },
  { name: "Colabora", path: "/colabora" },
];

const salir = async () => {
  await auth.logout();

  router.push("/iniciarSesion");
};

const toggleDropdown = () => {
  dropdownAbierto.value = !dropdownAbierto.value;
};

const fotoPerfil = () => {
  if (!auth.usuario?.foto) {
    return buildUserImage("user_icon.jpg");
  }

  return buildUserImage(auth.usuario.foto);
};
</script>
<template>
  <header class="header">
    <div class="header-container">
      <router-link to="/" class="brand text-decoration-none">
        <img src="../assets/img/img_iconos/favicon.png" alt="Logo" class="logo" />

        <span> Pawtita </span>
      </router-link>

      <nav class="nav-area">
        <ul class="nav nav-pills">
          <li class="nav-item" v-for="pags in auth.usuario?.rol === 'admin' ? navAdmin : auth.logueado ? navUsuario : nav" :key="pags.name">
            <router-link class="nav-link" :to="pags.path">
              <i v-if="pags.name === 'Colabora'"></i>
              {{ pags.name }}
            </router-link>
          </li>
        </ul>

        <div v-if="auth.logueado" class="admin-actions">
          <button class="icon-btn">
            <i class="bi bi-bell"></i>
          </button>

          <button class="icon-btn">
            <i class="bi bi-chat-dots"></i>
          </button>

          <div class="profile-wrapper">
            <button class="profile-btn" @click="toggleDropdown">
              <img :src="fotoPerfil()" alt="Avatar" class="profile-avatar" />

              <span class="profile-name">
                {{ auth.usuario?.nombre || "Admin" }}
              </span>

              <i class="bi bi-chevron-down"></i>
            </button>

            <div v-if="dropdownAbierto" class="dropdown-menu-custom">
              <router-link :to="auth.usuario?.rol === 'admin' ? '/panel-admin' : '/usuario'" class="dropdown-item-custom">
                {{ auth.usuario?.rol === "admin" ? "Panel administración" : "Mi perfil" }}
              </router-link>

              <button class="dropdown-item-custom logout-item" @click="salir">Salir</button>
            </div>
          </div>
        </div>
      </nav>
    </div>
  </header>
</template>

<style scoped>
.header {
  width: 100%;
  padding: 0 32px;
  background-color: #faf8b3;
  border-bottom: 1px solid #e7df9d;
}

.header-container {
  height: 80px;

  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo {
  width: 110px;
}

.brand span {
  color: #654236;
  font-size: 46px;
  font-family: "coolvetica";
}

.nav-area {
  display: flex;
  align-items: center;
  gap: 28px;
}

.nav-link {
  color: #654236;
  font-family: "coolvetica";
  font-size: 20px;

  transition: color 0.2s ease;
}

.nav-link:hover {
  color: #df9800 !important;
}

.admin-actions {
  display: flex;
  align-items: center;
  gap: 18px;
}

.icon-btn {
  width: 42px;
  height: 42px;

  border: none;
  border-radius: 50%;

  background-color: #fff5c4;

  color: #654236;
  font-size: 18px;

  transition:
    background-color 0.2s ease,
    transform 0.2s ease;

  cursor: pointer;
}

.icon-btn:hover {
  background-color: #f4df8b;
  transform: translateY(-2px);
}

.profile-wrapper {
  position: relative;
}

.nav-link.router-link-exact-active {
  color: #df9800 !important;

  position: relative;
}

.nav-link.router-link-exact-active::after {
  content: "";

  position: absolute;

  left: 12px;
  right: 12px;
  bottom: -2px;

  height: 3px;

  border-radius: 999px;

  background-color: #df9800;
}

.profile-btn {
  display: flex;
  align-items: center;
  gap: 12px;

  border: none;
  border-radius: 999px;

  padding: 8px 14px;

  background-color: #fff5c4;

  cursor: pointer;
}

.profile-avatar {
  width: 42px;
  height: 42px;

  border-radius: 50%;
  object-fit: cover;

  border: 2px solid #df9800;
}

.profile-name {
  color: #423430;
  font-family: "coolvetica";
  font-size: 18px;
}

.dropdown-menu-custom {
  position: absolute;
  top: 62px;
  right: 0;

  width: 240px;

  background-color: white;

  border-radius: 16px;

  overflow: hidden;

  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);

  z-index: 1000;
}

.dropdown-item-custom {
  display: block;

  width: 100%;

  padding: 14px 18px;

  border: none;

  background: white;

  color: #423430;

  text-align: left;
  text-decoration: none;

  font-size: 16px;

  cursor: pointer;

  transition: background-color 0.2s ease;
}

.dropdown-item-custom:hover {
  background-color: #fff7d1;
}

.logout-item {
  color: #b85c4c;
}

@media (max-width: 992px) {

  .header {
    padding: 12px 16px;
  }

  .header-container {
    height: auto;

    flex-direction: column;

    gap: 16px;
  }

  .nav-area {
    flex-direction: column;

    width: 100%;

    gap: 16px;
  }

  .nav {
    justify-content: center;

    flex-wrap: wrap;

    gap: 8px;
  }

  .brand span {
    font-size: 34px;
  }

  .logo {
    width: 80px;
  }

  .admin-actions {
    justify-content: center;
  }
}
</style>
