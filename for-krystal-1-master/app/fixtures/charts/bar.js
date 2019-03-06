export let initial = {
	tooltip: {
		trigger: 'axis',
		axisPointer: {
			type: 'shadow'
		}
	},
	xAxis: {
		data: []
	},
	yAxis: {
		axisLabel: {show: false}
	},
	series: [{
		name: 'Data',
		type: 'bar',
		data: []
	}]
}

var xAxisData = []
var data1 = []
var data2 = []
var data3 = []
for (let i = 0; i < 7; i++) {
	xAxisData.push('date' + i)
	data1.push(Math.floor((Math.random() * 20) + 1) + i)
	data2.push(Math.floor((Math.random() * 20) + 1) + i)
	data3.push(Math.floor((Math.random() * 20) + 1) + i)
}

export let async = {
	xAxis: {
		data: xAxisData,
		type: 'category',
		silent: false,
		splitLine: {
			show: false
		}
	},
	yAxis: {
		show: false
	},
	legend: {
		show: true,
		data: ['Hace 2 semanas', 'Semana pasada', 'Semana actual'],
		align: 'left'
	},
	series: [
		{
			name: 'Hace 2 semanas',
			type: 'bar',
			data: data1,
			animationDelay: function (idx) {
				return idx * 10
			}
		},
		{
			name: 'Semana pasada',
			type: 'bar',
			data: data2,
			animationDelay: function (idx) {
				return idx * 10 + 100
			}
		},
		{
			name: 'Semana actual',
			type: 'bar',
			data: data3,
			animationDelay: function (idx) {
				return idx * 10 + 100
			}
		}
	]
}
