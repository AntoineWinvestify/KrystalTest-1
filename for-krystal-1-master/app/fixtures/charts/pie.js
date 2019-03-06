export const pie = {
	tooltip: {
		trigger: 'item',
		formatter: '{a} <br/>{b}: {d}%'
	},
	legend: {
		orient: 'horizontal',
		x: 'center',
		icon: 'circle',
		data: []
	},
	series: [
		{
			name: 'Payment Delay',
			type: 'pie',
			radius: ['40%', '70%'],
			avoidLabelOverlap: false,
			label: {
				normal: {
					show: false,
					position: 'center'
				}
			},
			labelLine: {
				normal: {
					show: false
				}
			},
			data: []
		}
	]
}

export const dataPie = ({ data, display_name }) => {
	return {
		legend: {
			// data: ['1-7 days', '8-30 days', '31-60 days', '61-90 days']
			data: getLegend(data)
		},
		series: [
			{
				name: display_name,
				data: getSeries(data)
			}
		]
	}
}

function getLegend (data) {
	return data.map(item => {
		return item.range_display_name
	})
}

function getSeries (data) {
	return data.map(item => {
		return {
			value: item.percent,
			name: item.range_display_name
		}
	})
/* {value: 20, name: '1-7 days'},
{value: 4, name: '8-30 days'},
{value: 16, name: '31-60 days'},
{value: 60, name: '61-90 days'} */
}
