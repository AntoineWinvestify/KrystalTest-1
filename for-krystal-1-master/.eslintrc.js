module.exports = {
	root: true,
	parser: 'babel-eslint',
	env: {
		browser: true,
		node: true
	},
	extends: 'standard',
	// required to lint *.vue files
	plugins: [
		'html'
	],
	// add your custom rules here
	rules: {
		// allow paren-less arrow functions
		'arrow-parens': 0,
		// allow async-await
		'generator-star-spacing': 0,
		// allow debugger during development
		'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
		// warnings console.logs etc...
		'no-console': 1,
		// linebreaks to be 'LF'
		'linebreak-style': ['error', 'unix'],
		// This rule disallows mixed spaces and tabs for indentation.
		'no-mixed-spaces-and-tabs': 'error',
		// Only tabs ident
		'indent': [2, 'tab'],
		'no-tabs': 0
	},
	globals: {}
}