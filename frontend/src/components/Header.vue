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
        <li class="nav-item">
          <router-link class="nav-link" to="/">Adoptar</router-link>
        </li>

        <li class="nav-item">
          <router-link class="nav-link" to="/">Iniciar Sesión</router-link>
        </li>
        
        <li class="nav-item">
          <router-link class="nav-link" to="/">Colabora</router-link>
        </li>

        <li class="nav-item">
          <router-link class="nav-link" to="/">Noticias</router-link>
        </li>

        <li class="nav-item">
          <router-link class="nav-link" to="/">Contacto</router-link>
        </li>
      </ul>

    </header>
  </div>
</template>