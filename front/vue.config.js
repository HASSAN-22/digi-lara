const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  chainWebpack: config => {
    config.performance.maxEntrypointSize(17825792).maxAssetSize(17825792);
    config.plugins.delete('pwa');
    config.plugins.delete('workbox');

  },
})
