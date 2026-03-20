import { createRouter, createWebHistory } from 'vue-router'
import Index from '../views/Index.vue'
import Gatos from '../views/Gatos.vue'
import DetalleGatos from '../views/DetalleGatos.vue'
import Login from '../views/Login.vue'
import Registro from '../views/Registro.vue'
import PanelAdmin from '../views/PanelAdmin.vue'
import Contacto from '../views/Contacto.vue'

const routes = [
  { path: '/', component: Index },
  { path: '/gatos', component: Gatos },
  { path: '/gatos/:id', component: DetalleGatos },
  { path: '/login', component: Login },
  { path: '/registro', component: Registro },
  { path: '/admin', component: PanelAdmin },
  { path: '/contacto', component: Contacto }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router