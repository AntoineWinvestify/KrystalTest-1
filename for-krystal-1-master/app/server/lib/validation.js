import Joi from 'joi'

const login = {
	body: {
		username: Joi.string().email().required(),
		password: Joi.string().required()
	}
}

const linkAccountPreCheck = {
	body: {
		password: Joi.string().required(),
		username: Joi.string().required(),
		companyId: Joi.number().required(),
	}
}

const linkAccount = {
	body: {
		password: Joi.string().required(),
		username: Joi.string().required(),
		companyId: Joi.number().required(),
		linkedAccount: Joi.object().not().empty()
	}
}	

export {
	linkAccount,
	linkAccountPreCheck,
	login
}
