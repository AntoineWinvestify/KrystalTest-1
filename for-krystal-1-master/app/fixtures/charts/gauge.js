import echarts from 'vue-echarts/components/ECharts.vue'
let bgColor = '#393E44'
let	highlight = '#03b7c9'
let opacityColor = '#FFFFFF70'

export let optionGauge = {
	title: {
		textStyle: {
			fontSize: 30,
			color: '#fff'
		}
	},
	grid: {
		height: '20%'
	},
	series: []
}

export let dataGauge = function (value, max = 100, type = 1) {
	let color = type === 1 ? colors.normal : colors.inverted
	return {
		series: [
			{
				type: 'gauge',
				center: ['50%', '60%'],
				splitNumber: 5,
				min: 0,
				max,
				startAngle: 180,
				endAngle: 0,
				clockwise: true,
				axisLine: {
					show: true,
					lineStyle: {
						width: 2,
						shadowBlur: 0,
						color: [
							[1, opacityColor]
						]
					}
				},
				axisTick: {
					show: true,
					lineStyle: {
						color: opacityColor,
						width: 1
					},
					length: -4,
					splitNumber: 10
				},
				splitLine: {
					show: true,
					length: -2,
					lineStyle: {
						color: opacityColor
					}
				},
				axisLabel: {
					distance: -20,
					textStyle: {
						color: opacityColor,
						fontSize: '11',
						fontWeight: 'bold'
					}
				},
				pointer: {
					show: 0
				},
				detail: {
					show: false
				}
			},
			{
				center: ['50%', '60%'],
				type: 'gauge',
				radius: '70%',
				startAngle: 180,
				endAngle: 0,
				axisLine: {
					lineStyle: {
						shadowColor: 'rgba(0, 0, 0, 1)',
						shadowBlur: 1,
						color
					}
				},
				axisLabel: {
					show: false
				},
				axisTick: {
					show: false
				},
				splitLine: {
					show: false
				},
				detail: {
					show: false
				},
				pointer: {
					show: false
				}
			},
			{
				type: 'gauge',
				startAngle: 180,
				endAngle: 0,
				radius: '40%',
				center: ['50%', '60%'],
				splitNumber: 0,
				min: 0,
				max: 100,
				axisLine: {
					lineStyle: {
						color: [
							[1, highlight]
						],
						width: '1%'
					}
				},
				axisLabel: {
					show: false
				},
				splitLine: {
					lineStyle: {
						width: 0
					}
				},
				pointer: {
					color: highlight,
					width: '30%',
					length: '170%'
				},
				detail: {
					show: false
				},
				data: [{
					name: '',
					value
				}]
			},
			{
				tooltip: {
					show: false
				},
				type: 'pie',
				radius: '38%',
				center: ['50%', '60%'],
				hoverAnimation: false,
				itemStyle: {
					normal: {
						color: bgColor
					},
					emphasis: {
						color: bgColor
					}
				},
				labelLine: {
					normal: {
						show: false
					}
				},
				animation: false,
				data: [value]
			}
		]
	}
}

let colors = {
	normal: [
		[
			0.20, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 0, color: '#e8412e' }, { offset: 1, color: '#eb7742' }])
		],
		[
			0.40, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 0, color: '#eb7742' }, { offset: 1, color: '#edb168' }])
		],
		[
			0.60, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 0, color: '#edb168' }, { offset: 1, color: '#d8d563' }])
		],
		[
			0.80, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 0, color: '#d8d563' }, { offset: 1, color: '#b2f268' }])
		],
		[
			1, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 0, color: '#b2f268' }, { offset: 1, color: '#6fed5c' }])
		]
	],
	inverted: [
		[
			0.20, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 1, color: '#b2f268' }, { offset: 0, color: '#6fed5c' }])
		],
		[
			0.40, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 1, color: '#d8d563' }, { offset: 0, color: '#b2f268' }])
		],
		[
			0.60, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 1, color: '#edb168' }, { offset: 0, color: '#d8d563' }])
		],
		[
			0.80, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 1, color: '#eb7742' }, { offset: 0, color: '#edb168' }])
		],
		[
			1, new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 1, color: '#e8412e' }, { offset: 0, color: '#eb7742' }])
		]
	]
}
