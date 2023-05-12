"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_History_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/History.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/History.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'History',
  data: function data() {
    return {
      Data: Array(),
      SearchData: String(),
      PerPage: Number(10),
      Loading: Boolean(true)
    };
  },
  mounted: function mounted() {
    this.$store.dispatch('getEntities');
  },
  methods: {
    // GetUsers(page = 1, PerPage = 10) {
    // //this.Loading = true
    // let url = `goods?page=${page}
    // &search=${this.SearchData}
    // &limit=${PerPage}`
    // this.ax.get(url).then(r => {
    // let p = r.data.data
    // this.goods = p.data
    // this.PreparePagination(p)
    // }).catch(e => {
    // this.toast(e.response.data.message, 'error')
    // })//.finally(() => this.Loading = false)
    // },
    Search: function Search() {
      console.log(this.SearchData);
      return;
      // this.GetUsers()
    },
    storeSettings: function storeSettings(_ref) {
      var state = _ref.state,
        commit = _ref.commit,
        dispatch = _ref.dispatch;
      axios.post('/api/entities/get', {
        'onlyActiveDepartments': this.fromUsers,
        'Departments': this.toUsers,
        'leadStatus': this.leadStatusNew,
        'leadType': this.leadTypeNew,
        'dealType': this.dealTypeNew,
        'dealFunnel': this.dealFunnelNew,
        'regionsList': this.regionsList,
        'citiesList': this.cities,
        'count': this.count,
        'sources': this.sources,
        'departments': this.departments,
        'newSource': this.new_source,
        'newSalesDepartment': this.new_sales_department
      })["catch"](function (e) {
        return console.log(e);
      });
    }
  },
  watch: {
    fromUsers: function fromUsers(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(function (el) {
          return !newValue.includes(el);
        });
      } else {
        this.result = newValue.filter(function (el) {
          return !oldValue.includes(el);
        });
      }
      for (var i = 0; i < this.Departments.length; i++) {
        if (this.result[0].id === this.Departments[i]) {
          this.Departments.splice(i, 1);
          // console.log('Удаляем совпадение')
          console.log(this.Departments);
          return;
        }
      }
      this.Departments.push(this.result[0].id);
      console.log(this.Departments);
    },
    toUsers: function toUsers(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(function (el) {
          return !newValue.includes(el);
        });
      } else {
        this.result = newValue.filter(function (el) {
          return !oldValue.includes(el);
        });
      }
      for (var i = 0; i < this.onlyActiveDepartments.length; i++) {
        if (this.result[0].id === this.onlyActiveDepartments[i]) {
          this.onlyActiveDepartments.splice(i, 1);
          // console.log('Удаляем совпадение')
          console.log(this.onlyActiveDepartments);
          return;
        }
      }
      this.onlyActiveDepartments.push(this.result[0].id);
      console.log(this.onlyActiveDepartments);
    },
    leadStatus: function leadStatus(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(function (el) {
          return !newValue.includes(el);
        });
      } else {
        this.result = newValue.filter(function (el) {
          return !oldValue.includes(el);
        });
      }
      for (var i = 0; i < this.leadStatusNew.length; i++) {
        if (this.result[0].id === this.leadStatusNew[i]) {
          this.leadStatusNew.splice(i, 1);
          // console.log('Удаляем совпадение')
          console.log(this.leadStatusNew);
          return;
        }
      }
      this.leadStatusNew.push(this.result[0].id);
      console.log(this.leadStatusNew);
    },
    leadType: function leadType(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(function (el) {
          return !newValue.includes(el);
        });
      } else {
        this.result = newValue.filter(function (el) {
          return !oldValue.includes(el);
        });
      }
      for (var i = 0; i < this.leadTypeNew.length; i++) {
        if (this.result[0].id === this.leadTypeNew[i]) {
          this.leadTypeNew.splice(i, 1);
          // console.log('Удаляем совпадение')
          console.log(this.leadTypeNew);
          return;
        }
      }
      this.leadTypeNew.push(this.result[0].id);
      console.log(this.leadTypeNew);
    },
    dealType: function dealType(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(function (el) {
          return !newValue.includes(el);
        });
      } else {
        this.result = newValue.filter(function (el) {
          return !oldValue.includes(el);
        });
      }
      for (var i = 0; i < this.dealTypeNew.length; i++) {
        if (this.result[0].id === this.dealTypeNew[i]) {
          this.dealTypeNew.splice(i, 1);
          // console.log('Удаляем совпадение')
          console.log(this.dealTypeNew);
          return;
        }
      }
      this.dealTypeNew.push(this.result[0].id);
      console.log(this.dealTypeNew);
    },
    dealFunnel: function dealFunnel(newValue, oldValue) {
      if (oldValue.length > newValue.length) {
        this.result = oldValue.filter(function (el) {
          return !newValue.includes(el);
        });
      } else {
        this.result = newValue.filter(function (el) {
          return !oldValue.includes(el);
        });
      }
      for (var i = 0; i < this.dealFunnelNew.length; i++) {
        if (this.result[0].id === this.dealFunnelNew[i]) {
          this.dealFunnelNew.splice(i, 1);
          // console.log('Удаляем совпадение')
          console.log(this.dealFunnelNew);
          return;
        }
      }
      this.dealFunnelNew.push(this.result[0].id);
      console.log(this.dealFunnelNew);
    }
  },
  computed: _objectSpread({}, (0,vuex__WEBPACK_IMPORTED_MODULE_0__.mapGetters)({
    myOptions: 'myOptions',
    myOptionsOnlyActive: 'myOptionsOnlyActive',
    leadStatusList: 'leadStatusList',
    leadTypeList: 'leadTypeList',
    dealTypeList: 'dealTypeList',
    dealFunnelList: 'dealFunnelList',
    citiesList: 'citiesList',
    regionsList: 'regionsList',
    sourcesList: 'sourcesList',
    departmentsList: 'departmentsList',
    salesDepartmentsList: 'salesDepartmentsList'
  }))
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/History.vue?vue&type=template&id=270eb80e&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/History.vue?vue&type=template&id=270eb80e&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "container-fluid"
  }, [_c("div", {
    staticClass: "relative flex w-72"
  }, [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model.trim",
      value: _vm.SearchData,
      expression: "SearchData",
      modifiers: {
        trim: true
      }
    }],
    staticClass: "w-full px-5 py-2.5 text-sm rounded-lg bg-gray-50 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white searcher",
    attrs: {
      id: "goodsSearchButton",
      "data-dropdown-toggle": "goodsSearch",
      type: "search",
      placeholder: "Введите имя, должность или id"
    },
    domProps: {
      value: _vm.SearchData
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.SearchData = $event.target.value.trim();
      },
      blur: function blur($event) {
        return _vm.$forceUpdate();
      }
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "absolute top-0 right-0 flex items-center px-4 py-2.5 space-x-1"
  }, [_c("button", {
    on: {
      click: function click($event) {
        return _vm.Search();
      }
    }
  }, [_c("svg", {
    staticClass: "w-5 h-5 text-gray-500 dark:text-gray-400",
    attrs: {
      "aria-hidden": "true",
      fill: "none",
      stroke: "currentColor",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }
  }, [_c("path", {
    attrs: {
      "stroke-linecap": "round",
      "stroke-linejoin": "round",
      "stroke-width": "2",
      d: "M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
    }
  })])]), _vm._v(" "), _c("button", {
    on: {
      click: function click($event) {
        return _vm.SetFilterDefaults();
      }
    }
  }, [_c("span", {
    staticClass: "w-5 h-5 text-red-600"
  }, [_vm._v("✖")])])])]), _vm._v(" "), _c("table", {
    staticClass: "w-full text-sm text-left text-gray-500 dark:text-gray-400"
  }, [_vm._m(0), _vm._v(" "), _vm._l(_vm.myOptionsOnlyActive, function (user) {
    return _c("tbody", [_c("tr", {
      staticClass: "bg-white border-b dark:bg-gray-800 dark:border-gray-700"
    }, [_c("td", {
      staticClass: "px-6 py-4 font-medium text-gray-900 dark:text-white",
      attrs: {
        scope: "row"
      }
    }, [_vm._v("\n\t\t\t\t\t" + _vm._s(user.id) + "\n\t\t\t\t")]), _vm._v(" "), _c("td", {
      staticClass: "px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white",
      attrs: {
        scope: "row"
      }
    }, [_vm._v("\n\t\t\t\t\t" + _vm._s(user.text) + "\n\t\t\t\t")])])]);
  })], 2)]);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("thead", {
    staticClass: "text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
  }, [_c("tr", [_c("th", {
    staticClass: "px-6 py-3",
    attrs: {
      scope: "col"
    }
  }, [_vm._v("Id")]), _vm._v(" "), _c("th", {
    staticClass: "px-6 py-3",
    attrs: {
      scope: "col"
    }
  }, [_vm._v("Название")]), _vm._v(" "), _c("th", {
    staticClass: "px-6 py-3",
    attrs: {
      scope: "col"
    }
  }, [_vm._v("Цена")]), _vm._v(" "), _c("th", {
    staticClass: "px-6 py-3",
    attrs: {
      scope: "col"
    }
  }, [_vm._v("Дата создания")])])]);
}];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/History.vue":
/*!*********************************************!*\
  !*** ./resources/js/components/History.vue ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _History_vue_vue_type_template_id_270eb80e_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./History.vue?vue&type=template&id=270eb80e&scoped=true& */ "./resources/js/components/History.vue?vue&type=template&id=270eb80e&scoped=true&");
/* harmony import */ var _History_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./History.vue?vue&type=script&lang=js& */ "./resources/js/components/History.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _History_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _History_vue_vue_type_template_id_270eb80e_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render,
  _History_vue_vue_type_template_id_270eb80e_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  "270eb80e",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/History.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/History.vue?vue&type=script&lang=js&":
/*!**********************************************************************!*\
  !*** ./resources/js/components/History.vue?vue&type=script&lang=js& ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_History_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./History.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/History.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_History_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/History.vue?vue&type=template&id=270eb80e&scoped=true&":
/*!****************************************************************************************!*\
  !*** ./resources/js/components/History.vue?vue&type=template&id=270eb80e&scoped=true& ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_History_vue_vue_type_template_id_270eb80e_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_History_vue_vue_type_template_id_270eb80e_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_History_vue_vue_type_template_id_270eb80e_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./History.vue?vue&type=template&id=270eb80e&scoped=true& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/History.vue?vue&type=template&id=270eb80e&scoped=true&");


/***/ })

}]);