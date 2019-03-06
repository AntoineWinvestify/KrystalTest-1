import { Router } from 'express'
// import session from '../lib/session' Middleware to check if session exists

import companies from './company'
import dashboard from './dashboard'
import account from './account'
import polling from './polling'

const router = Router()

router.use('/companies', companies)
router.use('/dashboard', dashboard)
router.use('/link', account)
router.use('/polling', polling)

export default router
