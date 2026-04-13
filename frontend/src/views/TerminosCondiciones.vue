<template>
  <div class="terminos-contenedor">
    <div class="terminos-card">
      <h1>Términos y Condiciones</h1>
      <p>Bienvenido a Pawtita. Este documento describe de forma clara y sencilla cómo tratamos los datos personales dentro de este proyecto escolar.</p>

      <section>
        <h2>1. Datos que recogemos</h2>
        <p>En este sitio solo manejamos datos personales básicos necesarios para la gestión de usuarios y la experiencia dentro de la aplicación:</p>
        <ul>
          <li>Nombre</li>
          <li>Apellidos</li>
          <li>Edad</li>
          <li>Correo electrónico</li>
          <li>Sexo / género</li>
        </ul>
      </section>

      <section>
        <h2>2. Para qué usamos tus datos</h2>
        <p>Utilizamos tus datos únicamente para funciones propias del proyecto, como:</p>
        <ul>
          <li>Crear y gestionar cuentas de usuario.</li>
          <li>Personalizar la navegación dentro de la app.</li>
          <li>Enviar notificaciones o mensajes informativos si el proyecto lo integra.</li>
        </ul>
      </section>

      <section>
        <h2>3. Lo que no hacemos</h2>
        <p>Este proyecto es de carácter escolar y no trabaja con datos bancarios ni transacciones monetarias. No compartimos tus datos con terceros comerciales.</p>
      </section>

      <section>
        <h2>4. Seguridad y almacenamiento</h2>
        <p>Los datos se almacenan de forma segura dentro del sistema del proyecto. Se toman medidas básicas para protegerlos y evitar accesos no autorizados.</p>
      </section>

      <section>
        <h2>5. Derechos del usuario</h2>
        <p>Como usuario, tienes derecho a:</p>
        <ul>
          <li>Solicitar acceso a tus datos.</li>
          <li>Corregir información incorrecta.</li>
          <li>Solicitar la eliminación de tus datos si ya no son necesarios.</li>
        </ul>
      </section>

      <section>
        <h2>6. Contacto</h2>
        <p>Si necesitas más información o deseas ejercer tus derechos, puedes usar la página de contacto del proyecto.</p>
      </section>

      <p class="nota">Esta página es una versión genérica y de ejemplo creada para el proyecto escolar.</p>
    </div>
  </div>
</template>

<style scoped>
.terminos-contenedor {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: #f5f4f1;
}

.terminos-card {
  width: min(100%, 860px);
  background: white;
  border-radius: 24px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
  padding: 2.5rem;
  color: #3f3128;
}

h1, h2 {
  font-family: 'lemonMilk';
  color: #654236;
}

h1 {
  margin-bottom: 1rem;
}

h2 {
  margin-top: 1.75rem;
  margin-bottom: 0.75rem;
  font-size: 1.35rem;
}

p {
  line-height: 1.8;
  color: #5c5349;
}

ul {
  list-style: disc;
  padding-left: 1.3rem;
  margin: 0.75rem 0;
}

li {
  margin-bottom: 0.5rem;
}

.nota {
  margin-top: 2rem;
  color: #7b6b5c;
  font-style: italic;
}

@media (max-width: 640px) {
  .terminos-card {
    padding: 1.75rem;
  }

  h2 {
    font-size: 1.2rem;
  }
}
</style>