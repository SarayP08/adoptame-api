<script setup>

</script>

<template>
  <div class="contenedor">
    <div class="contenedorI">
    <form>
      <h2>Ponte en contacto</h2>
      <label for="text">Nombre: </label>
      <input type="text" class="form-control" id="text" placeholder="Introduce tu nombre">
      <label for="email">Correo electrónico: </label>
      <input type="email" class="form-control" id="email" placeholder="Introduce tu correo electrónico">
      <label for="message">Mensaje: </label>
      <textarea class="form-control" id="message" rows="4" placeholder="Introduce tu mensaje"></textarea>
      <button type="submit" class="btn btn-primary">Enviar</button>

    </form>
    </div>
  </div>
</template>

<style scoped>

h1, h2 {
  font-family: 'lemonMilk';
  color: #654236;
}

.contenedor {
  background-image: url(../assets/img/img_contacto/gato.png);
  background-size: cover;
  background-position: center;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.contenedor::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(65, 62, 80, 0.6); 
}

.contenedorI{
  max-width: 600px;
  padding: 2.5rem;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  position: relative;
  z-index: 1;
  color: #654236;
  text-align: center;
  transition: transform 0.3s ease;
}

.contenedorI label {
  text-align: left;
  display: block;
  margin-bottom: 0.5rem;
}

.form-control {
  border-radius: 8px;
  border: 1px solid #ddd;
  margin-bottom: 1rem;
}

.form-control:focus {
  border-color: #f09014;
  box-shadow: 0 0 0 0.2rem rgba(240, 144, 20, 0.25);
}

.btn-primary {
  background-color: #f09014;
  border: none;
}

.btn-primary:hover {
  background-color: #d97f10;
}
</style>