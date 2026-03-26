<script setup>
  const nav = [
    { name: 'Adoptar', path: 'gatos' },
    { name: 'Iniciar Sesión', path: 'login' },
    { name: 'Colabora', path: 'colabora' },
    { name: 'Noticias', path: 'noticias' },
    { name: 'Contacto', path: 'contacto' }
  ] 
</script>

<template>
    <header class="header d-flex flex-wrap justify-content-center py-3 border-bottom">
      <router-link
        to="/"
        class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none"
      >
        <span class="fs-4">Pawtita</span>
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
  height: 100%;
}

.nav-link {
  color: #654236;
  font-weight: 500;
}
span {
  color: #654236;
  font-weight: 700;
  margin-left: 120px;
}
</style>