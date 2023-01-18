import Vue from 'vue'
import VueRouter from 'vue-router'


Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/people',
            component: () => import('./components/Person/Table.vue'),
            name: 'person.index'
        },
        {
            path: '/people/create',
            component: () => import('./components/Person/Create.vue'),
            name: 'person.create'
        },
        {
            path: '/people/:id/edit',
            component: () => import('./components/Person/Edit.vue'),
            name: 'person.edit'
        },
        {
            path: '/people/:id',
            component: () => import('./components/Person/Show.vue'),
            name: 'person.show'
        },
        ],
})
