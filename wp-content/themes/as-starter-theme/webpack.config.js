const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const path = require('path');
const dotenv = require('dotenv');

dotenv.config();

module.exports = {
  entry: {
    main: ['./assets/js/src/main.js', './assets/css/src/main.scss'],
  },
  devServer: {
    writeToDisk: true,
    contentBase: path.join(__dirname, 'dist'),
    disableHostCheck: true,
    hot: true,
    host: 'localhost',
    port: 3000,
    proxy: {
      '*': {
        target: process.env.PROXY,
        changeOrigin: true,
        secure: false,
      },
    },
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              [
                '@babel/preset-env',
                {
                  debug: true,
                  useBuiltIns: 'usage',
                  corejs: 3,
                },
              ],
            ],
          },
        },
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          // Extract styles to external file
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '',
            },
          },
          // Translates CSS into CommonJS
          'css-loader',
          'resolve-url-loader',
          // Compiles Sass to CSS
          'sass-loader',
        ],
      },
      {
        test: /\.(png|svg|jpg|gif)$/,
        use: [
          'file-loader',
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: '[name].css',
      chunkFilename: '[id].css',
    }),
    new BrowserSyncPlugin(
      // BrowserSync options
      {
        // browse to http://localhost:3000/ during development
        host: 'localhost',
        port: 3100,
        // proxy the Webpack Dev Server endpoint
        // (which should be serving on http://localhost:3100/)
        // through BrowserSync
        proxy: 'localhost:3000',
        files: ['./dist/*.*', '**/*.php', '**/*.twig'],
        open: true,
      },
      // plugin options
      {
        // prevent BrowserSync from reloading the page
        // and let Webpack Dev Server take care of this
        reload: false,
        injectCss: true,
      },
    ),
  ],
  devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'eval-source-map',
};
