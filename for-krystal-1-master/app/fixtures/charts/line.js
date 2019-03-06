import echarts from 'vue-echarts/components/ECharts.vue'

export let option = {
	xAxis: {
	},
	yAxis: {
	},
	series: []
}

export let data = {
	grid: {
		left: '3%',
		right: '4%',
		top: '10%',
		containLabel: true,
		borderColor: 'transparent'
	},
	dataZoom: [{
		type: 'slider',
		width: '88%',
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
		}
	},
	xAxis: {
		type: 'category',
		data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
		boundaryGap: false,
		axisLine: {
			show: false
		},
		splitLine: { show: false }
	},
	yAxis: {
		type: 'value',
		axisLine: {
			show: false
		}
	},
	legend: {
		data: ['Mintos', 'Global Yield'],
		icon: 'circle',
		right: '5%'
	},
	series: [{
		data: [80, 130, 110, 170, 110, 250, 140],
		type: 'line',
		name: 'Mintos',
		animationEasing: 'linear',
		areaStyle: { opacity: 0 }
	},
	{
		data: [100, 160, 140, 200, 140, 280, 160],
		type: 'line',
		name: 'Global Yield',
		animationEasing: 'linear',
		areaStyle: { opacity: 0 }
	}]
}
