import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import Home from './../views/Home.vue'
import HostEditor from './../components/HostEditor.vue'
import Settings from './../views/Settings.vue'

// Routes
let routes = [
    {
        path: '/',
        name: 'home',
        meta: {title: 'Dashboard'},
        component: Home,
        children: [
          {
            path: 'hosts/:id',
            name: 'host_edit',
            component: HostEditor,
            props: true,
          }
        ]
      },
      {
        path: '/settings',
        name: 'settings',
        component: Settings,
      }
]

//routes = routes.concat(authRoutes)

let router = new Router({
  //base: process.env.BASE_URL,
  routes: routes,
  linkActiveClass: "has-active",
  linkExactActiveClass: "active"
})



export default router