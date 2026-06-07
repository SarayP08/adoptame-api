<script setup>
import { onMounted } from "vue";

import Header from "./components/Header.vue";
import Footer from "./components/Footer.vue";

import { useAuthStore } from "./stores/auth";

const auth = useAuthStore();

onMounted(() => {
  auth.comprobarSesion();
});
</script>

<template>
  <Header />
  <router-view />
  <Footer />
</template>
