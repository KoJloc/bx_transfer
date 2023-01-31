import Vue from 'vue'
import VueRouter from 'vue-router'
import {next} from "lodash/seq";


Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/people',
            component: () => import('./components/Person/Table.vue'),
            name: 'person.index'
        },
        // {
        //     path: '/people/create',
        //     component: () => import('./components/Person/Create.vue'),
        //     name: 'person.create'
        // },
        // {
        //     path: '/people/:id/edit',
        //     component: () => import('./components/Person/Edit.vue'),
        //     name: 'person.edit'
        // },
        // {
        //     path: '/people/:id',
        //     component: () => import('./components/Person/Show.vue'),
        //     name: 'person.show'
        // },
        {
            path: '/user/login',
            component: () => import('./components/Person/Login.vue'),
            name: 'user.login'
        },
        {
            path: '/user/registration',
            component: () => import('./components/Person/Registration.vue'),
            name: 'user.registration'
        },
        {
            path: '/user/personal',
            component: () => import('./components/Person/personal.vue'),
            name: 'user.personal'
        },
        ],
})

router.beforeEach( (to, from, next) => {
    const token = localStorage.getItem('x_xsrf_token');

    if(!token) {
        if (to.name === 'user.login' || to.name === 'user.registration') {
            return next()
        } else {
            return next({
                name: 'user.login'
            })
        }
    }

    if (to.name === 'user.login' || to.name === 'user.registration' && token) {
        return next({
            name: 'user.personal'
        })
    }
    next()
})

export default router
