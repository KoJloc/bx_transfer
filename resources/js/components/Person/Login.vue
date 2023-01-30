<template>
    <div>
        <div class="form-floating mb-3 w-25">
            <input v-model="email" type="email" class="form-control" id="floatingInput" placeholder="Email">
            <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating mb-3 w-25">
            <input v-model="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
        </div>
        <div class="form-floating w-25">
            <input @click.prevent="login" type="submit" value="Login" class="btn btn-primary">
        </div>
    </div>
</template>

<script>
export default {
    name: 'Login',

    data() {
        return {
            email: '',
            password: '',
        }
    },

    methods: {
        login() {
            axios.get('/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('/login',  {email: this.email, password: this.password,})
                        .then(r => {
                            localStorage.setItem('x_xsrf_token', r.config.headers['X-XSRF-TOKEN'])
                            this.$router.push({name: 'person.index'})
                        })
                        .catch( err => {
                            console.log(err.response)
                            }
                        )
                })
        }
    }
}

</script>

<style scoped>

</style>
