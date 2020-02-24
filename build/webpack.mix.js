let mix = require('laravel-mix');

mix.webpackConfig({
    externals: {
        jquery: 'jQuery'
    }
});

// Set our public path
mix.setPublicPath('../concrete');

// Copy already minified assets
mix.copy('node_modules/jquery/dist/jquery.min.js', '../concrete/js/jquery.js');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', '../concrete/css/webfonts');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.css', '../concrete/css/fontawesome/all.css');
mix.copy('node_modules/ckeditor4', '../concrete/js/ckeditor');

// CKEditor
mix
    .sass('node_modules/concretecms-bedrock/assets/ckeditor/scss/concrete.scss', 'css/ckeditor/concrete.css')
    .js('node_modules/concretecms-bedrock/assets/ckeditor/js/concrete.js', 'js/ckeditor/concrete.js');

// The CMS entry point
mix
    .sass('assets/cms.scss', 'css/cms.css')
    .js('assets/cms.js', 'js/cms.js');

// Concrete Theme
mix
    .sass('assets/themes/concrete/scss/main.scss', 'themes/concrete')
    .js('assets/themes/concrete/js/main.js', 'themes/concrete');

// Dashboard Theme
mix
    .sass('assets/themes/dashboard/scss/main.scss', 'themes/dashboard')
    .js('assets/themes/dashboard/js/main.js', 'themes/dashboard');

// Elemental Theme
mix
    .sass('assets/themes/elemental/scss/main.scss', 'themes/elemental')
    .js('assets/themes/elemental/js/main.js', 'themes/elemental');


// Turn off notifications
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    })
