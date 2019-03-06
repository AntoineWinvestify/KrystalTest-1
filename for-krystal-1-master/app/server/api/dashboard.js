import { Router } from 'express'
import { general } from '../fixtures/globalDashboard'
import { current, paymentDelay, exposure } from '../fixtures/charts'
/* import request from 'request-promise'
import { server } from '../server.config'
import { parseToQuery } from '../lib/utils' */

const router = Router()

router.get('/', (req, res, next) => {
	res.json(general)
})

router.get('/chart/payment', (req, res, next) => {
	res.json(paymentDelay)
})

router.get('/chart/current', (req, res, next) => {
	res.json(current)
})

router.get('/chart/exposure', (req, res, next) => {
	res.json(exposure)
})

export default router
