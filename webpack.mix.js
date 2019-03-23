const path = require('path')
const mix = require('laravel-mix')
// const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer')

mix.config.vue.esModule = true

mix
  .js([
    'resources/assets/js/app.js',
    'node_modules/vue-multiselect/dist/vue-multiselect.min.js',
    'node_modules/bootstrap4-toggle/js/bootstrap4-toggle.js',
    'node_modules/bootstrap/dist/js/bootstrap.js',
    'node_modules/axios/dist/axios.js'
  ], 'public/js/app.js')
  .sass('resources/assets/sass/app.scss', 'public/css')
  .sourceMaps()
  //.disableNotifications()
mix.styles(
  [
    'node_modules/vue-multiselect/dist/vue-multiselect.min.css',
    'node_modules/bootstrap4-toggle/css/bootstrap4-toggle.css'
  ], 'public/css/all.css');
mix.copy('resources/assets/img', 'public/img');

if (mix.inProduction()) {
  mix.version()

  mix.extract([
    'vue',
    'vform',
    'axios',
    'vuex',
    'jquery',
    'popper.js',
    'vue-i18n',
    'vue-meta',
    'js-cookie',
    'bootstrap',
    'vue-router',
    'sweetalert2',
    'vuex-router-sync',
    '@fortawesome/fontawesome',
    '@fortawesome/vue-fontawesome'
  ])
}

mix.webpackConfig({
  plugins: [
    // new BundleAnalyzerPlugin()
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      '~': path.join(__dirname, './resources/assets/js'),
      jquery: 'jquery/src/jquery'
    }
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: mix.config.hmr ? '//localhost:8080' : '/'
  }
})
