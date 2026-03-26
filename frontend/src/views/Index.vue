<script setup>
</script>
    <template>
    <div class="index-page">
     <div class="container-fluid hero" >
      <div class="containerTexto">
            <h1>
            Porque cuando todo falla
            </h1>
            <h2>Ellos te dan la patita</h2>
            <p class="lead">
             Encuentra al nuevo miembro de tu familia con Pawtita.
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start heroii">
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">
               Adoptar
              </button>
            </div>
          </div>
      </div>


      <div class="container mt-5 second-hero">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <img src="../assets/img/img_index/gato_dormido.png" alt="Gato dormido" class="img-fluid rounded shadow-sm" />
          </div>
          <div class="col-lg-6">
            <h1 class="display-5 fw-bold lh-1 mb-3 textoHero">
              ¿Quiénes somos?
            </h1>
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
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">
                Únete a nosotros
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="container mt-4 third-hero">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="display-5 fw-bold lh-1 mb-3 textoHero">
              Mi misión
            </h1>
            <p class="leadi">
              Mi misión es crear un espacio donde las personas puedan encontrar a su compañero ideal, 
              al mismo tiempo que se promueve la adopción responsable y se brinda apoyo a los gatos necesitados. A
               través de esta plataforma, busco conectar a aquellos que buscan un nuevo amigo 
              peludo con aquellos que necesitan un hogar amoroso, 
              fomentando así una comunidad de amantes de los animales comprometidos con el bienestar de nuestras mascotas.
            </p>
            <div class="d-flex justify-content-center align-items-center">
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">
                Colabora con la causa
              </button>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <img src="../assets/img/img_index/gato_callejero.png" alt="Gato callejero" class="img-fluid rounded shadow-sm" />
          </div>
        </div>
      </div>
      </div>
</template>

<style scoped>

.containerTexto {
  margin-left: 60px;
}


.index-page {
  background-color: #f19f32;
  min-height: 100vh;
}
.hero {
  background-image: url('../assets/img/img_index/kitten.png');
  background-size: cover;
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.hero h1 {
  color: #ffffff;

}
.hero h2 {
  color: #fff;
}

.lead {
  color: #fff;
}
.leadi {
  color: #654236;
}

button {
  background-color: #f09014;
  border: none;
  display: flex; 
  color: rgb(255, 255, 255);
  
}
.second-hero,
.third-hero {
  background-color: #faf8b3;
  border-radius: 14px;
  padding: 2rem;
  margin-bottom: 1.5rem;
}

.second-hero .leadi,
.third-hero .leadi {
  color: #654236;
  text-align: justify;
}

.second-hero .textoHero,
.third-hero .textoHero {
  color: #ad6119;
  text-align: center;
}

</style>