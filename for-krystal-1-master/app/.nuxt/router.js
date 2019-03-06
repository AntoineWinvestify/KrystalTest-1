import Vue from 'vue'
import Router from 'vue-router'
import { interopDefault } from './utils'

const _293bae40 = () => interopDefault(import('..\\pages\\index.vue' /* webpackChunkName: "pages_index" */))
const _6762fd32 = () => interopDefault(import('..\\pages\\_lang\\index.vue' /* webpackChunkName: "pages__lang_index" */))
const _629cc8b9 = () => interopDefault(import('..\\pages\\_lang\\dashboard\\index.vue' /* webpackChunkName: "pages__lang_dashboard_index" */))
const _aa2236d8 = () => interopDefault(import('..\\pages\\_lang\\lending-registration\\index.vue' /* webpackChunkName: "pages__lang_lending-registration_index" */))
const _48b5ba16 = () => interopDefault(import('..\\pages\\_lang\\link.vue' /* webpackChunkName: "pages__lang_link" */))
const _cec9583c = () => interopDefault(import('..\\pages\\_lang\\marketplace\\index.vue' /* webpackChunkName: "pages__lang_marketplace_index" */))
const _27a85904 = () => interopDefault(import('..\\pages\\_lang\\profile.vue' /* webpackChunkName: "pages__lang_profile" */))
const _18cf95f5 = () => interopDefault(import('..\\pages\\_lang\\template.vue' /* webpackChunkName: "pages__lang_template" */))
const _481be072 = () => interopDefault(import('..\\pages\\_lang\\dashboard\\platforms.vue' /* webpackChunkName: "pages__lang_dashboard_platforms" */))
const _b77fc12a = () => interopDefault(import('..\\pages\\_lang\\lending-registration\\profile.vue' /* webpackChunkName: "pages__lang_lending-registration_profile" */))
const _d3f0b53e = () => interopDefault(import('..\\pages\\_lang\\dashboard\\_id.vue' /* webpackChunkName: "pages__lang_dashboard__id" */))
const _7950e6ca = () => interopDefault(import('..\\pages\\_lang\\marketplace\\_id.vue' /* webpackChunkName: "pages__lang_marketplace__id" */))

Vue.use(Router)

if (process.client) {
  window.history.scrollRestoration = 'manual'
}
const scrollBehavior = function (to, from, savedPosition) {
  // if the returned position is falsy or an empty object,
  // will retain current scroll position.
  let position = false

  // if no children detected and scrollToTop is not explicitly disabled
  if (
    to.matched.length < 2 &&
    to.matched.every(r => r.components.default.options.scrollToTop !== false)
  ) {
    // scroll to the top of the page
    position = { x: 0, y: 0 }
  } else if (to.matched.some(r => r.components.default.options.scrollToTop)) {
    // if one of the children has scrollToTop option set to true
    position = { x: 0, y: 0 }
  }

  // savedPosition is only available for popstate navigations (back button)
  if (savedPosition) {
    position = savedPosition
  }

  return new Promise((resolve) => {
    // wait for the out transition to complete (if necessary)
    window.$nuxt.$once('triggerScroll', () => {
      // coords will be used if no selector is provided,
      // or if the selector didn't match any element.
      if (to.hash) {
        let hash = to.hash
        // CSS.escape() is not supported with IE and Edge.
        if (typeof window.CSS !== 'undefined' && typeof window.CSS.escape !== 'undefined') {
          hash = '#' + window.CSS.escape(hash.substr(1))
        }
        try {
          if (document.querySelector(hash)) {
            // scroll to anchor by returning the selector
            position = { selector: hash }
          }
        } catch (e) {
          console.warn('Failed to save scroll position. Please add CSS.escape() polyfill (https://github.com/mathiasbynens/CSS.escape).')
        }
      }
      resolve(position)
    })
  })
}

export function createRouter() {
  return new Router({
    mode: 'history',
    base: '/',
    linkActiveClass: 'nuxt-link-active',
    linkExactActiveClass: 'nuxt-link-exact-active',
    scrollBehavior,

    routes: [{
      path: "/",
      component: _293bae40,
      name: "index"
    }, {
      path: "/:lang",
      component: _6762fd32,
      name: "lang"
    }, {
      path: "/:lang/dashboard",
      component: _629cc8b9,
      name: "lang-dashboard"
    }, {
      path: "/:lang/lending-registration",
      component: _aa2236d8,
      name: "lang-lending-registration"
    }, {
      path: "/:lang/link",
      component: _48b5ba16,
      name: "lang-link"
    }, {
      path: "/:lang/marketplace",
      component: _cec9583c,
      name: "lang-marketplace"
    }, {
      path: "/:lang/profile",
      component: _27a85904,
      name: "lang-profile"
    }, {
      path: "/:lang/template",
      component: _18cf95f5,
      name: "lang-template"
    }, {
      path: "/:lang/dashboard/platforms",
      component: _481be072,
      name: "lang-dashboard-platforms"
    }, {
      path: "/:lang/lending-registration/profile",
      component: _b77fc12a,
      name: "lang-lending-registration-profile"
    }, {
      path: "/:lang/dashboard/:id",
      component: _d3f0b53e,
      name: "lang-dashboard-id"
    }, {
      path: "/:lang/marketplace/:id",
      component: _7950e6ca,
      name: "lang-marketplace-id"
    }],

    fallback: false
  })
}
