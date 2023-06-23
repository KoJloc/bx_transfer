<template>
  <div class="container-fluid">
    <div class="container">
      <form>
        <div class="row mb-3">
          <p for="SearchInput" class="col-sm-3 col-form-label">Поиск по ID сущности</p>
          <div class="col-sm-9">
            <input v-model.trim="SearchData" id="SearchInput" type="search" class="form-control"
                   placeholder="Введите ID передачи" @keyup.enter="Search()">
          </div>
        </div>
        <div class="row mb-3">
          <p for="inputEmail3" class="col-sm-3 col-form-label">Поиск по дате создания </p>
          <div class="col-sm-6">
            <date-picker v-model.trim="SearchDateData" valueType="format" datetime range></date-picker>
          </div>
          <div class="col-sm-3">
            <button class="btn btn-outline-success" type="button" @click="Search()">Поиск</button>
            <button class="btn btn-outline-danger" type="button" @click="SetFilterDefaults()">Сброс</button>
          </div>
        </div>
      </form>
      <div class="row">
        <table class="table table-hover">
          <thead>
          <tr>
            <th scope="col" class="px-6 py-3">ID сущности</th>
            <th scope="col" class="px-6 py-3">Тип сущности</th>
            <th scope="col" class="px-6 py-3">С кого</th>
            <th scope="col" class="px-6 py-3">На кого</th>
            <th scope="col" class="px-6 py-3">Статус передачи</th>
            <th scope="col" class="px-6 py-3">Статус отката</th>
            <th scope="col" class="px-6 py-3">Дата создания</th>
            <th scope="col" class="px-6 py-3">Дата изменения</th>
          </tr>
          </thead>
          <tbody v-for="history in Data">
          <tr>
            <th scope="row"><a target="_blank" :href="GenerateLink(history.entity_ID, history.entity_type)">{{history.entity_ID}}</a></th>
            <td>{{ history.entity_type }}</td>
            <td>{{ history.old_responsible_ID }}</td>
            <td>{{ history.new_responsible_ID }}</td>
            <td>{{ history.transfer_status }}</td>
            <td>{{ history.rollback_status }}</td>
            <td>{{ history.created_at }}</td>
            <td>{{ history.updated_at }}</td>
          </tr>
          </tbody>
        </table>
      </div>

      <!-- pagination -->
      <div class="row" >
        <div class="col" v-if="!(Pagination.total <= Pagination.per_page) || Pagination.data.length === null">
        <nav aria-label="Навигация">
          <ul class="pagination" >
            <li class="page-item">
              <button @click="Previous()" :disabled="Page == 1" class="page-link">Previous</button>
            </li>
            <li v-for="link in Pagination.links" class="page-item" v-if="Pagination.next_page_url !== null">
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
            <li class="page-item" v-if="Pagination.next_page_url !== null">
              <button @click="Next()" :disabled="IsLastPage" class="page-link">Next</button>
            </li>
          </ul>
        </nav>
        </div>
        <div class="col">
        <router-link class="btn btn-primary ms-auto"
                     :to="{name: 'history.index', params: {page: this.$route.params.page}}">Назад
        </router-link>
        </div>
        <!--	<button @click="Next()" :disabled="IsLastPage" class="col-2 m-4 p-2">Вперёд</button>-->
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
      SearchDateData: String(),
      Page: Number(1),
      Waiting: Boolean(),
      IsLastPage: Boolean(),
      PerPage: Number(15),
    }
  },
  mounted() {
    this.GetHistory()
  },
  methods: {
    GenerateLink(id, type) {
      let confirmed_types = ['lead', 'deal', 'contact']
      if (confirmed_types.includes(type)) {
        return `https://xn--24-9kc.xn--d1ao9c.xn--p1ai/crm/${type}/details/${id}/`
      }
    },
    GetHistory(page = 1) {
      if (this.Waiting) return

      this.Waiting = true
      let url = `/api/histories/history/show?page=${page}
        &transfer_group=${this.$route.params.id}
				&search=${this.SearchData}
				&limit=${this.PerPage}`

      axios.post(url).then(r => {
        let p = r.data.data

        if (p.data.length == 0) {
          this.IsLastPage = true
          return
        }

        this.Data = p.data
        console.log(this.Data)
        this.Pagination = r.data.data
        console.log(this.Pagination)
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
      this.SearchData = ''
      this.GetHistory()
    },
  },
}
</script>
<style scoped>
</style>
