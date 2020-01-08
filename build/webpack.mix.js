let mix = require('laravel-mix');

mix.webpackConfig({
    externals: {
        dropzone: 'Dropzone',
        jquery: 'jQuery'
    }
});

// Set our public path
mix.setPublicPath('../concrete');

// Build our components into a bundle
mix.js('components/avatar.js', 'js/components/avatar.bundle.js');
if (mix.inProduction()) {
    mix.copy('node_modules/vue/dist/vue.min.js', '../concrete/js/vue.js');
} else {
    mix.copy('node_modules/vue/dist/vue.js', '../concrete/js/vue.js');
}

// Copy already minified assets
mix.copy('node_modules/jquery/dist/jquery.min.js', '../concrete/js/jquery.js');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', '../concrete/css/webfonts');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.css', '../concrete/css/fontawesome/all.css');
mix.copy('node_modules/ckeditor4', '../concrete/js/ckeditor');

//delete these?!
// mix.copy('node_modules/bootstrap/dist/js/bootstrap.bundle.js', '../concrete/js/bootstrap.js');

// Build themes
mix
    .sass('themes/concrete/scss/main.scss', 'themes/concrete')
    .sass('themes/dashboard/scss/main.scss', 'themes/dashboard')
    .sass('themes/elemental/scss/main.scss', 'themes/elemental/css')
    .sass('foundation/scss/cms/cms.scss', 'css/cms.css')
    .sass('foundation/scss/ckeditor/concrete.scss', 'css/ckeditor/concrete.css')
    .js('themes/elemental/js/main.js', 'themes/elemental')
    .js('themes/dashboard/js/main.js', 'themes/dashboard')
    .js('themes/concrete/js/main.js', 'themes/concrete')
    .js('foundation/js/cms.js', 'js/cms.js')
    .js('foundation/js/ckeditor/concrete.js', 'js/ckeditor/concrete.js');


// Turn off notifications
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    })
