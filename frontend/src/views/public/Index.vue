<script setup>
import { ref, onMounted } from 'vue'
import '../../assets/css/css_index.css'

const imagenes = [
  new URL('../../assets/img/img_index/gatoC_1.png', import.meta.url).href,
  new URL('../../assets/img/img_index/gatoC_2.png', import.meta.url).href,
  new URL('../../assets/img/img_index/gatoC_3.png', import.meta.url).href
];

const posts = [
  {
    id: 1,
    titulo: "La segunda oportunidad de Simón",
    descripcion: "Simón fue rescatado en condiciones difíciles, pero hoy disfruta de un hogar lleno de cariño.",
    imagen: new URL('../../assets/img/img_index/simon.jpeg', import.meta.url).href
  },
  {
    id: 2,
    titulo: "Nueva ley del bienestar animal",
    descripcion: "Te explicamos los puntos clave de la nueva normativa y cómo afecta a las adopciones.",
    imagen: new URL('../../assets/img/img_index/kitten.png', import.meta.url).href
  },
  {
    id: 3,
    titulo: "3 señales de que tu gato sufre en silencio",
    descripcion: "Aprende a identificar comportamientos que pueden indicar que tu gato no está bien.",
    imagen: new URL('../../assets/img/img_index/gato_callejero.png', import.meta.url).href
  }
];

  const imagenActual = ref(0);
  onMounted(() => {
    setInterval(() => {
      imagenActual.value = (imagenActual.value + 1) % imagenes.length;
    }, 5000);
    });

</script>

    <template>
    <div class="index-page">
      <div class="hero-slider">

        <div
            v-for="(img,index) in imagenes"
            :key="index"
            class="hero-slide"
            :class="{ active: index === imagenActual }"
            :style="{ backgroundImage: `url(${img})` }"
        ></div>

        <div class="containerTexto">
          <h1> Porque cuando todo falla,</h1>
          <h2>Ellos te dan la patita</h2>
          <p class="lead">Encuentra al nuevo miembro de tu familia con Pawtita.</p>
          <div class="d-grid gap-2 d-md-flex justify-content-md-start heroii">
            <RouterLink
                :to="'/gatos'"
                class="btn btn-primary btn-lg px-4 me-md-2">
              Adoptar
            </RouterLink>
          </div>
        </div>
      </div>


      <div class="container mt-5 second-hero">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <img src="../../assets/img/img_index/programadora.png" alt="Programadora de la página web" class="img-fluid rounded shadow-sm" />
          </div>
          <div class="col-lg-6">
            <h1 class="display-5  lh-1 mb-3 textoHero">¿Quiénes somos?</h1>
            <p class="leadi">
              Soy una desarrolladora junior amante de los animales con el propósito 
              de crear una plataforma que facilite la adopción de mascotas en Lalín.
               Mi pasión por los animales y mi deseo de marcar una diferencia positiva 
               me llevaron a desarrollar este proyecto, donde conecto a personas que buscan 
               un compañero con animales que necesitan un hogar.
              Creo en la adopción responsable, por eso colaboro con una protectora local 
              y me comprometo a ofrecer un espacio seguro y confiable para que las mascotas
               encuentren su lugar en el mundo.
            </p>
            <div class="d-flex justify-content-center align-items-center">
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">Únete a nosotros</button>
            </div>
          </div>
        </div>
      </div>

      <div class="container mt-4 third-hero">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="display-5 lh-1 mb-3 textoHero">Mi misión</h1>
            <p class="leadi">
              Mi misión es crear un espacio donde las personas puedan encontrar a su compañero ideal, 
              al mismo tiempo que se promueve la adopción responsable y se brinda apoyo a los gatos necesitados. A
               través de esta plataforma, busco conectar a aquellos que buscan un nuevo amigo 
              peludo con aquellos que necesitan un hogar amoroso, 
              fomentando así una comunidad de amantes de los animales comprometidos con el bienestar de nuestras mascotas.
            </p>
            <div class="d-flex justify-content-center align-items-center">
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">Colabora con la causa</button>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <img src="../../assets/img/img_index/gato_dormido.png" alt="Gato callejero" class="img-fluid rounded shadow-sm" />
          </div>
        </div>
         </div>
      <div class="container mt-5 novedades">
  <h2 class="mb-4 text-center titulo-novedades">Novedades</h2>
  <div class="row">
    
    <div class="col-md-4 mb-4" v-for="post in posts" :key="post.id">
      <div class="card h-100 shadow-sm">

        <img :src="post.imagen" class="card-img-top" alt="imagen" />

        <div class="card-body d-flex flex-column">
          <h5 class="card-title">{{ post.titulo }}</h5>
          <p class="card-text">{{ post.descripcion }}</p>

          <button class="btn btn-primary mt-auto">Leer más →</button>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
</template>