const nodeExternals = require('webpack-node-externals')

module.exports = {
  /*
  ** Headers of the page
  */
  head: {
    title: 'starter',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'Nuxt.js project' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700|Material+Icons' }
    ]
  },
  loading: { color: '#73DC50', failedColor: '#F90000' },
  css: [
    { src: '~assets/css/style.styl', lang: 'styl' }
  ],
  plugins: [
    { src: '~plugins/i18n.js', injectAs: 'i18n' },
    { src: '~plugins/vuetify.js' },
    { src: '~plugins/toast.js', ssr: false },
    // { src: '~plugins/currency.js', ssr: false },
    { src: '~filters/currency.js', ssr: false }
  ],
  build: {
    vendor: ['axios', 'vue-i18n'],
    /*
    ** Run ESLINT on save
    */
   transpile: [
    /^vue-echarts/,
    /\/node_modules\/resize-detector\//
   ],
    extend (config, ctx) {
      if (ctx.isDev && process.isClient) {
        config.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules)/
        })
      }
      if (ctx.isServer) {
        config.externals = [
          nodeExternals({
            // default value for `whitelist` is
            // [/es6-promise|\.(?!(?:js|json)$).{1,5}$/i]
            whitelist: [/es6-promise|\.(?!(?:js|json)$).{1,5}$/i, /^vue-echarts/]
          })
        ]
      }
    }
  }
}
