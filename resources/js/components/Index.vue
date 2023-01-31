<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <router-link v-if="token" :to="{name: 'person.index'}" class="navbar-brand">Table</router-link>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
<!--                        <li class="nav-item">-->
<!--                            <router-link v-if="token" :to="{name: 'person.create'}" class="nav-link">Create</router-link>-->
<!--                        </li>-->
                        <li class="nav-item">
                            <router-link v-if="token" :to="{name: 'user.personal'}" class="nav-link">My page</router-link>
                        </li>
                        <li class="nav-item">
                            <a v-if="token" class="nav-link disabled" href="#">Disabled</a>
                        </li>
                    </ul>
                    <span class="navbar-text me-3">
                        <router-link v-if="!token" :to="{name: 'user.login'}" class=" btn btn-outline-primary btn-sm">Login</router-link>
                    </span>
                    <span class="navbar-text me-3">
                        <router-link v-if="!token" :to="{name: 'user.registration'}" class=" btn btn-outline-secondary btn-sm">Register</router-link>
                    </span>
                    <span class="navbar-text me-3">
                        <a v-if="token" @click.prevent="logout" href="#" class="btn btn-outline-danger btn-sm">Logout</a>
                    </span>
                </div>
            </div>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>

export default {
    name: "Index",
    data() {
        return {
            token: '',
        }
    },

    mounted() {
        this.getToken()
    },

    updated() {
        this.getToken()
    },


    methods: {

        getToken() {
            this.token = localStorage.getItem('x_xsrf_token')
        },

        logout() {
            axios.post('/logout')
                .then(res => {
                    localStorage.removeItem('x_xsrf_token')
                    this.$router.push({name: 'user.login'})
                })
        }
    }
}
</script>

<style scoped>

</style>
