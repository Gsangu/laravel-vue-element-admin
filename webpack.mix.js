let mix = require('laravel-mix');

const { VueLoaderPlugin } = require('vue-loader')
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Make Laravel Mix ignore .svgs
Mix.listen('configReady', function (config) {
  const rules = config.module.rules;
  const targetRegex = /(\.(png|jpe?g|gif)$|^((?!font).)*\.svg$)/;

  for (let rule of rules) {
    if (rule.test.toString() == targetRegex.toString()) {
      rule.exclude = path.resolve(__dirname, 'resources/assets/js/icons');
      break;
    }
  }
});




mix.js('resources/assets/js/app.js', 'public/js')
   .extract(['vue','axios'])
   .webpackConfig({
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
             '@': path.resolve(__dirname, 'resources/assets/js'),
        },
    },
    output: {
        publicPath: '/',
        filename: '[name].js',
        chunkFilename: 'js/[name].[chunkhash].chunk.js'
    },
    plugins: [
        new VueLoaderPlugin()
      ],
      module: {
        rules: [
          {
          test: /\.svg$/,
          loader: 'svg-sprite-loader',
          include: [path.resolve(__dirname, 'resources/assets/js')],
          options: {
          symbolId: 'icon-[name]'
            }
          }
        ],
      }
 });
