<script setup>
import { ref, onMounted } from 'vue';

const gatos = ref([]);

onMounted(async () => {
  const res = await fetch("https://saraypa.iesallerulloa.es/www/api/despliegueGatos.php");
  gatos.value = await res.json();
});
</script>


<template>
  <div class="container mt-5">
    <h1>Adoptar</h1>
    <p>Página de vista de gatos en construcción</p>
    <p>(Aquí falta el backend para ser funcional)</p>

  <div>
    <h2>Gatetes en adopción 🐱</h2>

    <div v-for="gato in gatos" :key="gato.id">
      <h3>{{ gato.nombre }}</h3>

      <img 
        :src="'https://saraypa.iesallerulloa.es/public_html' + gato.imagen" 
        width="200"
      >

      <p>{{ gato.descripcion }}</p>
    </div>

  </div>

  </div>
</template>