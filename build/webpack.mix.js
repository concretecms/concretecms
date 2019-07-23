let mix = require('laravel-mix');

mix.webpackConfig({
    externals: {
        dropzone: 'Dropzone',
        vue: 'Vue'
    }
});

// Set our public path
mix.setPublicPath('../concrete/js');

// Build our components into a bundle
mix.js('components/avatar.js', 'components/avatar.bundle.js')
if (mix.inProduction()) {
    mix.copy('node_modules/vue/dist/vue.min.js', '../concrete/js/vue.js');
} else {
    mix.copy('node_modules/vue/dist/vue.js', '../concrete/js/vue.js');
}

// Turn off notifications
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    })
