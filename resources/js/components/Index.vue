<template>
  <div>
    <header class="mb-2">
      <div class="px-3 py-2 bg-dark text-white">
        <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
			<router-link :to="{ name: 'entities.index' }" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white fs-4 fw-bold">
              <svg class="bi me-2" width="64" height="32" role="img" aria-label="Bootstrap">
                <use xlink:href="#bootstrap"/>
              </svg>
              TRANSFER
			</router-link>
			
            <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
              <li>
                <router-link :to="{ name: 'history.index' }" class="nav-link text-white">
                  <svg class="bi d-block mx-auto mb-1" width="24" height="24">
                    <use xlink:href="#table"/>
                  </svg>
                  История
                </router-link>
              </li>
              <li>
                <router-link :to="{name: 'user.personal'}" class="nav-link text-white">
                  <svg class="bi d-block mx-auto mb-1" width="24" height="24">
                    <use xlink:href="#grid"/>
                  </svg>
                  Дашборд
                </router-link>
              </li>
              <li>
                <router-link :to="{name: 'users.index'}" class="nav-link text-white">
                  <svg class="bi d-block mx-auto mb-1" width="24" height="24">
                    <use xlink:href="#people-circle"/>
                  </svg>
                  Пользователи
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>
<!--        <nav class="navbar navbar-expand-lg navbar-light bg-light">-->
<!--              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"-->
<!--                      aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">-->
<!--                <span class="navbar-toggler-icon"></span>-->
<!--              </button>-->
<!--              <div class="col">-->
<!--                <div class="collapse navbar-collapse" id="navbarText">-->
<!--                  <ul class="navbar-nav me-auto mb-2 mb-lg-0">-->
<!--                    <li class="nav-item">-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                      <a v-if="token" class="nav-link disabled" href="#">Disabled</a>-->
<!--                    </li>-->
<!--                  </ul>-->
<!--                                      <span class="navbar-text me-3">-->
<!--                                          <router-link v-if="!token" :to="{name: 'user.login'}" class=" btn btn-outline-primary btn-sm">Login</router-link>-->
<!--                                      </span>-->
<!--                                      <span class="navbar-text me-3">-->
<!--                                          <router-link v-if="!token" :to="{name: 'user.registration'}" class=" btn btn-outline-secondary btn-sm">Register</router-link>-->
<!--                                      </span>-->
<!--                                      <span class="navbar-text me-3">-->
<!--                                          <a v-if="token" @click.prevent="logout" href="#" class="btn btn-outline-danger btn-sm">Logout</a>-->
<!--                                      </span>-->
<!--                </div>-->
<!--              </div>-->
<!--        </nav>-->
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
    this.$router.push({name: 'entities.index'})
    this.getToken()
  },

  updated() {
    this.getToken()
  },


  methods: {

    getToken() {
      this.token = localStorage.getItem('x_xsrf_token')
    },

    // logout() {
    //     axios.post('/logout')
    //         .then(res => {
    //             localStorage.removeItem('x_xsrf_token')
    //             this.$router.push({name: 'user.login'})
    //         })
    // }
  }
}
</script>

<style scoped>

</style>
