function parseToQuery (options) {
	let results = ''
	Object.keys(options).map((key) => {
		if (options[key]) {
			results += `${key}=${options[key]}&`
		}
	})
	return results
}

export {
	parseToQuery
}
