<template>
  <div class="container-fluid">
    <div class="container">
      <form>
        <div class="row mb-2">
          <p for="SearchInput" class="col-sm-2 col-form-label">Поиск по ID передачи</p>
          <div class="col-sm-6">
            <input v-model.trim="SearchData" id="SearchInput" type="search" class="form-control"
                   placeholder="Введите ID передачи" @keyup.enter="Search()">
          </div>
        </div>
        <div class="row mb-3">
          <p for="DateInput" class="col-sm-2 col-form-label">Поиск по дате создания </p>
          <div class="col-sm-6">
            <date-picker id="DateInput" class="custom-datepicker" v-model.trim="SearchDate" type="datetime" range valueType="format"></date-picker>
          </div>
          <div class="col-sm-3">
            <button class="btn btn-outline-success" type="button" @click="Search()">Поиск</button>
            <button class="btn btn-outline-danger" type="button" @click="SetFilterDefaults()">Сброс</button>
          </div>
        </div>
      </form>
      <div class="row p-3">
        <table class="table table-hover">
          <thead>
          <tr>
            <th scope="col" class="px-6 py-3">ID</th>
            <th scope="col" class="px-6 py-3">Статус передачи</th>
            <th scope="col" class="px-6 py-3">Статус отката</th>
            <th scope="col" class="px-6 py-3">Дата создания</th>
            <th scope="col" class="px-6 py-3">Дата изменения</th>
            <th scope="col" class="px-6 py-3">Начать откат</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="history in Data">
            <td><router-link :to="{name: 'history.show', params: {id: history.id, page: Page }}">{{ history.id }}</router-link></td>
            <td>{{ history.transfer_group_status === 1 ? 'Успешно' : 'Не успешно' }}</td>
            <td>{{ history.rollback_status === 1 ? 'Выполнено' : 'Не запускался' }}</td>
            <td>{{ history.created_at }}</td>
            <td>{{ history.updated_at }}</td>
            <td>
              <button @click="Rollback(history.id)" type="button" class="btn btn-outline-warning btn-sm">Откатить
              </button>
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
      let url = `/api/histories/history?page=${page}
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
    Rollback(transferGroupID) {
      axios.post('api/transfer/rollback', {
        'transferGroupID': transferGroupID
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
