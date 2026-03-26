<script setup>
const enlaces = [
  { name: 'Inicio', path: '/' },
  { name: 'Regístrate', path: '/register' },
  { name: 'Colabora con nosotros', path: '/colabora' },
  { name: 'FAQs', path: '/faqs' },
  { name: 'Ayuda', path: '/ayuda' }
]
</script>

<template>
  <div class="container-fluid">
    <footer class="py-3">

      <ul class="nav justify-content-center border-bottom">
        <li
          class="nav-item"
          v-for="link in enlaces"
          :key="link.name"
        >
          <router-link
            class="nav-link px-2 text-body-secondary"
            :to="link.path"
          >
            {{ link.name }}
          </router-link>
        </li>
      </ul>

      <p class="text-center text-body-secondary">
        &copy; {{ new Date().getFullYear() }} Pawtita
      </p>

    </footer>
  </div>
</template>

<style scoped>
.container-fluid {
  padding: 0;
}

footer {
  background-color: #756969;
  padding: 1rem 0;
  width: 100%;
}

footer ul {
  margin: 0;
}

footer p {
  margin: 0;
  color:#ffffff;
}

footer .nav-link {
  color: #fff !important;
}
</style>