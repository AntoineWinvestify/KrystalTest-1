import echarts from 'vue-echarts/components/ECharts.vue'
import dataNet from '../netDeposit'
import dataInvestments from '../activeInvestment'

export let option = {
	xAxis: {
	},
	yAxis: {
	},
	series: []
}

let dataActive = []
let dataLegendActive = []

dataInvestments.forEach(item => {
	dataActive.push(item.date)
	dataLegendActive.push(item.userinvestmentdata_numberActiveInvestments)
})

export let data = {
	grid: {
		left: '3%',
		right: '4%',
		top: '10%',
		containLabel: true,
		borderColor: 'transparent'
	},
	tooltip: {
		trigger: 'axis',
		axisPointer: {
			type: 'shadow',
			animation: false,
			label: {
				backgroundColor: '#ccc',
				borderColor: '#aaa',
				borderWidth: 1,
				shadowBlur: 0,
				shadowOffsetX: 0,
				shadowOffsetY: 0,
				textStyle: {
					color: '#FFFFFF'
				}
			}
		},
		formatter: function (params) {
			return `${params[0].value} € <br />  ${params[0].axisValue}`
		}
	},
	dataZoom: [{
		type: 'slider',
		width: '72%',
		left: 80,
		start: 80,
		end: 100,
		lineStyle: {
			color: '#81D648'
		},
		dataBackground: {
			areaStyle: {
				color: '#393e44'
			}
		},
		backgroundColor: '#393e44',
		fillerColor: '#81d6486b',
		handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
		handleSize: '80%',
		handleStyle: {
			color: '#fff',
			shadowBlur: 3,
			shadowColor: 'rgba(0, 0, 0, 0.6)',
			shadowOffsetX: 2,
			shadowOffsetY: 2
		}
	}],
	xAxis: {
		axisLabel: {
			color: '#FFFFFF'
		},
		show: false,
		axisLine: {
			show: false
		},
		type: 'category',
		splitLine: { show: false },
		boundaryGap: false,
		data: dataActive
	},
	yAxis: {
		axisLine: {
			show: false
		},
		boundaryGap: ['50%', '50%'],
		axisLabel: {
			color: '#FFFFFF',
			formatter: function (value) {
				return `${value} %`
			}
		},
		type: 'value'
	},
	series: [{
		data: dataLegendActive,
		type: 'line',
		animationEasing: 'linear',
		areaStyle: {}
	}]
}

let date = []

let dataToFillChart = []

dataNet.forEach(item => {
	date.push(item.date)
	dataToFillChart.push(item.userinvestmentdata_totalNetDeposits)
})

export let optionArea = {
	tooltip: {
		trigger: 'axis',
		axisPointer: {
			type: 'shadow',
			animation: false,
			label: {
				backgroundColor: '#ccc',
				borderColor: '#aaa',
				borderWidth: 1,
				shadowBlur: 0,
				shadowOffsetX: 0,
				shadowOffsetY: 0,
				textStyle: {
					color: '#FFFFFF'
				}
			}
		},
		formatter: function (params) {
			return `${params[0].axisValue} </br> ${params[0].value.toLocaleString('it-IT', {style: 'currency', currency: 'EUR', maximumFractionDigits: 4})}`
		}
	},
	dataZoom: [{
		type: 'slider',
		width: '72%',
		left: 80,
		start: 80,
		end: 100,
		lineStyle: {
			color: '#81D648'
		},
		dataBackground: {
			areaStyle: {
				color: '#393e44'
			}
		},
		backgroundColor: '#393e44',
		fillerColor: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
			offset: 0,
			color: '#353c42'
		}, {
			offset: 1,
			color: '#000b1623'
		}]),
		handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
		handleSize: '80%',
		handleStyle: {
			color: '#fff',
			shadowBlur: 3,
			shadowColor: 'rgba(0, 0, 0, 0.6)',
			shadowOffsetX: 2,
			shadowOffsetY: 2
		}
	}],
	grid: {
		left: '3%',
		right: '4%',
		top: '10%',
		containLabel: true,
		borderColor: 'transparent'
	},
	legend: {
		top: 'bottom'
	},
	xAxis: {
		type: 'category',
		boundaryGap: false,
		axisLabel: {
			show: true
		},
		show: true,
		splitLine: {
			show: false
		},
		data: date
	},
	yAxis: {
		type: 'value',
		boundaryGap: [0, '100%'],
		axisLine: {
			show: false
		},
		axisLabel: {
			color: '#FFFFFF',
			formatter: function (value) {
				return `${value.toLocaleString('it-IT', {style: 'currency', currency: 'EUR'})}`
			}
		},
		splitLine: {
			show: true,
			lineStyle: {
				color: 'rgba(255, 255, 255, .12)'
			}
		}
	}
}

export let dataArea = {
	series: [
		{
			type: 'line',
			smooth: true,
			symbol: 'none',
			sampling: 'average',
			itemStyle: {
				normal: {
					color: '#5d6166'
				}
			},
			areaStyle: {
				normal: {
					color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
						offset: 0,
						color: '#353c42'
					}, {
						offset: 1,
						color: '#000b1623'
					}])
				}
			},
			data: dataToFillChart
		}
	]
}
