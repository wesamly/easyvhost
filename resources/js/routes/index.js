import { createRouter, createWebHashHistory } from 'vue-router'

import Home from './../views/Home.vue'
import HostEditor from './../components/HostEditor.vue'
import Tags from './../views/Tags.vue'
import Settings from './../views/Settings.vue'

// Routes
let routes = [
    {
        path: '/',
        name: 'home',
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
        path: '/tags',
        name: 'tags',
        component: Tags,
      },
      {
        path: '/settings',
        name: 'settings',
        component: Settings,
      }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  linkActiveClass: "has-active",
  linkExactActiveClass: "active"
})

export default router