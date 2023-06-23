<template>
  <div class="container-fluid">
    <div class="container">
      <div class="row">
        <div class="col-sm-6"><div id="chart_roles"></div></div>
        <div class="col-sm-6"><div id="chart_prices"></div></div>
      </div>
      <div class="row">
        <div class="col-sm-12"><div id="chart_sales"></div></div>
      </div>
      </div>
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import * as echarts from 'echarts/core'
import {
  DatasetComponent,
  GridComponent,
  TransformComponent,
  TitleComponent,
  TooltipComponent,
  ToolboxComponent,
  LegendComponent
} from 'echarts/components'
import { PieChart, BarChart, LineChart } from 'echarts/charts'
import { UniversalTransition } from 'echarts/features'
import { CanvasRenderer } from 'echarts/renderers'

echarts.use([
  DatasetComponent,
  GridComponent,
  TransformComponent,
  TitleComponent,
  TooltipComponent,
  ToolboxComponent,
  LegendComponent,
  PieChart,
  BarChart,
  LineChart,
  CanvasRenderer,
  UniversalTransition
])

export default {
  name: 'History',

  components: {
    DatePicker
  },

  data() {
    return {
    }
  },
  mounted() {
    this.InitTypesPercents()
    this.InitGroupsCounts()
    this.InitDatesCount()
  },
  methods: {
    InitTypesPercents() {
      let d = [];
      axios.post('api/dashboard/types')
          .then(r => {
            let p = r.data.data
            console.log(p)
            p.forEach(pd => d.push({value: pd.count, name: pd.entity_type}))

            var myChart = echarts.init(document.getElementById('chart_roles'), null, {
              width: 600,
              height: 400,
            })

            // Draw the chart
            myChart.setOption({
              title: {
                text: 'Процентаж сущностей',
                subtext: '',
                left: 'center'
              },
              tooltip: {
                trigger: 'item'
              },
              legend: {
                orient: 'horizontal',
                top: 'bottom'
              },
              toolbox: {
                feature: {
                  restore: {},
                  saveAsImage: {}
                }
              },
              series: [
                {
                  name: '',
                  type: 'pie',
                  radius: '50%',
                  data: d,
                  emphasis: {
                    itemStyle: {
                      shadowBlur: 10,
                      shadowOffsetX: 0,
                      shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                  }
                }
              ]
            })
          }).catch(e => this.toast(e.response.data.message, 'error'))
    },
    InitGroupsCounts() {
      let d = [];
      axios.post('api/dashboard/counts')
          .then(r => {
            let p = r.data.data
            console.log(p)
            p.forEach(pd => d.push({count: pd.count, type: pd.type}))


            var myChart = echarts.init(document.getElementById('chart_prices'), null, {
              width: 600,
              height: 400,
            })
            // Draw the chart
            myChart.setOption({
              title: {
                text: 'Общее количество',
                subtext: '',
                left: 'center'
              },
              xAxis: {
                type: 'category',
                axisLabel: { interval: 0, rotate: 0 }
              },
              yAxis: {},
              series: {
                type: 'bar',
                encode: { x: 'name', y: 'count' },
                datasetIndex: 1
              },
              tooltip: {
                trigger: 'item'
              },
              toolbox: {
                feature: {
                  restore: {},
                  saveAsImage: {}
                }
              },
              dataset: [
                {
                  dimensions: ['count', 'type'],
                  source: p,
                },
                {
                  transform: {
                    type: 'sort',
                    config: { dimension: 'count', order: 'desc' }
                  }
                }
              ],
            })
          }).catch(e => this.toast(e.response.data.message, 'error'))
    },
    InitDatesCount() {
      let d = [], dates = [], counts = []

      axios.post('api/dashboard/dates')
          .then(r => {
            let p = r.data.data

            p.forEach(pd => {
              dates.push(pd.date)
              counts.push(pd.count)
            })

            d.push({
              name: 'Counts',
              type: 'line',
              data: counts
            })


            var myChart = echarts.init(document.getElementById('chart_sales'), null, {
              width: 1200,
              height: 500,
            })

            myChart.setOption({
              title: {
                text: 'Количество сущностей по дням',
                left: 'center'
              },
              xAxis: {
                type: 'category',
                boundaryGap: false,
                data: dates
              },
              yAxis: {},
              tooltip: {
                trigger: 'axis'
              },
              legend: {
                data: ['Counts'],
                top: 'bottom'
              },
              grid: {
                left: '10%',
                right: '10%',
                bottom: '10%',
                containLabel: true
              },
              toolbox: {
                feature: {
                  dataZoom: {
                    yAxisIndex: 'none'
                  },
                  restore: {},
                  saveAsImage: {}
                }
              },
              series: d
            })
          }).catch(e => this.toast(e.response.data.message, 'error'))
    },
  },
}
</script>
<style scoped>
</style>
