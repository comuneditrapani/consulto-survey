const path = require("path");

module.exports = {
  mode: "development",

  entry: "./src/admin/index.js",

  output: {
    path: path.resolve(__dirname, "build"),
    filename: "admin.js"
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
