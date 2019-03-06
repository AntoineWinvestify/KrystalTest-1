import moment from 'moment'

export default () => {
	let data = []
	for (let i = 0; i < 100; i++) {
		data.push({
			id: `MC29${i}`,
			date: moment(new Date()).format('L'),
			investment: Math.random(),
			interes: '4.55%',
			progress: '47.33%',
			outstanding: Math.random(),
			paymentDate: moment(new Date()).format('L'),
			status: `${i} days`
		})
	}

	return data
}
