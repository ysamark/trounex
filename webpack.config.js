var path = require('path');
var webpack = require('webpack');

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
  entry: './assets/javascript/application.js',
  output: {
    path: path.resolve(__dirname, 'assets', 'javascript', 'build'),
    filename: 'application.bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
            plugins: [
              '@babel/plugin-proposal-object-rest-spread',
              '@babel/plugin-proposal-class-properties'
            ]
          }
        }
      }
    ]
  },
  stats: {
    colors: true
  },

  devtool: false,
  plugins: [
    new webpack.SourceMapDevToolPlugin ({
      filename: '../../../public/assets/js/application.js.map',
      append: '\n//# sourceMappingURL=/app/assets/js/application.js.map'
    })
  ],

  resolve: {
    alias: {
      '~': path.resolve (__dirname, 'assets', 'javascript'),
      '@components': path.resolve (__dirname, 'assets', 'javascript', 'components')
    }
  },

  optimization: {
    minimizer: [
      new UglifyJsPlugin ()
    ]
  }
};
