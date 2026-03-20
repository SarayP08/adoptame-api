<script setup>
</script>
    <template>
     <div class="container-fluid hero" >
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

          <div class="col-lg-6 ms-auto">
            <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3 text-end">
              ¿Quiénes somos?
            </h1>
            <p class="leadi text-end">
              Soy una desarrolladora junior amante de los animales con el propósito 
              de crear una plataforma que facilite la adopción de mascotas en Lalín.
               Mi pasión por los animales y mi deseo de marcar una diferencia positiva 
               me llevaron a desarrollar este proyecto, donde conecto a personas que buscan 
               un compañero con animales que necesitan un hogar.
              Creo en la adopción responsable, por eso colaboro con una protectora local 
              y me comprometo a ofrecer un espacio seguro y confiable para que las mascotas
               encuentren su lugar en el mundo.
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">
                Únete a nosotros
              </button>
            </div>
          </div>

          <div class="col-lg-6">
            <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">
            Mi misión
            </h1>
            <p class="leadi">
              Mi misión es crear un espacio donde las personas puedan encontrar a su compañero ideal, 
              al mismo tiempo que se promueve la adopción responsable y se brinda apoyo a los gatos necesitados. A
               través de esta plataforma, busco conectar a aquellos que buscan un nuevo amigo 
              peludo con aquellos que necesitan un hogar amoroso, 
              fomentando así una comunidad de amantes de los animales comprometidos con el bienestar de nuestras mascotas.
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
              <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">
                Colabora con la causa
              </button>
            </div>
          </div>
</template>

<style scoped>
.hero {
  background-image: url('../assets/img/img_index/kitten.png');
  background-size: cover;
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.hero h1 {
  color: #fff;

}
.hero h2 {
  color: #fff;
}

.lead {
  color: #fff;
}
.leadi {
  color: #000;
}


button {
  background-color: #7fb680;
  border: none;
}
</style>