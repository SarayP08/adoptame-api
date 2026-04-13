<script setup>

</script>

<template>
  <div class="contenedor">
    <div class="contenedorI">
      <div class="intro">
        <h1>¿Quieres colaborar?</h1>
        <p>
          Si te gustaría apoyar a nuestros gatitos, aquí tienes las formas más rápidas de hacerlo.
          Cada donación nos ayuda a cubrir alimento, veterinario y cuidados.
        </p>
      </div>

      <div class="info-grid">
        <div class="info-card">
          <span class="info-icon">💛</span>
          <h2>Bizum</h2>
          <p>000-00-00-00</p>
        </div>

        <div class="info-card">
          <span class="info-icon">🏦</span>
          <h2>Cuenta bancaria</h2>
          <p>ES12 3456 7890 1234 5678 9012</p>
        </div>

        <div class="info-card">
          <span class="info-icon">📍</span>
          <h2>Dirección de recogida</h2>
          <p>Rúa Genérica, 30, Lalín, Pontevedra</p>
        </div>
      </div>

      <div class="cta">
        <p>¿Prefieres colaborar con material o voluntariado? Escríbenos desde la página de contacto.</p>
        <a class="btn-outline" href="/contacto">Contactar</a>
      </div>
    </div>
  </div>
</template>

<style scoped>

h1, h2 {
  font-family: 'lemonMilk';
  color: #654236;
}

.contenedor {
  background-image: url(../assets/img/img_colabora/gato.png);
  background-size: cover;
  background-position: right center;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  padding: 2rem 1rem;
}

.contenedor::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(65, 62, 80, 0.65);
}

.contenedorI {
  width: min(100%, 680px);
  padding: 2.5rem;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 24px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
  position: relative;
  z-index: 1;
  color: #3e2a1f;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contenedorI:hover {
  transform: translateY(-6px);
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.22);
}

.intro p {
  margin: 1rem auto 0;
  max-width: 520px;
  color: #5d4b42;
  line-height: 1.8;
}

.info-grid {
  display: grid;
  gap: 1rem;
  margin: 2rem 0;
}

.info-card {
  display: grid;
  gap: 0.5rem;
  padding: 1.35rem 1.25rem;
  border-radius: 18px;
  background: #fff;
  border: 1px solid rgba(240, 144, 20, 0.18);
  box-shadow: 0 12px 30px rgba(235, 186, 136, 0.12);
}

.info-icon {
  display: inline-flex;
  width: 3rem;
  height: 3rem;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  border-radius: 50%;
  background: rgba(240, 144, 20, 0.12);
}

.info-card h2 {
  margin: 0;
  font-size: 1rem;
  letter-spacing: 0.01em;
}

.info-card p {
  margin: 0;
  font-weight: 600;
  color: #3f3128;
}

.cta {
  padding-top: 1.5rem;
  border-top: 1px solid rgba(101, 66, 54, 0.12);
}

.cta p {
  margin: 0 0 1rem;
  color: #6b5447;
  line-height: 1.7;
}

.btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.95rem 1.6rem;
  border-radius: 999px;
  border: 1px solid #f09014;
  background: transparent;
  color: #654236;
  text-decoration: none;
  transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
}

.btn-outline:hover {
  background: #f09014;
  color: white;
  transform: translateY(-2px);
}

@media (min-width: 768px) {
  .info-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>