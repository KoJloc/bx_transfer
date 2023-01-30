<template>
    <div>
        <div class="form-floating mb-3 w-25">
            <input v-model="name" type="name" class="form-control" id="floatingInput" placeholder="Name">
            <label for="floatingInput">Your name</label>
        </div>
        <div class="form-floating mb-3 w-25">
            <input v-model="email" type="email" class="form-control" id="floatingInput" placeholder="Email">
            <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating mb-3 w-25">
            <input v-model="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
        </div>
        <div class="form-floating mb-3 w-25">
            <input v-model="password_confirmation" type="password" class="form-control" id="floatingPassword"
                   placeholder="password_confirmation">
            <label for="floatingPassword">Confirm your password</label>
        </div>
        <div class="form-floating w-25">
            <input @click.prevent="register" type="submit" value="Register" class="btn btn-primary">
        </div>
    </div>
</template>

<script>
export default {
    name: 'Registration',

    data() {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        }
    },

    methods: {
        register() {
            axios.get('/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('/register', {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    })
                        .then(response => {
                            localStorage.setItem('x_xsrf_token', response.config.headers['X-XSRF-TOKEN'])
                            this.$router.push({name: 'user.personal'})
                            console.log(response)
                        })
                        .catch(error => {
                            console.log(error.response);
                        })
                })
                .catch(
                    error => {
                        console.log(error.response);
                    })
        }
    }
}

</script>

<style scoped>

</style>
