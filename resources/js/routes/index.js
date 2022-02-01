import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import Home from './../views/Home.vue'
import HostEditor from './../components/HostEditor.vue'

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
]

//routes = routes.concat(authRoutes)

let router = new Router({
  //base: process.env.BASE_URL,
  routes: routes,
})



export default router