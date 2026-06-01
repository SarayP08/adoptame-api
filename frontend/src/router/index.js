import { createRouter, createWebHistory } from 'vue-router'


import Index from '../views/public/Index.vue'
import Gatos from '../views/public/Gatos.vue'
import DetalleGatos from "../views/public/DetalleGatos.vue"
import Login from '../views/auth/Login.vue'
import Colabora from '../views/public/Colabora.vue'
import Noticias from '../views/public/Noticias.vue'
import Contacto from '../views/public/Contacto.vue'
import Registro from '../views/auth/Registro.vue'
import ContraOlvidada from '../views/auth/ContraOlvidada.vue'
import Terminos from '../views/public/TerminosCondiciones.vue'
import PanelAdministrador from "../views/admin/PanelAdmin.vue"
import AdminCrearGato from "../views/admin/gatos/AdminCrearGato.vue"
import AdminVerGato from "../views/admin/gatos/AdminVerGato.vue"
import AdminEditarGato from "../views/admin/gatos/AdminEditarGato.vue"
import UsuarioHome from "../views/usuario/UsuarioHome.vue"
import UsuarioAdoptarGato from "../views/usuario/UsuarioAdoptarGato.vue"


const routes = [
  { path: '/', component: Index },
  { path: '/gatos', component: Gatos },
  { path: '/detalleGato/:id', component: DetalleGatos},
  { path: '/iniciarSesion', component: Login },
  { path: '/registro', component: Registro },
  { path: '/colabora', component: Colabora },
  { path: '/noticias', component: Noticias },
  { path: '/contacto', component: Contacto },
  { path: '/panel-admin', component: PanelAdministrador },
  { path: '/terminosCondiciones', component: Terminos },
  { path: '/añadirGato', component: AdminCrearGato },
  { path: '/verGato', component: AdminVerGato },
  { path: '/editarGato/:id', component: AdminEditarGato },
  { path: '/usuario', component: UsuarioHome },
  { path: '/adoptar/:id', component: UsuarioAdoptarGato }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router