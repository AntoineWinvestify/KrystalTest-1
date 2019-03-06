export default function ({ app, store, router }) {
	app.router.beforeEach((to, from, next) => {
		store.commit('SET_BACK_LINK', from.path)
		next()
	})
}
