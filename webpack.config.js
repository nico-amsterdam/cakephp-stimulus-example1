const webpack = require('webpack');
const CompressionPlugin = require('compression-webpack-plugin');
const path = require("path")

module.exports = {
  entry: {
    stimulus_v1_0: "./stimulus/index.js"
  },

  plugins: [
    new CompressionPlugin({
      test: /stimulus.*\.js(\.map)?$/i
    })
  ],

  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "webroot/js")
  },

  mode: "production",
  devtool: "source-map",

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: [
          /node_modules/
        ],
        use: [
          { loader: "babel-loader" }
        ]
      }
    ]
  }
}
