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
  <div class="container">
    <footer class="py-3 my-4">

      <ul class="nav justify-content-center border-bottom pb-3 mb-3">
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