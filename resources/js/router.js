import Vue from 'vue'
import VueRouter from 'vue-router'
import History from './components/History.vue'
import Users from './components/Users.vue'
import Show from './components/Show.vue'


Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/entities',
            component: () => import('./components/Person/Table.vue'),
            name: 'entities.index'
        },
        {
            path: '/history',
            component: History,
            name: 'history.index'
        },
        {
            path: '/history/show',
            component: Show,
            name: 'history.show'
        },
        {
            path: '/users',
            component: Users,
            name: 'users.index'
        },
        // {
        //     path: '/user/login',
        //     component: () => import('./components/Person/Login.vue'),
        //     name: 'user.login'
        // },
        // {
        //     path: '/user/registration',
        //     component: () => import('./components/Person/Registration.vue'),
        //     name: 'user.registration'
        // },
        // {
        //     path: '/user/personal',
        //     component: () => import('./components/Person/Personal.vue'),
        //     name: 'user.personal'
        // },
    ],
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('x_xsrf_token')
	return next()
    // if (to.name == 'entities.index') {
        // return next()
    // } else {
        // return next({
            // name: 'entities.index'
        // })
    // }

    // if(!token) {
    //     if (to.name === 'user.login' || to.name === 'user.registration') {
    //         return next()
    //     } else {
    //         return next({
    //             name: 'user.login'
    //         })
    //     }
    // }
    //
    // if (to.name === 'user.login' || to.name === 'user.registration' && token) {
    //     return next({
    //         name: 'user.personal'
    //     })
    // }
    // next()
})

export default router
