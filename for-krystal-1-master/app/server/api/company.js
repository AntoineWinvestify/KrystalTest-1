import { Router } from 'express'
import { companies, marketplace, getCompany } from '../fixtures/companies'
/* import request from 'request-promise'
import { server } from '../server.config'
import { parseToQuery } from '../lib/utils' */

const router = Router()

router.get('/lending', (req, res, next) => {
	res.json(companies)
})

router.get('/marketplace', (req, res, next) => {
	res.json(marketplace)
})

router.get('/marketplace/:id', async (req, res, next) => {
	res.json(getCompany(req.params.id))
})

export default router
