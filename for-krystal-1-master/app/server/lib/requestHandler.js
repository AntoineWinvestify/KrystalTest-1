let token = req.session.user.access_token

let options = {
	method: 'GET',
	url: `${server.urlApi}/api/****`,
	headers: {
		'Authorization': `Bearer ${token}`
	}
}

request(options)
	.then((data) => {
		try {
			res.status(200).json(JSON.parse(data))
		} catch (error) {
			res.status(200).json(data)
		}
	})
	.catch((err) => {
		next(err)
	})
