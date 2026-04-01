<script setup>
  const nav = [
    { name: 'Adoptar', path: 'gatos' },
    { name: 'Colabora', path: 'colabora' },
    { name: 'Noticias', path: 'noticias' },
    { name: 'Iniciar Sesión', path: 'login' },
    { name: 'Registrarse', path: 'registro' },
    { name: 'Contacto', path: 'contacto' }
  ] 
</script>

<template>
    <header class="header d-flex flex-wrap justify-content-center py-3 border-bottom">
      <router-link
        to="/"
        class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none"
      >
      <img src="../assets/img/img_iconos/favicon.png" alt="Logo" class="logo" width="140">
        <span> 
          Pawtita
        </span>
      </router-link>

      <ul class="nav nav-pills">
        <li class="nav-item"
        v-for="pags in nav"
        :key="pags.name"
        >
        <router-link 
        class="nav-link"
        :to="pags.path"
        >
        {{ pags.name}}
        </router-link>
        </li>
      </ul>
    </header>
</template>

<style scoped>
body {
  margin: 0;
  padding: 0;
}
.header {
  background-color: #faf8b3;
  width: 100%;
  height: 80px;
}

.nav-link:hover {
  color: #df9800 !important; 
}

.nav-link {
  color: #654236;
    font-family: 'coolvetica';
    font-size: 20px;
}
span {
  margin-top: -30px;
  color: #654236;
  font-size: 50px;
  font-family: 'coolvetica';

}

.logo {
  margin-top: -20px;
}
</style>