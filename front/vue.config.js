const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  chainWebpack: config => {
    config.performance.maxEntrypointSize(17825792).maxAssetSize(17825792);
    // config.plugins.delete('pwa');
    // config.plugins.delete('workbox');

  },
  pwa: {
    name: "نام کامل فروشگاه",
    short_name  : 'نام کوتاه',
    description: 'توضیحات | اختیاری',
    themeColor: '#ff0454',
    msTileColor: '#000000',
    start_url: ".",
    display: "standalone",
    background_color: "#000000",
    appleMobileWebAppCapable: 'yes',
    appleMobileWebAppStatusBarStyle: 'black',

  }
})
