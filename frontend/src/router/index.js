import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Index.vue'
import Gatos from '../views/Gatos.vue'
import DetalleGatos from '../views/DetalleGatos.vue'
import Login from '../views/Login.vue'
import Registro from '../views/Registro.vue'
import PanelAdmin from '../views/PanelAdmin.vue'
import Contacto from '../views/Contacto.vue'

const rutas = createRouter({
    history: createWebHistory(), 
    routes
})

export default rutas