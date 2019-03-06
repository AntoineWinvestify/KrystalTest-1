import Vue from 'vue'

Vue.filter('currency', function (value, option) {
	if (!value) return ''

	const number = Number(value).toLocaleString('de-DE')

	switch (option) {
	case 'EUR': // => ["EUR", "€"],
		return `${number} €`

	case 'GBP': // => ["GBP", "£"],
		return `${number} £`

	case 'USD': // => ["USD", "$"],
		return `${number} $`

	case 'ARS': // => ["ARS", "$"],
		return `${number} $`

	case 'AUD': // => ["AUD", "$"],
		return `${number} $`

	case 'NZD': // => ["NZD", "$"],
		return `${number} $`

	case 'BYN': // => ["BYN", "BR"],
		return `${number} BR`

	case 'BGN': // => ["BGN", "лв"],
		return `${number} лв`

	case 'CZK': // => ["CZK", "Kč"],
		return `${number} Kč`

	case 'DKK': // => ["DKK", "Kr"],
		return `${number} Kr`

	case 'CHF': // => ["CHF", "Fr"],
		return `${number} Fr`

	case 'MXN': // => ["MXN", "$"],
		return `${number} $`

	case 'RUB': // => ["RUB", "₽"],
		return `${number} ₽`

	case 'GEL': // => [“GEL”, “₾”],
		return `${number} ₾`

	case 'KZT': // => [“KZT”,”₸”],
		return `${number} ₸`

	case 'PLN': // => [“PLN”, “zł”],
		return `${number} zł`

	case 'SEK': // => [“SEK” “kr”]
		return `${number} kr`

	default:
		return `${number} €`
	}
})
