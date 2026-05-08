const path = require("path");

module.exports = {
  mode: "development",

  entry: {
    admin: "./src/admin/index.js",
    i18n: "./src/i18n/index.js",
  },

  output: {
    path: path.resolve(__dirname, "build"),
    filename: "[name].js"
  },

  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: "babel-loader"
      }
    ]
  },

  resolve: {
    extensions: [".js", ".jsx"]
  },

  devtool: "source-map"
};
