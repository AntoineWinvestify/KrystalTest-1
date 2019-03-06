const paymentDelay = {
	'dataset1': {
		'display_name': 'PaymentDelay',
		'data': [
			{
				'range_display_name': '1-7 days',
				'percent': 0.55
			},
			{
				'range_display_name': '8-30 days',
				'percent': 0.12
			},
			{
				'range_display_name': '31-60 days',
				'percent': 20.93
			},
			{
				'range_display_name': '61-90 days',
				'percent': 0.00
			},
			{
				'range_display_name': '> 91 days',
				'percent': 0.01
			}
		]
	}
}

const current = {
	'dataset_1': {
		'display_name': 'Daily Current',
		'data': [
			{
				'max_value': 100,
				'percent': 73.5
			}
		]
	}
}

const exposure = {
	'dataset_1': {
		'display_name': 'Platform Exposure',
		'data': [
			{
				'max_value': 100,
				'percent': 22.55
			}
		]
	}
}

export {
	paymentDelay,
	current,
	exposure
}
