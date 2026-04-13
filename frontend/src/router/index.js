import { createRouter, createWebHistory } from 'vue-router'

import Gatos from '../views/Gatos.vue'
import Login from '../views/Login.vue'
import Colabora from '../views/Colabora.vue'
import Noticias from '../views/Noticias.vue'
import Contacto from '../views/Contacto.vue'
import Index from '../views/Index.vue'
import Registro from '../views/Registro.vue'
import ContraOlvidada from '../views/ContraOlvidada.vue'
import Terminos from '../views/TerminosCondiciones.vue'

const routes = [
  { path: '/', component: Index },
  { path: '/gatos', component: Gatos },
  { path: '/login', component: Login },
  { path: '/registro', component: Registro },
  { path: '/colabora', component: Colabora },
  { path: '/noticias', component: Noticias },
  { path: '/contacto', component: Contacto },
  { path: '/contra-olvidada', component: ContraOlvidada },
  { path: '/terminos', component: Terminos }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router