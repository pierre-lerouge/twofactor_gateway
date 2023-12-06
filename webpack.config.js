const path = require('path')
const { merge } = require('webpack-merge');
const webpackConfig = require('@nextcloud/webpack-vue-config')

module.exports = merge([
  webpackConfig,
  {
    entry : {
      'main-settings': path.join(__dirname, 'src','main.js'),
      'main-login-setup': path.join(__dirname, 'src','main-login-vue.js'),
      },
  }
])