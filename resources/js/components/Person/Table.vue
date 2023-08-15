<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-3 mb-3">
        <h6><b>Выбор сотрудников</b></h6>
        <multiselect v-model="fromUsers"
                     :options="myOptions"
                     :multiple="true"
                     group-values="params"
                     group-label="department"
                     :group-select="true"
                     placeholder="С сотрудников"
                     track-by="text"
                     label="text">
          <template slot="option" slot-scope="props">
            <span v-if="props.option.text"
                  :style="{'color' : props.option.active ? '#030303' : '#ff0000'}">{{ props.option.text }}</span>
          </template>
          <template slot="tag" slot-scope="{ option, remove }">
            <span class="multiselect__tag" :style="{'background' : option.active ? '#41b883' : '#ff0000'}">
              <span>{{option.text }}</span>
              <span class="multiselect__tag-icon" @click="remove(option)"></span>
            </span>
          </template>
        </multiselect>
        <multiselect v-model="toUsers"
                     :options="myOptionsOnlyActive"
                     :multiple="true"
                     group-values="params"
                     group-label="department"
                     :group-select="true"
                     placeholder="На сотрудников"
                     track-by="text"
                     label="text">
        </multiselect>
        <!--
                <multiselect v-model="fromUsers"
                             class="mb-2"
                             id="myOptions"
                             label="text"
                             track-by="text"
                             placeholder="С сотрудников"
                             open-direction="bottom"
                             :options="myOptions"
                             :multiple="true"
                             :searchable="true"
                             :internal-search="true"
                             :clear-on-select="true"
                             :close-on-select="false"
                             :max-height="600"
                             :show-no-results="false"
                             :hide-selected="true"
                             group-values="params"
                             group-label="department"
                             :group-select="true">
                  <template slot="tag" slot-scope="{ option, remove }">
                  <span class="multiselect__tag"><span>{{option.params.text}}</span>
                    <span class="multiselect__tag-icon" @click="remove(option)"></span>
                  </span>
                  </template>
                  <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                </multiselect>
                <multiselect v-model="toUsers"
                             id="myOptionsOnlyActive"
                             label="text"
                             track-by="text"
                             placeholder="На сотрудников"
                             open-direction="bottom"
                             :options="myOptionsOnlyActive"
                             :multiple="true"
                             :searchable="true"
                             :internal-search="true"
                             :clear-on-select="true"
                             :close-on-select="false"
                             :max-height="600"
                             :show-no-results="false"
                             :hide-selected="true"
                             group-values="params"
                             group-label="department"
                             :group-select="true">
                  <template slot="tag" slot-scope="{ option, remove }">
                  <span class="multiselect__tag"><span>{{ option.params.text }}</span>
                    <span class="multiselect__tag-icon" @click="remove(option)"></span>
                  </span>
                  </template>
                  <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                </multiselect>
        -->
      </div>
      <div class="col">
        <div class="row">
          <div class="col-sm-3 d-flex flex-column">
            <h6><b>Тип сущности</b></h6>
            <label v-for="t in types" :key="t.name">
              <input type="radio" :value="t.name" v-model="checkedType" @change="ClearOnChange"/>
              <span>{{ t.text }}</span>
            </label>
          </div>
          <div class="col-sm-9">
            <div v-if="['lead', 'all'].includes(checkedType)" class="d-flex flex-row">
              <div class="col">
                <h6><b>Типы лида</b></h6>
                <div id="leadType" class="col">
                  <multiselect v-model="leadTypes"
                               id="leadTypeSelect"
                               label="text"
                               track-by="text"
                               placeholder="Тип лида"
                               open-direction="bottom"
                               :options="leadTypeList"
                               :multiple="true"
                               :searchable="true"
                               :internal-search="true"
                               :clear-on-select="true"
                               :close-on-select="false"
                               :max-height="600"
                               :show-no-results="false"
                               :hide-selected="true">
                    <template slot="tag" slot-scope="{ option, remove }">
										<span class="multiselect__tag"><span>{{ option.text }}</span>
											<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
										</span>
                    </template>
                    <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                  </multiselect>
                </div>
              </div>
              <div class="col">
                <h6><b>Статусы лида</b></h6>
                <div id="leadStatus" class="col">
                  <multiselect v-model="leadStatus"
                               id="leadStatusSelect"
                               label="text"
                               track-by="text"
                               placeholder="Статус лида"
                               open-direction="bottom"
                               :options="leadStatusList"
                               :multiple="true"
                               :searchable="true"
                               :internal-search="true"
                               :clear-on-select="true"
                               :close-on-select="false"
                               :max-height="600"
                               :show-no-results="false"
                               :hide-selected="true">
                    <template slot="tag" slot-scope="{ option, remove }">
										<span class="multiselect__tag"><span>{{ option.text }}</span>
											<span class="multiselect__tag-icon" @click="remove(option)"></span>
                      <!-- ❌ -->
										</span>
                    </template>
                    <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                  </multiselect>
                </div>
              </div>
            </div>
            <div v-if="['deal', 'all'].includes(checkedType)" class="d-flex flex-row">
              <div class="col">
                <h6><b>Типы сделки</b></h6>
                <multiselect v-model="dealTypes"
                             id="dealTypesSelect"
                             label="text"
                             track-by="text"
                             placeholder="Тип сделки"
                             open-direction="bottom"
                             :options="dealTypeList"
                             :multiple="true"
                             :searchable="true"
                             :internal-search="true"
                             :clear-on-select="true"
                             :close-on-select="false"
                             :max-height="600"
                             :show-no-results="false"
                             :hide-selected="true">
                  <template slot="tag" slot-scope="{ option, remove }">
										<span class="multiselect__tag"><span>{{ option.text }}</span>
											<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
										</span>
                  </template>
                  <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                </multiselect>
              </div>
              <div class="col">
                <h6><b>Направления сделки</b></h6>
                <multiselect v-model="dealFunnels"
                             id="dealStatusSelect"
                             label="text"
                             track-by="text"
                             placeholder="Направление сделки"
                             open-direction="bottom"
                             :options="dealFunnelList"
                             :multiple="true"
                             :searchable="true"
                             :internal-search="true"
                             :clear-on-select="true"
                             :close-on-select="false"
                             :max-height="600"
                             :show-no-results="false"
                             :hide-selected="true">
                  <template slot="tag" slot-scope="{ option, remove }">
										<span class="multiselect__tag"><span>{{ option.text }}</span>
											<span class="multiselect__tag-icon" @click="remove(option)"></span>
                      <!-- ❌ -->
										</span>
                  </template>
                  <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                </multiselect>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="d-flex flex-row row">
        <div v-if="checkedType === 'lead'" class="col-sm-3 d-flex flex-column">
          <h6><b>Горячие Лиды</b></h6>
          <label v-for="hon in IsLeadHotOrNot" :key="hon.name">
            <input type="radio" :value="hon.name" v-model="isLeadHot"/>
            <span>{{ hon.text }}</span>
          </label>
        </div>
        <div v-if="checkedType === 'lead'" class="col-lg-3 flex col">
          <h6><b>Город</b></h6>
          <input style="
				width: 100%;
				min-height: 44px;
        display: flex;
        padding: 4px 0px 0 8px;
        border-radius: 5px;
        border: 1px solid #e8e8e8;
        background: #fff;
        font-size: 16px;" type="text" v-model.trim="city"/>
        </div>
        <div v-if="checkedType === 'lead'" class="col-lg-3 flex col">
          <h6><b>Дополнительно об источнике</b></h6>
          <input style="
				width: 100%;
				min-height: 44px;
        display: flex;
        padding: 4px 0px 0 8px;
        border-radius: 5px;
        border: 1px solid #e8e8e8;
        background: #fff;
        font-size: 16px;" type="text" v-model.trim="aboutSource"/>
        </div>
        <div v-if="['lead', 'deal'].includes(checkedType)" class="col-lg-3 flex col">
          <h6><b>Регионы</b></h6>
          <multiselect v-model="regions"
                       id="regionsSelect"
                       label="text"
                       track-by="text"
                       placeholder="Регион"
                       open-direction="bottom"
                       :options="regionsList"
                       :multiple="true"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="true"
                       :close-on-select="false"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="true">
            <template slot="tag" slot-scope="{ option, remove }">
						<span class="multiselect__tag"><span>{{ option.text }}</span>
							<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
						</span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
        </div>
        <div v-if="['lead', 'deal'].includes(checkedType)" class="col-lg-3 flex col">
          <h6><b>Новый отдел продаж</b></h6>
          <multiselect v-model="salesDepartmentsChange"
                       id="salesDepartmentsChangeSelect"
                       label="text"
                       track-by="text"
                       placeholder="Отдел продаж"
                       open-direction="bottom"
                       :options="this.checkedType === 'lead'
                          ? this.salesDepartmentsLeadList : this.checkedType === 'deal'
                          ? this.salesDepartmentsDealList : this.salesDepartmentsContactList"
                       :multiple="false"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="false"
                       :close-on-select="true"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="false">
            <template slot="tag" slot-scope="{ option, remove }">
						<span class="multiselect__tag"><span>{{ option.text }}</span>
							<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
						</span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
        </div>
        <div v-if="['lead', 'deal'].includes(checkedType)" class="col-lg-3 flex col">
          <h6><b>Новый источник</b></h6>
          <multiselect v-model="sourcesChange"
                       id="sourcesChangeSelect"
                       label="text"
                       track-by="text"
                       placeholder="Источник"
                       open-direction="bottom"
                       :options="sourcesList"
                       :multiple="false"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="false"
                       :close-on-select="true"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="false">
            <template slot="tag" slot-scope="{ option, remove }">
						<span class="multiselect__tag"><span>{{ option.text }}</span>
							<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
						</span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
        </div>
      </div>
    </div>

    <!--Общие фильтры-->
    <div class="row mt-5">
      <div v-if="checkedType != 'all'" class="col-sm-2 d-flex flex-column">
        <h6><b>Количество</b></h6>
        <input style="
				width: 100%;
				min-height: 44px;
        display: flex;
        padding: 4px 0px 0 8px;
        border-radius: 5px;
        border: 1px solid #e8e8e8;
        background: #fff;
        font-size: 16px;" type="number" v-model="count"/>
      </div>
      <div v-if="checkedType != 'all'" class="col-sm-3 d-flex flex-column">
        <h6><b>Отдел продаж</b></h6>
        <div class="d-flex flex-row">
          <multiselect v-model="salesDepartments"
                       id="departmentsSelect"
                       label="text"
                       track-by="text"
                       :placeholder="this.checkedType === 'lead'
                  ? 'Отдел продаж Лиды' : this.checkedType === 'deal'
                  ? 'Отдел продаж Сделки' : 'Отдел продаж Контакты'"
                       open-direction="top"
                       :options="this.checkedType === 'lead'
                  ? this.salesDepartmentsLeadList : this.checkedType === 'deal'
                  ? this.salesDepartmentsDealList : this.salesDepartmentsContactList"
                       :multiple="true"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="false"
                       :close-on-select="false"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="true">
            <template slot="tag" slot-scope="{ option, remove }">
              <span class="multiselect__tag"><span>{{ option.text }}</span>
                <span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
              </span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
        </div>
      </div>
      <div v-else class="col-sm-9 d-flex flex-column">
        <h6><b>Отдел продаж</b></h6>
        <div class="d-flex flex-row">
          <multiselect v-model="salesDepartmentsLead"
                       id="departmentsSelect"
                       label="text"
                       track-by="text"
                       placeholder="Отдел продаж Лиды"
                       open-direction="top"
                       :options="salesDepartmentsLeadList"
                       :multiple="true"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="false"
                       :close-on-select="false"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="true">
            <template slot="tag" slot-scope="{ option, remove }">
							<span class="multiselect__tag"><span>{{ option.text }}</span>
								<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
							</span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
          <multiselect v-model="salesDepartmentsDeal"
                       id="departmentsSelect"
                       label="text"
                       track-by="text"
                       placeholder="Отдел продаж Сделки"
                       open-direction="top"
                       :options="salesDepartmentsDealList"
                       :multiple="true"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="false"
                       :close-on-select="false"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="true">
            <template slot="tag" slot-scope="{ option, remove }">
							<span class="multiselect__tag"><span>{{ option.text }}</span>
								<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
							</span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
          <multiselect v-model="salesDepartmentsContact"
                       id="departmentsSelect"
                       label="text"
                       track-by="text"
                       placeholder="Отдел продаж Контакты"
                       open-direction="top"
                       :options="salesDepartmentsContactList"
                       :multiple="true"
                       :searchable="true"
                       :internal-search="true"
                       :clear-on-select="false"
                       :close-on-select="false"
                       :max-height="600"
                       :show-no-results="false"
                       :hide-selected="true">
            <template slot="tag" slot-scope="{ option, remove }">
							<span class="multiselect__tag"><span>{{ option.text }}</span>
								<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
							</span>
            </template>
            <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
          </multiselect>
        </div>
      </div>

      <div class="col-sm-3 d-flex flex-column">
        <h6><b>Источники</b></h6>
        <multiselect v-model="sources"
                     id="sourcesSelect"
                     label="text"
                     track-by="text"
                     placeholder="Источник"
                     open-direction="top"
                     :options="sourcesList"
                     :multiple="true"
                     :searchable="true"
                     :internal-search="true"
                     :clear-on-select="false"
                     :close-on-select="false"
                     :max-height="600"
                     :show-no-results="false"
                     :hide-selected="true">
          <template slot="tag" slot-scope="{ option, remove }">
							<span class="multiselect__tag"><span>{{ option.text }}</span>
								<span class="multiselect__tag-icon" @click="remove(option)"></span> <!-- ❌ -->
							</span>
          </template>
          <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
        </multiselect>
      </div>
      <div class="col">
        <h6><b>Выбор даты</b></h6>
        <date-picker style="height: 44px" v-model="fromDate" type="date" placeholder="Начальная дата"></date-picker>
        <date-picker style="height: 44px" v-model="toDate" type="date" placeholder="Конечная дата"></date-picker>
      </div>
    </div>
    <div>
      <button @click.prevent="StoreSettings" type="submit" class="btn btn-primary mt-3">
        Подтвердить выбор
      </button>
      <button @click.prevent="CacheRefresh" :disabled="cache_refreshing" class="btn btn-secondary mt-3">
        Обновить кеш
      </button>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import Multiselect from 'vue-multiselect'
import Select2MultipleControl from 'v-select2-multiple-component'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import {mapGetters} from 'vuex'

export default {
  name: "Table",

  data() {
    return {
      //Users
      Departments: [],
      onlyActiveDepartments: [],
      //Filters
      fromUsers: [],
      toUsers: [],
      leadStatus: [],
      leadTypes: [],
      dealTypes: [],
      dealFunnels: [],
      regions: [],
      sources: [],
      salesDepartments: [],
      salesDepartmentsLead: [],
      salesDepartmentsDeal: [],
      salesDepartmentsContact: [],
      salesDepartmentsList: [],
      salesDepartmentsChange: [],
      sourcesChange: [],
      //Date
      fromDate: [],
      toDate: [],
      //Other
      aboutSource: '',
      city: '',
      count: "0",
      result: [],
      types: [
        {text: 'Лиды', name: 'lead'},
        {text: 'Сделки', name: 'deal'},
        {text: 'Контакты', name: 'contact'},
        {text: 'Все', name: 'all'}
      ],
      checkedType: String('lead'),
      IsLeadHotOrNot: [
        {text: 'Да', name: '1'},
        {text: 'Нет', name: '0'},
        {text: 'Любые', name: 'null'},
      ],
      isLeadHot: String('null'),
      cache_refreshing: Boolean(false),
    }
  },

  components: {
    Select2MultipleControl,
    Multiselect,
    DatePicker,
  },

  mounted() {
    this.$store.dispatch('getEntities')
  },

  methods: {
    ClearOnChange() {
      this.salesDepartments = []
      this.leadStatus = [];
      this.leadTypes = [];
      this.dealTypes = [];
      this.dealFunnels = [];
      this.regions = [];
      this.sourcesChange = [];
      this.salesDepartmentsChange = [];
      this.salesDepartmentsContact = [];
      this.salesDepartmentsDeal = [];
      this.salesDepartmentsLead = [];
    },
    StoreSettings({state, commit, dispatch}) {
      axios.post('/api/entities/params/set', {
        'departments': this.fromUsers,
        'onlyActiveDepartments': this.toUsers,
        'checkedType': this.checkedType,
        'hotLeadList': this.isLeadHot,
        'leadStatus': this.leadStatus,
        'leadType': this.leadTypes,
        'dealType': this.dealTypes,
        'dealFunnel': this.dealFunnels,
        'regions': this.regions,
        'aboutSource': this.aboutSource,
        'city': this.city,
        'count': this.count,
        'sources': this.sources,
        'salesDepartments': this.salesDepartments,
        'salesDepartmentsLead': this.salesDepartmentsLead,
        'salesDepartmentsDeal': this.salesDepartmentsDeal,
        'salesDepartmentsContact': this.salesDepartmentsContact,
        'newSource': this.sourcesChange,
        'newSalesDepartment': this.salesDepartmentsChange,
        'fromDate': this.fromDate,
        'toDate': this.toDate,
      }).then(r => {
        Vue.$toast(`Передача запущена, ${r.data.data} в очереди`, {
          timeout: 2000
        });
      }).catch(e => {
        console.log(e)
        Vue.$toast("Не удалось запустить передачу", {
          timeout: 2000
        });
      });
    },
    CacheRefresh() {
      this.cache_refreshing = true
      Vue.$toast("Запущено обновление кеша, \nожидайте завершения процесса", {timeout: 2000})

      axios.post('/api/cache/refresh').then(res => {
        let message = res == []
          ? "Не удалось обновить кеш, \nперезагрузите страницу или \nобратитесь к администратору"
          : "Кеш успешно обновлён"
        Vue.$toast(message, {timeout: 2000})
      }).catch(console.error)
      .finally(() => this.cache_refreshing = false)
    },
  },

  computed: {
    ...mapGetters({
      myOptions: 'myOptions',
      myOptionsOnlyActive: 'myOptionsOnlyActive',
      leadStatusList: 'leadStatusList',
      leadTypeList: 'leadTypeList',
      dealTypeList: 'dealTypeList',
      dealFunnelList: 'dealFunnelList',
      regionsList: 'regionsList',
      sourcesList: 'sourcesList',
      departmentsList: 'departmentsList',
      salesDepartmentsLeadList: 'salesDepartmentsLeadList',
      salesDepartmentsDealList: 'salesDepartmentsDealList',
      salesDepartmentsContactList: 'salesDepartmentsContactList',
    }),
  },
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>

.multiselect__option_custom {
  display: block;
  color: #fff324;
  padding: 12px;
  min-height: 40px;
  line-height: 16px;
  text-decoration: none;
  text-transform: none;
  vertical-align: middle;
  position: relative;
  cursor: pointer;
  white-space: nowrap
}

</style>
