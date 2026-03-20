<script setup>
  const nav = [
    { name: 'Adoptar', path: '/' },
    { name: 'Iniciar Sesión', path: '/' },
    { name: 'Colabora', path: '/' },
    { name: 'Noticias', path: '/' },
    { name: 'Contacto', path: '/' }
  ] 
</script>

<template>
  <div class="container">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
      
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
  </div>
</template>