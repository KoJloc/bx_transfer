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
                <router-link :to="{name: 'dashboard.index'}" class="nav-link text-white">
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
  }
}
</script>

<style scoped>

</style>
