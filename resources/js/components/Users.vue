<template>
  <div class="container-fluid">
    <div class="container">
      <form class="pt-2">
        <div class="row mb-2">
          <div class="col-sm-4">
            <input v-model.trim="SearchData" id="SearchInput" type="search" class="form-control"
                   placeholder="Введите ФИО сотрудника" @keyup.enter="Search()">
          </div>
            <div class="col-sm-3">
              <button class="btn btn-outline-success" type="button" @click="Search()">Поиск</button>
              <button class="btn btn-outline-danger" type="button" @click="SetFilterDefaults()">Сброс</button>
            </div>
        </div>
        <div class="row mb-3">
          <p style="display:none;" for="DateInput" class="col-sm-2 col-form-label">Поиск по дате создания </p>
          <div style="display:none;" class="col-sm-6">
            <date-picker style="display:none;" id="DateInput" class="custom-datepicker" v-model.trim="SearchDate" type="datetime" range valueType="format"></date-picker>
          </div>
        </div>
      </form>
      <div class="row p-3">
        <table class="table table-hover">
          <thead>
          <tr>
            <th scope="col" class="px-6 py-3">#</th>
            <th scope="col" class="px-6 py-3"></th>
            <th scope="col" class="px-6 py-3">ФИО</th>
            <th scope="col" class="px-6 py-3">Должность</th>
            <th scope="col" class="px-6 py-3">Уволен</th>
            <th scope="col" class="px-6 py-3">Доступ</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in Data">
            <th scope="row">{{ user.id }}</th>
            <td><img class="img-thumbnail" style="width: 50px; height: 50px" :src="user.image"/></td>
            <td>{{ user.full_name }}</td>
            <td>{{ user.job }}</td>
            <td>{{ user.active === 1 ? 'Не уволен' : 'Уволен' }}</td>
            <td>
              <div class="form-check form-switch align-items-center">
                <input class="form-check-input" v-model="user.verified" @change="VerifyUser(user.id, user.verified)"
                       type="checkbox" role="switch">
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <!-- pagination -->
      <div class="row" v-if="!(Pagination.total <= Pagination.per_page)">
        <nav aria-label="Навигация">
          <ul class="pagination">
            <li class="page-item">
              <button @click="Previous()" :disabled="Page == 1" class="page-link">Previous</button>
            </li>
            <li v-for="link in Pagination.links" class="page-item">
              <template v-if="Number(link.label) &&
                      (Pagination.current_page - link.label < 3 &&
                      Pagination.current_page - link.label > -3) ||
                      Number(link.label) === 1 ||  Number(link.label) === Pagination.last_page">
                <a @click="Search(link.label)" class="page-link" :class="link.active ?  'active' : ''"
                   href="#">{{ link.label }}</a>
              </template>
              <template v-if="Number(link.label) &&
                        Pagination.current_page !== 4 &&
                      (Pagination.current_page - link.label === 3) ||
                          Number(link.label) &&
                          Pagination.current_page !== Pagination.last_page - 2 &&
                          Pagination.current_page + 3 !== Pagination.last_page  &&
                      (Pagination.current_page - link.label === -3)">
                <a class="page-link">...</a>
              </template>
            </li>
            <li class="page-item">
              <button @click="Next()" :disabled="IsLastPage" class="page-link">Next</button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'

export default {
  name: 'History',

  components: {
    DatePicker
  },

  data() {
    return {
      Data: Array(),
      Pagination: Array(),
      SearchData: String(),
      SearchDate: String(),
      Page: Number(1),
      Waiting: Boolean(),
      IsLastPage: Boolean(),
      PerPage: Number(12),
      Rofl: true,
    }
  },
  mounted() {
    if(typeof(this.$route.params.page) != "undefined" && this.$route.params.page !== null ){
      this.Page = this.$route.params.page
    }

    this.GetHistory(this.Page)
  },
  methods: {
    GetHistory(page = 1) {
      if (this.Waiting) return

      this.Waiting = true
      let url = `/api/users/get?page=${page}
				&search=${this.SearchData}
				&date=${this.SearchDate}
				&limit=${this.PerPage}`

      axios.post(url).then(r => {
        let p = r.data.data

        if (p.data.length == 0) {
          this.IsLastPage = true
          return
        }

        this.Data = p.data
        this.Pagination = r.data.data
        this.IsLastPage = p.current_page == p.last_page
      }).catch(e => console.log(e))
          .finally(() => this.Waiting = false)
    },
    VerifyUser(id, verified) {
      axios.post('https://transfer.stepan.sms19.ru/api/users/verify', {
        'id': id,
        'verified': verified
      }).catch(e => console.log(e))
    },
    Previous() {
      if (this.Page <= 1) {
        this.Page = 1
        return
      }

      this.Page--
      this.Search(this.Page)
    },
    Next() {
      if (this.IsLastPage) {
        return
      }

      this.Page++
      this.Search(this.Page)
    },
    Search(page = 1) {
      this.Page = page
      this.GetHistory(page)
      this.SetFilterDefaults()
    },
    SetFilterDefaults() {
      this.SearchDate = ''
      this.SearchData = ''
      this.GetHistory()
    },
  },
}
</script>
<style scoped>
</style>
