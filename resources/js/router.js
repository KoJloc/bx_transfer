import Vue from 'vue'
import VueRouter from 'vue-router'


Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/persons', component:() => import('./components/Person/Index.vue'),
            name: 'person.index'
        },
        {
            path: '/create',
            component: () => import('./components/Person/Create.vue'),
            name: 'person.create'
        },
        {
            path: '/show',
            component: function (){return import('./components/Person/Show.vue')},
            name: 'person.show'
        },

        {
            path: '/edit',
            component: function (){return import('./components/Person/Edit.vue')},
            name: 'person.edit'
        },
        ],
})
