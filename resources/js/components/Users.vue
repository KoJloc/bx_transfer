<template>
<div class="container-fluid">
<div class="container">
<div class="row">
	<div class="relative flex w-72">
		<input v-model.trim="SearchData" id="goodsSearchButton" data-dropdown-toggle="goodsSearch" type="search" class="w-full px-5 py-2.5 text-sm rounded-lg bg-gray-50 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white searcher" placeholder="Введите имя, должность или id" />
		<div class="absolute top-0 right-0 flex items-center px-4 py-2.5 space-x-1">
			<button @click="Search()">
				<svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
				</svg>
			</button>
			<button @click="SetFilterDefaults()">
				<span class="w-5 h-5 text-red-600">✖</span>
			</button>
		</div>
	</div>
</div>

<div class="row">
	<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
		<thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
			<tr>
				<th scope="col" class="px-6 py-3">Id</th>
				<th scope="col" class="px-6 py-3">ФИО</th>
			</tr>
		</thead>
		<tbody v-for="user in myOptionsOnlyActive">
			<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
				<td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
					{{ user.id }}
				</td>
				<td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
					{{ user.text }}
				</td>
			</tr>
		</tbody>
	</table>
</div>

<!-- pagination -->
<div class="row">
	<button v-if="Page > 1" @click="Previous()" class="col-2 m-4 p-2">Назад</button>
	<button v-if="!IsLastPage" @click="Next()" class="col-2 m-4 p-2">Вперёд</button>
</div>
</div>
</div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
	name: 'Users',
	data() {
		return {
			Data: Array(),
			SearchData: String(),
			Page: Number(1),
			IsLastPage: Boolean(),
			PerPage: Number(20),
		}
	},
	mounted() {
		//this.GetHistory()
	},
	methods: {
		GetHistory(page = 1) {
			let url = `/api/histories/test?page=${page}
				&search=${this.SearchData}
				&limit=${this.PerPage}`

			axios.post(url).then(r => {
				let p = r.data.data
				console.log(p)
				this.Data = p.data
				this.IsLastPage = p.current_page == p.last_page
				console.log(this.IsLastPage)
				// this.PreparePagination(p)
			}).catch(e => console.log(e))
		},
		Previous() {
			if (this.Page <= 1) {
				console.log("this.Page = 1")
				this.Page = 1
				return
			}
			
			this.Page--
			this.Search(this.Page)
		},
		Next() {
			if (this.IsLastPage) {
				console.log(this.IsLastPage)
				return
			}
			
			this.Page++
			this.Search(this.Page)
		},
		Search(page = 1) {
			this.GetHistory(page)
			this.SetFilterDefaults()
		},
		SetFilterDefaults() {
			this.SearchData = ''
		},
		storeSettings({state, commit, dispatch}) {
			axios.post('/api/entities/get', {
				'onlyActiveDepartments': this.fromUsers,
			}).catch(e => console.log(e))
		},
	},

  watch: {
    fromUsers(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(el => !newValue.includes(el));
      } else {
        this.result = newValue.filter(el => !oldValue.includes(el));
      }
      for (let i = 0; i < this.Departments.length; i++) {
        if (this.result[0].id === this.Departments[i]) {
          this.Departments.splice(i, 1)
          // console.log('Удаляем совпадение')
          console.log(this.Departments)
          return
        }
      }
      this.Departments.push(this.result[0].id)
      console.log(this.Departments)
    },

    toUsers(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(el => !newValue.includes(el));
      } else {
        this.result = newValue.filter(el => !oldValue.includes(el));
      }
      for (let i = 0; i < this.onlyActiveDepartments.length; i++) {
        if (this.result[0].id === this.onlyActiveDepartments[i]) {
          this.onlyActiveDepartments.splice(i, 1)
          // console.log('Удаляем совпадение')
          console.log(this.onlyActiveDepartments)
          return
        }
      }
      this.onlyActiveDepartments.push(this.result[0].id)
      console.log(this.onlyActiveDepartments)
    },
    leadStatus(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(el => !newValue.includes(el));
      } else {
        this.result = newValue.filter(el => !oldValue.includes(el));
      }
      for (let i = 0; i < this.leadStatusNew.length; i++) {
        if (this.result[0].id === this.leadStatusNew[i]) {
          this.leadStatusNew.splice(i, 1)
          // console.log('Удаляем совпадение')
          console.log(this.leadStatusNew)
          return
        }
      }
      this.leadStatusNew.push(this.result[0].id)
      console.log(this.leadStatusNew)
    },
    leadType(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(el => !newValue.includes(el));
      } else {
        this.result = newValue.filter(el => !oldValue.includes(el));
      }
      for (let i = 0; i < this.leadTypeNew.length; i++) {
        if (this.result[0].id === this.leadTypeNew[i]) {
          this.leadTypeNew.splice(i, 1)
          // console.log('Удаляем совпадение')
          console.log(this.leadTypeNew)
          return
        }
      }
      this.leadTypeNew.push(this.result[0].id)
      console.log(this.leadTypeNew)
    },
    dealType(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(el => !newValue.includes(el));
      } else {
        this.result = newValue.filter(el => !oldValue.includes(el));
      }
      for (let i = 0; i < this.dealTypeNew.length; i++) {
        if (this.result[0].id === this.dealTypeNew[i]) {
          this.dealTypeNew.splice(i, 1)
          // console.log('Удаляем совпадение')
          console.log(this.dealTypeNew)
          return
        }
      }
      this.dealTypeNew.push(this.result[0].id)
      console.log(this.dealTypeNew)
    },
    dealFunnel(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(el => !newValue.includes(el));
      } else {
        this.result = newValue.filter(el => !oldValue.includes(el));
      }
      for (let i = 0; i < this.dealFunnelNew.length; i++) {
        if (this.result[0].id === this.dealFunnelNew[i]) {
          this.dealFunnelNew.splice(i, 1)
          // console.log('Удаляем совпадение')
          console.log(this.dealFunnelNew)
          return
        }
      }
      this.dealFunnelNew.push(this.result[0].id)
      console.log(this.dealFunnelNew)
    },
  },

  computed: {
    ...mapGetters({
      myOptions: 'myOptions',
      myOptionsOnlyActive: 'myOptionsOnlyActive',
    }),
  },
}
</script>
<style scoped>
</style>
