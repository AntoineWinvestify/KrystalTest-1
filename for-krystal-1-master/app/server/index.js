import express from 'express'
import { Nuxt, Builder } from 'nuxt'
import { server as serverConfig, cookie as cookieConfig } from './server.config'
import cookieParser from 'cookie-parser'
import bodyParser from 'body-parser'
import validate from 'express-validation'
import cookieSession from 'cookie-session'

import api from './api'

const app = express()
const host = serverConfig.host
const port = serverConfig.port

app.set('port', port)

app.use(bodyParser.json({limit: '50mb'}))
app.use(bodyParser.urlencoded({limit: '50mb', extended: true}))
app.use(cookieParser())

app.use(cookieSession({
	name: 'wvt',
	keys: cookieConfig.session.keys
}))

app.use(express.static('../assets'))
// Import API Routes
app.use('/api', api)

// catch validation errors
app.use((err, req, res, next) => {
	if (err.statusCode === 401) {
		delete req.session.user
	}
	if (err instanceof validate.ValidationError) {
		// const error = new Error(err.errors, err.status, true)
		res.status(err.status).json(err.errors)
	}
	return next(err)
})

// Import and Set Nuxt.js options
let config = require('../nuxt.config.js')
config.dev = !(process.env.NODE_ENV === 'production')

// Init Nuxt.js
const nuxt = new Nuxt(config)

// Build only in dev mode
if (config.dev) {
	const builder = new Builder(nuxt)
	builder.build()
}

// Give nuxt middleware to express
app.use(nuxt.render)

// Listen the server
app.listen(port, host)
console.log('Server listening on: ' + host + ':' + port) // eslint-disable-line no-console
