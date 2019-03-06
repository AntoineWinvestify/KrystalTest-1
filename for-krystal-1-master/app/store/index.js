import Vuex from 'vuex'

const store = () => new Vuex.Store({
	state: {
		authUser: null,
		locales: ['es', 'en'],
		locale: 'en',
		loading: false,
		buttonBack: false,
		backLink: '',
		advertisingModal: true,
		landingDrawer: false
	},
	mutations: {
		SET_USER: function (state, user) {
			state.authUser = user
		},
		SET_LANG: function (state, locale) {
			if (state.locales.indexOf(locale) !== -1) {
				state.locale = locale
			}
		},
		TOGGLE_LOADING: function (state, loading) {
			state.loading = loading
		},
		SET_BUTTON_BACK: function (state, value) {
			state.buttonBack = value
		},
		TOGGLE_LANDING_DRAWER: function (state, value) {
			state.landingDrawer = value
		},
		TOGGLE_MODAL_ADVERTISING: function (state, value) {
			state.advertisingModal = value
		},
		SET_BACK_LINK: function (state, link) {
			state.backLink = link
		}
	},
	actions: {
		nuxtServerInit ({ commit }, { route, req }) {
			if (req.session && req.session.user) {
				// TODO Verificar si el lang del user es el mismo de los params que llegan
				commit('SET_USER', req.session.user)
			}
		},
		initLoading ({ commit }, data) {
			data.$nextTick(() => {
				data.$nuxt.$loading.start()
				commit('TOGGLE_LOADING', true)
			})
		},
		finishLoading ({ commit }, data) {
			data.$nextTick(() => {
				data.$nuxt.$loading.finish()
				commit('TOGGLE_LOADING', false)
			})
		},
		errorLoading ({ commit }, data) {
			data.$nextTick(() => {
				data.$nuxt.$loading.fail()

				setTimeout(() => {
					data.$nuxt.$loading.finish()
					commit('TOGGLE_LOADING', false)
				}, 1000)
			})
		},
		landingDrawer ({ commit }, data) {
			commit('TOGGLE_LANDING_DRAWER', data)
		},
		advertisingModal ({ commit }, data) {
			commit('TOGGLE_MODAL_ADVERTISING', data)
		}
	},
	getters: {
		authUser: state => state.authUser,
		back: state => state.buttonBack,
		backLink: state => state.backLink,
		advertisingModal: state => state.advertisingModal,
		landingDrawer: state => state.landingDrawer,
		locale: state => state.locale
	}
})

export default store
